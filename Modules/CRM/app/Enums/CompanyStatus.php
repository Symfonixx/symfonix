<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum CompanyStatus: string
{
    use HasEnumValues;

    case ACTIVE = 'active';
    case DISABLED = 'disabled';
}
