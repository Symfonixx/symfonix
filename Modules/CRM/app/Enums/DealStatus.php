<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum DealStatus: string
{
    use HasEnumValues;

    case OPEN = 'open';
    case WON = 'won';
    case LOST = 'lost';
}
