<?php

namespace Modules\Product\Policies;

use App\Models\User;
use Modules\Product\Models\ProductCategory;

class ProductCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Product Management');
    }

    public function view(User $user, ProductCategory $productCategory): bool
    {
        return $user->can('Product Management');
    }

    public function create(User $user): bool
    {
        return $user->can('Product Management');
    }

    public function update(User $user, ProductCategory $productCategory): bool
    {
        return $user->can('Product Management');
    }

    public function delete(User $user, ProductCategory $productCategory): bool
    {
        return $user->can('Product Management');
    }
}
