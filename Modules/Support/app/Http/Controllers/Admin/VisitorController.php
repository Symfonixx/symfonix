<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class VisitorController extends Controller
{
    public function __construct()
    {
        $this->setActive('support');
        $this->setActive('visitors');
    }

    public function index()
    {
        $model = VisitorTable::latest()->paginate(config('core.page_size'));

        return view('support::admin.visitor.index', compact('model'));
    }
}
