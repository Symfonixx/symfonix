<?php

namespace Modules\Product\Enums;

use Modules\Core\Traits\HasEnumValues;

enum ProductStatus: string
{
    use HasEnumValues;

    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
