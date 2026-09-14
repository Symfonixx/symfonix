<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum CrmActivityType: string
{
    use HasEnumValues;

    case NOTE = 'note';
    case CALL = 'call';
    case MEETING = 'meeting';
    case TASK = 'task';
    case EMAIL = 'email';
}
