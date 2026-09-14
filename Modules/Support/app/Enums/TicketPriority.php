<?php

namespace Modules\Support\Enums;

use Modules\Core\Traits\HasEnumValues;

enum TicketPriority: string
{
    use HasEnumValues;

    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
}
