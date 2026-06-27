<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Contact;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function view(User $user, Contact $contact): bool
    {
        return $user->can('CRM Management');
    }

    public function create(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->can('CRM Management');
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->can('CRM Management');
    }
}
