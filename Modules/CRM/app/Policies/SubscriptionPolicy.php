<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Subscription;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isCustomer()) {
            return true;
        }

        return $user->can('sales.subscriptions.view');
    }

    public function view(User $user, Subscription $subscription): bool
    {
        if ($user->isCustomer() && in_array($subscription->company_id, $user->companyIds(), true)) {
            return true;
        }

        return $user->can('sales.subscriptions.view');
    }

    public function create(User $user): bool
    {
        return $user->can('sales.subscriptions.create');
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $user->can('sales.subscriptions.edit');
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->can('sales.subscriptions.delete');
    }
}
