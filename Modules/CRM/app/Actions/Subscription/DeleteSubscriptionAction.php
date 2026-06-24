<?php

namespace Modules\CRM\Actions\Subscription;

use Modules\CRM\Models\Subscription;
use Modules\CRM\Services\Subscription\SubscriptionService;

class DeleteSubscriptionAction
{
    public function __construct(private readonly SubscriptionService $service) {}

    public function execute(Subscription $subscription): ?bool
    {
        return $this->service->delete($subscription);
    }
}
