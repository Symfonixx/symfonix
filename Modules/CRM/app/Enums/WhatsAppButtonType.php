<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum WhatsAppButtonType: string
{
    use HasEnumValues;

    case QUICK_REPLY = 'QUICK_REPLY';
    case URL = 'URL';
    case PHONE_NUMBER = 'PHONE_NUMBER';
}
