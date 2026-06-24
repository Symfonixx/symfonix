<?php

namespace Modules\CRM\Repositories\Subscription;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\DTOs\Subscription\SubscriptionData;
use Modules\CRM\Models\Subscription;

interface SubscriptionRepository
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id, bool $withTrashed = false): Subscription;

    public function create(SubscriptionData $data): ?Subscription;

    public function update(Subscription $subscription, SubscriptionData $data): ?Subscription;

    public function delete(Subscription $subscription): ?bool;

    public function bulkDelete(array $ids): ?bool;
}
