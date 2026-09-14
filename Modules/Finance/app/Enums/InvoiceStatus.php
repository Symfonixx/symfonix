<?php

namespace Modules\Finance\Enums;

use Modules\Core\Traits\HasEnumValues;

enum InvoiceStatus: string
{
    use HasEnumValues;

    case DRAFT = 'draft';
    case SENT = 'sent';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case VOID = 'void';

    /**
     * @return list<string>
     */
    public static function openValues(): array
    {
        return [self::SENT->value, self::OVERDUE->value];
    }
}
