<?php

namespace Modules\Reporting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Reporting\Http\Requests\ReportFilterRequest;
use Modules\Reporting\Services\BaseReportService;
use Modules\Reporting\Services\ReportExportService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class BaseReportController extends Controller
{
    abstract protected function reportService(): BaseReportService;

    abstract protected function department(): string;

    abstract protected function viewName(): string;

    public function __construct(
        protected readonly ReportExportService $exportService,
    ) {}

    public function index(Request $request): View
    {
        $report = $this->reportService()->build($request->all());

        return view($this->viewName(), [
            'report' => $report,
            'department' => $this->department(),
            'filters' => $report['filters'],
        ]);
    }

    public function data(ReportFilterRequest $request): JsonResponse
    {
        $report = $this->reportService()->build($request->filters());

        return response()->json($report);
    }

    public function export(ReportFilterRequest $request): StreamedResponse|Response
    {
        $filters = $request->filters();
        $report = $this->reportService()->build($filters);
        $from = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['date_to'] ?? now()->endOfMonth()->toDateString();
        $title = __('reporting::report.departments.'.$this->department());

        if ($request->input('format') === 'pdf') {
            return $this->exportService->exportPdf($report, $this->department(), $title, $from, $to);
        }

        return $this->exportService->exportCsv($report, $this->department(), $from, $to);
    }
}
