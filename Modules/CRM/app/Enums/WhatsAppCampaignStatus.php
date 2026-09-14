<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum WhatsAppCampaignStatus: string
{
    use HasEnumValues;

    case PENDING = 'pending';
    case SENDING = 'sending';
    case FINISHED = 'finished';
    case FAILED = 'failed';
}
