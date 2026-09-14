<?php

namespace Modules\Tax\Policies;

use App\Models\User;
use Modules\Tax\Models\TaxRate;

class TaxRatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tax.rates.view');
    }

    public function create(User $user): bool
    {
        return $user->can('tax.rates.create');
    }

    public function update(User $user, TaxRate $taxRate): bool
    {
        return $user->can('tax.rates.edit');
    }

    public function delete(User $user, TaxRate $taxRate): bool
    {
        return $user->can('tax.rates.delete');
    }
}
