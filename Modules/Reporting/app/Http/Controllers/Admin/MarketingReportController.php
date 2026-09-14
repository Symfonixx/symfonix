<?php

namespace Modules\Reporting\Http\Controllers\Admin;

use Modules\Reporting\Services\BaseReportService;
use Modules\Reporting\Services\MarketingReportService;
use Modules\Reporting\Services\ReportExportService;

class MarketingReportController extends BaseReportController
{
    public function __construct(
        ReportExportService $exportService,
        private readonly MarketingReportService $marketingReportService,
    ) {
        parent::__construct($exportService);
        $this->setActive('reporting_marketing');
    }

    protected function reportService(): BaseReportService
    {
        return $this->marketingReportService;
    }

    protected function department(): string
    {
        return 'marketing';
    }

    protected function viewName(): string
    {
        return 'reporting::admin.marketing.index';
    }
}
