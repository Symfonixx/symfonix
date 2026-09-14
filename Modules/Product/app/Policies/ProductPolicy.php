<?php

namespace Modules\Product\Policies;

use App\Models\User;
use Modules\Product\Models\Product;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('product.catalog.view');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->can('product.catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->can('product.catalog.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can('product.catalog.edit');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->can('product.catalog.delete');
    }
}
