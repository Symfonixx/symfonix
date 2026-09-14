<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum SubscriptionStatus: string
{
    use HasEnumValues;

    case ACTIVE = 'active';
    case TRIAL = 'trial';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
