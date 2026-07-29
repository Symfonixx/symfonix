<?php

namespace Modules\CRM\Services\Subscription;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\CRM\DTOs\Subscription\SubscriptionData;
use Modules\CRM\Events\SubscriptionCreated;
use Modules\CRM\Models\Subscription;
use Modules\CRM\Repositories\Subscription\SubscriptionRepository;
use Modules\CRM\Support\AuditLogger;

class SubscriptionService
{
    public function __construct(private readonly SubscriptionRepository $repository) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, (int) config('core.page_size', 15));
    }

    public function findForEdit(int $id, bool $withTrashed = false): Subscription
    {
        return $this->repository->findOrFail($id, $withTrashed);
    }

    public function create(SubscriptionData $data, array $serviceIds = []): ?Subscription
    {
        $payload = $this->normalizePayload($data);
        $subscription = $this->repository->create($payload);

        if ($subscription) {
            $this->syncServices($subscription, $serviceIds);
            AuditLogger::logCreated($subscription);

            Log::info('CRM subscription created', [
                'subscription_id' => $subscription->id,
                'company_id' => $subscription->company_id,
                'actor_id' => auth()->id(),
            ]);

            SubscriptionCreated::dispatch($subscription);
        }

        return $subscription;
    }

    public function update(Subscription $subscription, SubscriptionData $data, array $serviceIds = []): ?Subscription
    {
        $before = AuditLogger::auditableSnapshot($subscription);
        $payload = $this->normalizePayload($data);
        $updated = $this->repository->update($subscription, $payload);

        if ($updated) {
            $this->syncServices($subscription, $serviceIds);
            AuditLogger::logUpdated($subscription, $before, AuditLogger::auditableSnapshot($subscription->fresh()));

            Log::info('CRM subscription updated', [
                'subscription_id' => $subscription->id,
                'company_id' => $subscription->company_id,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(Subscription $subscription): ?bool
    {
        AuditLogger::logDeleted($subscription);
        $deleted = $this->repository->delete($subscription);

        if ($deleted) {
            Log::warning('CRM subscription deleted', [
                'subscription_id' => $subscription->id,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): ?bool
    {
        $deleted = $this->repository->bulkDelete($ids);

        if ($deleted) {
            Log::warning('CRM subscriptions bulk deleted', [
                'subscription_ids' => $ids,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    private function normalizePayload(SubscriptionData $data): SubscriptionData
    {
        $array = $data->toArray();

        if ($array['status'] === Subscription::STATUS_CANCELLED && empty($array['cancelled_at'])) {
            $array['cancelled_at'] = now()->toDateTimeString();
        }

        if ($array['status'] !== Subscription::STATUS_CANCELLED) {
            $array['cancelled_at'] = null;
        }

        if ($array['billing_cycle'] === Subscription::BILLING_ONE_TIME) {
            $array['auto_renew'] = false;
            $array['renewal_at'] = null;
        } elseif (empty($array['renewal_at']) && ! empty($array['starts_at'])) {
            $array['renewal_at'] = $this->calculateNextRenewal(
                $array['starts_at'],
                $array['billing_cycle']
            );
        }

        return SubscriptionData::from($array);
    }

    public function syncServices(Subscription $subscription, array $serviceIds): void
    {
        $ids = collect($serviceIds)
            ->filter(fn ($id) => $id !== '' && $id !== null)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $subscription->services()->sync($ids);

        if ($ids !== []) {
            $subscription->update(['service_id' => $ids[0]]);
        }
    }

    public function advanceRenewalDate(Subscription $subscription): void
    {
        if (! $subscription->renewal_at || $subscription->billing_cycle === Subscription::BILLING_ONE_TIME) {
            return;
        }

        $next = match ($subscription->billing_cycle) {
            Subscription::BILLING_MONTHLY => $subscription->renewal_at->copy()->addMonth(),
            Subscription::BILLING_QUARTERLY => $subscription->renewal_at->copy()->addMonths(3),
            Subscription::BILLING_YEARLY => $subscription->renewal_at->copy()->addYear(),
            default => null,
        };

        if ($next) {
            $subscription->update(['renewal_at' => $next->toDateString()]);
        }
    }

    private function calculateNextRenewal(string $startDate, string $billingCycle): ?string
    {
        $date = \Carbon\Carbon::parse($startDate);

        return match ($billingCycle) {
            Subscription::BILLING_MONTHLY => $date->copy()->addMonth()->toDateString(),
            Subscription::BILLING_QUARTERLY => $date->copy()->addMonths(3)->toDateString(),
            Subscription::BILLING_YEARLY => $date->copy()->addYear()->toDateString(),
            default => null,
        };
    }
}
