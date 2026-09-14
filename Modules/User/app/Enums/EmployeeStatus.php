<?php

namespace Modules\User\Enums;

use Modules\Core\Traits\HasEnumValues;

enum EmployeeStatus: string
{
    use HasEnumValues;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
