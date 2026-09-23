<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum MarketingEmailStatus: string
{
    use HasEnumValues;

    case PENDING = 'pending';
    case QUEUED = 'queued';
    case FAILED = 'failed';
}
