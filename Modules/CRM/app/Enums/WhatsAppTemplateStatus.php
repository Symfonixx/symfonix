<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum WhatsAppTemplateStatus: string
{
    use HasEnumValues;

    case APPROVED = 'approved';
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case DRAFT = 'draft';
}
