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
        if ($this->customerCanView($user, $invoice)) {
            return true;
        }

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

    private function customerCanView(User $user, Invoice $invoice): bool
    {
        if (! $user->isCustomer()) {
            return false;
        }

        if (in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID], true)) {
            return false;
        }

        return in_array($invoice->company_id, $user->companyIds(), true);
    }
}
