<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\Support\app\Exports\SubscriberExport;
use Modules\Support\app\Imports\SubscriberImport;
use Modules\Support\Models\Subscriber;

class SubscriberController extends Controller
{
    public function __construct()
    {
        $this->setActive('support');
        $this->setActive('subscribers');
    }

    public function index()
    {
        $model = Subscriber::latest()->paginate(config('core.page_size'));

        return view('support::admin.subscriber.index', compact('model'));
    }

    public function export()
    {
        return Excel::download(new SubscriberExport, 'subscribers_' . date('Y-m-d_His') . '.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new SubscriberImport, $request->file('file'));
            session()->flushMessage(true, __('Subscribers imported successfully.'));
        } catch (\Exception $e) {
            session()->flushMessage(false, __('Error importing subscribers: :message', ['message' => $e->getMessage()]));
        }

        return redirect()->back();
    }

    public function deleteMulti(DeleteMultiRequest $request)
    {
        Subscriber::destroy($request->ids);
        session()->flushMessage(true);

        return redirect()->back();
    }
}
