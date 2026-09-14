<?php

namespace Modules\Finance\Policies;

use App\Models\User;
use Modules\Finance\Models\Commission;

class CommissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('finance.commissions.view');
    }

    public function update(User $user, Commission $commission): bool
    {
        return $user->can('finance.commissions.edit');
    }
}
