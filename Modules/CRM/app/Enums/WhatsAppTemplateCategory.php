<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum WhatsAppTemplateCategory: string
{
    use HasEnumValues;

    case MARKETING = 'MARKETING';
    case UTILITY = 'UTILITY';
    case AUTHENTICATION = 'AUTHENTICATION';
}
