<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Exports\ContactFormExport;
use Modules\CRM\Http\Requests\StoreContactFormRequest;
use Modules\CRM\Http\Requests\UpdateContactFormRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\ContactForm;

class ContactFormController extends Controller
{
    public function __construct()
    {
        $this->setActive('crm');
        $this->setActive('contact_forms');
    }

    public function index(): View
    {
        $model = ContactForm::query()
            ->with('company:id,name')
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
        ContactForm::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
            'blocked' => false,
        ]);

        session()->flushMessage(true);

        return redirect()->route('admin.contact_forms.index');
    }

    public function edit(ContactForm $contactForm): View
    {
        return view('crm::admin.contact_form.edit', array_merge(
            ['contact' => $contactForm],
            $this->formData()
        ));
    }

    public function update(UpdateContactFormRequest $request, ContactForm $contactForm): RedirectResponse
    {
        $contactForm->update($request->validated());

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

    private function formData(): array
    {
        return [
            'companies' => Company::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
        ];
    }
}
