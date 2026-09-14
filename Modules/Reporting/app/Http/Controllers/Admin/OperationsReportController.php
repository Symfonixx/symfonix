<?php

namespace Modules\Reporting\Http\Controllers\Admin;

use Modules\Reporting\Services\BaseReportService;
use Modules\Reporting\Services\OperationsReportService;
use Modules\Reporting\Services\ReportExportService;

class OperationsReportController extends BaseReportController
{
    public function __construct(
        ReportExportService $exportService,
        private readonly OperationsReportService $operationsReportService,
    ) {
        parent::__construct($exportService);
        $this->setActive('reporting_operations');
    }

    protected function reportService(): BaseReportService
    {
        return $this->operationsReportService;
    }

    protected function department(): string
    {
        return 'operations';
    }

    protected function viewName(): string
    {
        return 'reporting::admin.operations.index';
    }
}
