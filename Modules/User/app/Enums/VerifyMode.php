<?php

namespace Modules\User\Enums;

use Modules\Core\Traits\HasEnumValues;

enum VerifyMode: int
{
    use HasEnumValues;

    case OTHER = 0;
    case FINGERPRINT = 1;
    case PASSWORD = 3;
    case CARD = 4;
    case FACE = 15;
}
