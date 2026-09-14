<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum MarketingCampaignStatus: string
{
    use HasEnumValues;

    case PENDING = 'pending';
    case FINISHED = 'finished';
    case FAILED = 'failed';
}
