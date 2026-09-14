<?php

namespace Modules\Reporting\Http\Controllers\Admin;

use Modules\Reporting\Services\BaseReportService;
use Modules\Reporting\Services\ReportExportService;
use Modules\Reporting\Services\SalesReportService;

class SalesReportController extends BaseReportController
{
    public function __construct(
        ReportExportService $exportService,
        private readonly SalesReportService $salesReportService,
    ) {
        parent::__construct($exportService);
        $this->setActive('reporting_sales');
    }

    protected function reportService(): BaseReportService
    {
        return $this->salesReportService;
    }

    protected function department(): string
    {
        return 'sales';
    }

    protected function viewName(): string
    {
        return 'reporting::admin.sales.index';
    }
}
