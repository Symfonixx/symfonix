<?php

namespace Modules\Reporting\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Reporting\Http\Requests\ReportFilterRequest;
use Modules\Reporting\Services\EmployeeReportService;
use Modules\Reporting\Services\FinanceReportService;
use Modules\Reporting\Services\MarketingReportService;
use Modules\Reporting\Services\OperationsReportService;
use Modules\Reporting\Services\SalesReportService;

class ReportDataController extends Controller
{
    public function __construct(
        private readonly FinanceReportService $financeReportService,
        private readonly SalesReportService $salesReportService,
        private readonly MarketingReportService $marketingReportService,
        private readonly OperationsReportService $operationsReportService,
        private readonly EmployeeReportService $employeeReportService,
    ) {}

    public function finance(ReportFilterRequest $request): JsonResponse
    {
        $this->authorizeDepartment('finance');

        return response()->json($this->financeReportService->build($request->filters()));
    }

    public function sales(ReportFilterRequest $request): JsonResponse
    {
        $this->authorizeDepartment('sales');

        return response()->json($this->salesReportService->build($request->filters()));
    }

    public function marketing(ReportFilterRequest $request): JsonResponse
    {
        $this->authorizeDepartment('marketing');

        return response()->json($this->marketingReportService->build($request->filters()));
    }

    public function operations(ReportFilterRequest $request): JsonResponse
    {
        $this->authorizeDepartment('operations');

        return response()->json($this->operationsReportService->build($request->filters()));
    }

    public function employee(ReportFilterRequest $request): JsonResponse
    {
        $this->authorizeDepartment('employee');

        return response()->json($this->employeeReportService->build($request->filters()));
    }

    private function authorizeDepartment(string $department): void
    {
        $permission = config("reporting.departments.{$department}.permission", "reporting.{$department}.view");

        abort_unless(auth()->user()?->can($permission), 403);
    }
}
