<?php

namespace Modules\Finance\Policies;

use App\Models\User;
use Modules\Finance\Models\Invoice;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('finance.invoices.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($this->customerCanView($user, $invoice)) {
            return true;
        }

        return $user->can('finance.invoices.view');
    }

    public function create(User $user): bool
    {
        return $user->can('finance.invoices.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('finance.invoices.edit');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('finance.invoices.delete');
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
