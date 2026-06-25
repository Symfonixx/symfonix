<?php

namespace Modules\Finance\Policies;

use App\Models\User;
use Modules\Finance\Models\Commission;

class CommissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Finance Management');
    }

    public function update(User $user, Commission $commission): bool
    {
        return $user->can('Finance Management');
    }
}
