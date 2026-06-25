<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Finance\Http\Requests\StoreSalaryRequest;
use Modules\Finance\Models\Salary;
use Modules\Finance\Services\FinanceService;
use Modules\User\Support\EmployeeAccess;

class SalaryController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {
        $this->authorizeResource(Salary::class, 'salary');
        $this->setActive('finance_salaries');
    }

    public function index(): View
    {
        $salaries = Salary::query()
            ->with('employee:id,name,email')
            ->latest()
            ->paginate((int) config('core.page_size', 15));

        $employees = EmployeeAccess::assignableQuery()
            ->get(['id', 'name']);

        return view('finance::admin.salary.index', compact('salaries', 'employees'));
    }

    public function store(StoreSalaryRequest $request): RedirectResponse
    {
        Salary::query()->create($request->validated());

        session()->flushMessage(true, __('finance::salary.messages.created'));

        return redirect()->route('admin.finance.salaries.index');
    }

    public function recordPayout(Salary $salary): RedirectResponse
    {
        $this->authorize('update', $salary);

        $this->financeService->recordSalaryPayout($salary->load('employee'));

        session()->flushMessage(true, __('finance::salary.messages.paid'));

        return back();
    }
}
