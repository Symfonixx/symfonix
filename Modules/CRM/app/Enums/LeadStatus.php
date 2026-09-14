<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum LeadStatus: string
{
    use HasEnumValues;

    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case UNQUALIFIED = 'unqualified';
    case CONVERTED = 'converted';
    case LOST = 'lost';
}
