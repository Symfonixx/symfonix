<?php

namespace Modules\CRM\Actions\Subscription;

use Modules\CRM\DTOs\Subscription\SubscriptionData;
use Modules\CRM\Models\Subscription;
use Modules\CRM\Services\Subscription\SubscriptionService;

class UpdateSubscriptionAction
{
    public function __construct(private readonly SubscriptionService $service) {}

    public function execute(Subscription $subscription, SubscriptionData $data): ?Subscription
    {
        return $this->service->update($subscription, $data);
    }
}
