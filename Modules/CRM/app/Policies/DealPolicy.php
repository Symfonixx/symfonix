<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Deal;
use Modules\CRM\Support\CrmAccess;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('sales.deals.view') || $user->can('sales.pipeline.view');
    }

    public function view(User $user, Deal $deal): bool
    {
        return CrmAccess::canAccessDeal($user, $deal->assigned_to, 'sales.deals.view');
    }

    public function create(User $user): bool
    {
        return $user->can('sales.deals.create');
    }

    public function update(User $user, Deal $deal): bool
    {
        return CrmAccess::canAccessDeal($user, $deal->assigned_to, 'sales.deals.edit');
    }

    public function delete(User $user, Deal $deal): bool
    {
        return CrmAccess::canAccessDeal($user, $deal->assigned_to, 'sales.deals.delete');
    }
}
