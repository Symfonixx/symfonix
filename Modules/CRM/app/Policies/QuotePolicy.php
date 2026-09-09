<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Quote;

class QuotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function view(User $user, Quote $quote): bool
    {
        return $user->can('CRM Management');
    }

    public function create(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function update(User $user, Quote $quote): bool
    {
        return $user->can('CRM Management');
    }

    public function delete(User $user, Quote $quote): bool
    {
        return $user->can('CRM Management');
    }
}
