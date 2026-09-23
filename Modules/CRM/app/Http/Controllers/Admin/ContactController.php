<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Contact\BulkDeleteContactsAction;
use Modules\CRM\Actions\Contact\CreateContactAction;
use Modules\CRM\Actions\Contact\DeleteContactAction;
use Modules\CRM\Actions\Contact\ListContactsAction;
use Modules\CRM\Actions\Contact\UpdateContactAction;
use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Exports\ContactExport;
use Modules\CRM\Exports\ContactImportSampleExport;
use Modules\CRM\Http\Requests\ContactIndexRequest;
use Modules\CRM\Http\Requests\StoreContactRequest;
use Modules\CRM\Http\Requests\UpdateContactRequest;
use Modules\CRM\Imports\ContactImport;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;

class ContactController extends Controller
{
    public function __construct(
        private readonly ListContactsAction $listContactsAction,
        private readonly CreateContactAction $createContactAction,
        private readonly UpdateContactAction $updateContactAction,
        private readonly DeleteContactAction $deleteContactAction,
        private readonly BulkDeleteContactsAction $bulkDeleteContactsAction,
    ) {
        $this->authorizeResource(Contact::class, 'contact');
        $this->setActive('crm');
        $this->setActive('contacts');
    }

    public function index(ContactIndexRequest $request)
    {
        $model = $this->listContactsAction->execute($request->validated());

        return view('crm::admin.contact.index', array_merge(compact('model'), $this->formData()));
    }

    public function create()
    {
        return view('crm::admin.contact.create', $this->formData());
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $data = ContactData::fromRequest($request->validated());
        $this->createContactAction->execute($data);

        return redirect()->route('admin.contacts.index');
    }

    public function show(Contact $contact)
    {
        $contact->loadMissing([
            'company:id,name,email,phone',
            'crmActivities' => fn ($q) => $q->with('user:id,name')->latest()->limit(50),
            'crmAuditLogs' => fn ($q) => $q->with('user:id,name')->latest('created_at')->limit(50),
        ]);

        return view('crm::admin.contact.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        return view('crm::admin.contact.edit', array_merge(['contact' => $contact], $this->formData()));
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $data = ContactData::fromRequest($request->validated());
        $this->updateContactAction->execute($contact, $data);

        return redirect()->route('admin.contacts.index');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->deleteContactAction->execute($contact);

        return redirect()->route('admin.contacts.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->bulkDeleteContactsAction->execute($request->input('ids', []));

        return back();
    }

    public function export()
    {
        return Excel::download(new ContactExport, 'contacts_'.date('Y-m-d_His').'.xlsx');
    }

    public function downloadSample()
    {
        return Excel::download(new ContactImportSampleExport, 'contacts_import_sample.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'extensions:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(app(ContactImport::class), $request->file('file'));
            session()->flushMessage(true, __('crm::contact.import.success'));
        } catch (ValidationException $e) {
            $messages = collect($e->failures())
                ->flatMap(fn ($failure) => $failure->errors())
                ->unique()
                ->implode(' ');
            session()->flushMessage(false, __('crm::contact.import.error', ['message' => $messages]));
        } catch (\Exception $e) {
            session()->flushMessage(false, __('crm::contact.import.error', ['message' => $e->getMessage()]));
        }

        return redirect()->back();
    }

    private function formData(): array
    {
        return [
            'companies' => Company::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'customers' => User::query()
                ->customers()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get(),
        ];
    }
}
