<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum CompanyActivityType: string
{
    use HasEnumValues;

    case TECHNOLOGY = 'technology';
    case RETAIL = 'retail';
    case HEALTHCARE = 'healthcare';
    case FINANCE = 'finance';
    case EDUCATION = 'education';
    case MANUFACTURING = 'manufacturing';
    case CONSULTING = 'consulting';
    case OTHER = 'other';
}
