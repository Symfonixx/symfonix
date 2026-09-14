<?php

namespace Modules\Product\Policies;

use App\Models\User;
use Modules\Product\Models\ProductSale;

class ProductSalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('finance.product_sales.view');
    }

    public function create(User $user): bool
    {
        return $user->can('finance.product_sales.create');
    }

    public function delete(User $user, ProductSale $productSale): bool
    {
        return $user->can('finance.product_sales.delete');
    }
}
