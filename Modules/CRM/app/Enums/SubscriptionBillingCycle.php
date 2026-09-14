<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum SubscriptionBillingCycle: string
{
    use HasEnumValues;

    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
    case ONE_TIME = 'one_time';
}
