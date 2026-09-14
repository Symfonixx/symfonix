<?php

namespace Modules\User\Enums;

use Modules\Core\Traits\HasEnumValues;

enum PunchState: int
{
    use HasEnumValues;

    case CHECK_IN = 0;
    case CHECK_OUT = 1;
    case BREAK_OUT = 2;
    case BREAK_IN = 3;
    case OVERTIME_IN = 4;
    case OVERTIME_OUT = 5;
    case UNDEFINED = 255;

    public function label(): string
    {
        return match ($this) {
            self::CHECK_IN => 'check_in',
            self::CHECK_OUT => 'check_out',
            self::BREAK_OUT => 'break_out',
            self::BREAK_IN => 'break_in',
            self::OVERTIME_IN => 'overtime_in',
            self::OVERTIME_OUT => 'overtime_out',
            self::UNDEFINED => 'undefined',
        };
    }
}
