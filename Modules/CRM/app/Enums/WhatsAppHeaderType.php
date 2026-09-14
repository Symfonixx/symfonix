<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum WhatsAppHeaderType: string
{
    use HasEnumValues;

    case NONE = 'none';
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
}
