<?php

namespace Modules\CRM\Actions\Analytics;

use Modules\CRM\Services\Analytics\CrmAnalyticsService;

class GetCrmDashboardAnalyticsAction
{
    public function __construct(private readonly CrmAnalyticsService $service) {}

    public function execute(array $filters = []): array
    {
        return $this->service->build($filters);
    }
}
