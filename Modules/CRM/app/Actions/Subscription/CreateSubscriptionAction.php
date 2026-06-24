<?php

namespace Modules\CRM\Actions\Subscription;

use Modules\CRM\DTOs\Subscription\SubscriptionData;
use Modules\CRM\Models\Subscription;
use Modules\CRM\Services\Subscription\SubscriptionService;

class CreateSubscriptionAction
{
    public function __construct(private readonly SubscriptionService $service) {}

    public function execute(SubscriptionData $data): ?Subscription
    {
        return $this->service->create($data);
    }
}
