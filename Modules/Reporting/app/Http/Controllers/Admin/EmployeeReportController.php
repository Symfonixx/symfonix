<?php

namespace Modules\Reporting\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Reporting\Services\BaseReportService;
use Modules\Reporting\Services\EmployeeReportService;
use Modules\Reporting\Services\ReportExportService;
use Modules\User\Models\Employee;

class EmployeeReportController extends BaseReportController
{
    public function __construct(
        ReportExportService $exportService,
        private readonly EmployeeReportService $employeeReportService,
    ) {
        parent::__construct($exportService);
        $this->setActive('reporting_employee');
    }

    protected function reportService(): BaseReportService
    {
        return $this->employeeReportService;
    }

    protected function department(): string
    {
        return 'employee';
    }

    protected function viewName(): string
    {
        return 'reporting::admin.employee.index';
    }

    public function show(Employee $employee, Request $request): View
    {
        $this->setActive('reporting_employee');

        $report = $this->employeeReportService->buildProfile($employee, $request->all());

        return view('reporting::admin.employee.show', [
            'report' => $report,
            'employee' => $employee,
            'filters' => $report['filters'],
        ]);
    }
}
