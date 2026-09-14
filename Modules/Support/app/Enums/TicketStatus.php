<?php

namespace Modules\Support\Enums;

use Modules\Core\Traits\HasEnumValues;

enum TicketStatus: string
{
    use HasEnumValues;

    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    /**
     * @return list<string>
     */
    public static function openValues(): array
    {
        return [self::OPEN->value, self::IN_PROGRESS->value];
    }

    /**
     * @return list<string>
     */
    public static function closedValues(): array
    {
        return [self::RESOLVED->value, self::CLOSED->value];
    }
}
