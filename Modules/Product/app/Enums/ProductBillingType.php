<?php

namespace Modules\Product\Enums;

use Modules\Core\Traits\HasEnumValues;

enum ProductBillingType: string
{
    use HasEnumValues;

    case ONE_TIME = 'one_time';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
}
