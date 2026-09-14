<?php

namespace Modules\Reporting\Http\Controllers\Admin;

use Modules\Reporting\Services\BaseReportService;
use Modules\Reporting\Services\FinanceReportService;
use Modules\Reporting\Services\ReportExportService;

class FinanceReportController extends BaseReportController
{
    public function __construct(
        ReportExportService $exportService,
        private readonly FinanceReportService $financeReportService,
    ) {
        parent::__construct($exportService);
        $this->setActive('reporting_finance');
    }

    protected function reportService(): BaseReportService
    {
        return $this->financeReportService;
    }

    protected function department(): string
    {
        return 'finance';
    }

    protected function viewName(): string
    {
        return 'reporting::admin.finance.index';
    }
}
