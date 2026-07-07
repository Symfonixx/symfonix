<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Finance\Http\Requests\RecordSalaryPayoutRequest;
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
            ->orderByDesc('period')
            ->orderByDesc('id')
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

    public function recordPayout(RecordSalaryPayoutRequest $request, Salary $salary): RedirectResponse
    {
        $this->financeService->recordSalaryPayout(
            $salary->load('employee'),
            $request->validated('paid_at'),
        );

        session()->flushMessage(true, __('finance::salary.messages.paid'));

        return back();
    }

    public function destroy(Salary $salary): RedirectResponse
    {
        $this->financeService->deleteSalary($salary);

        session()->flushMessage(true, __('finance::salary.messages.deleted'));

        return back();
    }
}
