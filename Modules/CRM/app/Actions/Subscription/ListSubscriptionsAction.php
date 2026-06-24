<?php

namespace Modules\CRM\Actions\Subscription;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\Services\Subscription\SubscriptionService;

class ListSubscriptionsAction
{
    public function __construct(private readonly SubscriptionService $service) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->service->list($filters);
    }
}
