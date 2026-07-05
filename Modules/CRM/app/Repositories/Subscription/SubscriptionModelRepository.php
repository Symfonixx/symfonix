<?php

namespace Modules\CRM\Repositories\Subscription;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\CRM\DTOs\Subscription\SubscriptionData;
use Modules\CRM\Models\Subscription;

class SubscriptionModelRepository implements SubscriptionRepository
{
    use ExceptionHandlerTrait;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Subscription::query()
            ->with(['company:id,name', 'service:id,title', 'services:id,title'])
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id, bool $withTrashed = false): Subscription
    {
        $query = Subscription::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    public function create(SubscriptionData $data): ?Subscription
    {
        return $this->execute(function () use ($data) {
            $subscription = Subscription::create($data->toArray());
            session()->flushMessage(true);

            return $subscription;
        });
    }

    public function update(Subscription $subscription, SubscriptionData $data): ?Subscription
    {
        return $this->execute(function () use ($subscription, $data) {
            $subscription->update($data->toArray());
            session()->flushMessage(true);

            return $subscription;
        });
    }

    public function delete(Subscription $subscription): ?bool
    {
        return $this->execute(function () use ($subscription) {
            $deleted = $subscription->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $deleted = Subscription::whereIn('id', $ids)->delete();
            session()->flushMessage(true);

            return (bool) $deleted;
        });
    }
}
