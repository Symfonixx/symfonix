<?php

namespace Modules\CRM\Actions\Subscription;

use Modules\CRM\Services\Subscription\SubscriptionService;

class BulkDeleteSubscriptionsAction
{
    public function __construct(private readonly SubscriptionService $service) {}

    public function execute(array $ids): ?bool
    {
        return $this->service->bulkDelete($ids);
    }
}
