<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Contact;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.contacts.view');
    }

    public function view(User $user, Contact $contact): bool
    {
        return $user->can('crm.contacts.view');
    }

    public function create(User $user): bool
    {
        return $user->can('crm.contacts.create');
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->can('crm.contacts.edit');
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->can('crm.contacts.delete');
    }
}
