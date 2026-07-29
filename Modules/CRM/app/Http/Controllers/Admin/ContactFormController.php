<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Contact\ConvertContactFormToContactAction;
use Modules\CRM\Actions\Contact\ConvertContactFormToLeadAction;
use Modules\CRM\Exports\ContactFormExport;
use Modules\CRM\Http\Requests\StoreContactFormRequest;
use Modules\CRM\Http\Requests\UpdateContactFormRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\ContactForm;
use Modules\Services\Models\Service;

class ContactFormController extends Controller
{
    public function __construct(
        private readonly ConvertContactFormToLeadAction $convertToLeadAction,
        private readonly ConvertContactFormToContactAction $convertToContactAction,
    ) {
        $this->setActive('crm');
        $this->setActive('contact_forms');
    }

    public function index(): View
    {
        $model = ContactForm::query()
            ->with(['company:id,name', 'service:id,title', 'services:services.id,title', 'lead:id,name', 'crmContact:id,name'])
            ->latest()
            ->paginate(config('core.page_size'));

        return view('crm::admin.contact_form.index', array_merge(compact('model'), $this->formData()));
    }

    public function create(): View
    {
        return view('crm::admin.contact_form.create', $this->formData());
    }

    public function store(StoreContactFormRequest $request): RedirectResponse
    {
        $contactForm = ContactForm::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
            'blocked' => false,
        ]);

        $contactForm->services()->sync($this->serviceIds($request->input('service_ids', [])));

        session()->flushMessage(true);

        return redirect()->route('admin.contact_forms.index');
    }

    public function edit(ContactForm $contactForm): View
    {
        $contactForm->loadMissing('services:services.id');

        return view('crm::admin.contact_form.edit', array_merge(
            ['contact' => $contactForm],
            $this->formData()
        ));
    }

    public function update(UpdateContactFormRequest $request, ContactForm $contactForm): RedirectResponse
    {
        $contactForm->update($request->validated());
        $contactForm->services()->sync($this->serviceIds($request->input('service_ids', [])));

        session()->flushMessage(true);

        return redirect()->route('admin.contact_forms.index');
    }

    public function export()
    {
        return Excel::download(new ContactFormExport, 'contacts_'.date('Y-m-d_His').'.xlsx');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        ContactForm::destroy($request->ids);
        session()->flushMessage(true);

        return redirect()->back();
    }

    public function convertToLead(ContactForm $contactForm): RedirectResponse
    {
        $lead = $this->convertToLeadAction->execute($contactForm);

        return redirect()->route('admin.leads.show', $lead);
    }

    public function convertToContact(ContactForm $contactForm): RedirectResponse
    {
        $contact = $this->convertToContactAction->execute($contactForm);

        return redirect()->route('admin.contacts.show', $contact);
    }

    private function serviceIds(array $serviceIds): array
    {
        return collect($serviceIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function formData(): array
    {
        return [
            'companies' => Company::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'services' => Service::query()
                ->published()
                ->select(['id', 'title'])
                ->orderBy('title')
                ->get(),
        ];
    }
}
