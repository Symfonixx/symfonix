<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\Support\app\Exports\ContactFormExport;
use Modules\Support\Models\ContactForm;

class ContactFormController extends Controller
{
    public function __construct()
    {
        $this->setActive('support');
        $this->setActive('contact_forms');
    }

    public function index()
    {
        $model = ContactForm::latest()->paginate(config('core.page_size'));

        return view('support::admin.contact_form.index', compact('model'));
    }

    public function export()
    {
        return Excel::download(new ContactFormExport, 'contacts_' . date('Y-m-d_His') . '.xlsx');
    }

    public function deleteMulti(DeleteMultiRequest $request)
    {
        ContactForm::destroy($request->ids);
        session()->flushMessage(true);

        return redirect()->back();
    }
}
