<?php

namespace Modules\CRM\Enums;

use Modules\Core\Traits\HasEnumValues;

enum LeadSource: string
{
    use HasEnumValues;

    case ORGANIC_SEARCH = 'organic_search';
    case DIRECT = 'direct';
    case SOCIAL_MEDIA = 'social_media';
    case REFERRAL = 'referral';
    case PAID_ADS = 'paid_ads';
    case WEBSITE = 'website';
    case SALES = 'sales';
    case MANUAL = 'manual';
}
