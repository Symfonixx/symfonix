<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Finance\Models\Commission;
use Modules\Finance\Models\Salary;
use Modules\Finance\Services\FinanceService;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->setActive('finance_dashboard');
    }

    public function index(): View
    {
        return view('finance::admin.dashboard.index');
    }
}
