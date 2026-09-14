<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\CrmActivity;

class CrmActivityPolicy
{
    public function create(User $user): bool
    {
        return $user->can('crm.activities.create');
    }

    public function delete(User $user, CrmActivity $activity): bool
    {
        return $user->can('crm.activities.delete');
    }
}
