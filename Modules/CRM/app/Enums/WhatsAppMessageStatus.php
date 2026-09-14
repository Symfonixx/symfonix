<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum WhatsAppMessageStatus: string
{
    use HasEnumValues;

    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
