<?php

namespace Modules\Finance\Policies;

use App\Models\User;
use Modules\Finance\Models\Invoice;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Finance Management');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('Finance Management');
    }

    public function create(User $user): bool
    {
        return $user->can('Finance Management');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('Finance Management');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('Finance Management');
    }
}
