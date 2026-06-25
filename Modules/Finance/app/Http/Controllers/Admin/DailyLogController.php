<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DailyLogController extends Controller
{
    public function __construct()
    {
        $this->setActive('finance_daily_log');
    }

    public function index(): View
    {
        return view('finance::admin.daily_log.index');
    }
}
