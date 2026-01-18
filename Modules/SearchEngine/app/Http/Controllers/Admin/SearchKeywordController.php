<?php

namespace Modules\SearchEngine\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\SearchEngine\app\Exports\SearchKeywordExport;
use Modules\SearchEngine\Models\SearchKeyword;

class SearchKeywordController extends Controller
{
    public function __construct()
    {
        $this->setActive('support');
        $this->setActive('search_keywords');
    }

    public function index()
    {
        $model = SearchKeyword::orderBy('count', 'desc')->latest()->paginate(config('core.page_size'));

        return view('searchengine::admin.search_keyword.index', compact('model'));
    }

    public function export()
    {
        return Excel::download(new SearchKeywordExport, 'search_keywords_' . date('Y-m-d_His') . '.xlsx');
    }

    public function deleteMulti(DeleteMultiRequest $request)
    {
        SearchKeyword::destroy($request->ids);
        session()->flushMessage(true);

        return redirect()->back();
    }
}




