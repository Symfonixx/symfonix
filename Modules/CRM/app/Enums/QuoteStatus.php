<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum QuoteStatus: string
{
    use HasEnumValues;

    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case VOID = 'void';
}
