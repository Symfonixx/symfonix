<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Deal;
use Modules\CRM\Support\CrmAccess;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function view(User $user, Deal $deal): bool
    {
        return CrmAccess::canAccessDeal($user, $deal->assigned_to);
    }

    public function create(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function update(User $user, Deal $deal): bool
    {
        return CrmAccess::canAccessDeal($user, $deal->assigned_to);
    }

    public function delete(User $user, Deal $deal): bool
    {
        return CrmAccess::canAccessDeal($user, $deal->assigned_to);
    }
}
