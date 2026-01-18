<?php

namespace Modules\Services\Enums;

enum ServiceStatus: string
{
    case ARCHIVED = 'Archived';
    case PUBLISHED = 'Published';

    public static function getStatus(): array
    {
        return array_map(
            fn (self $status) => [$status->value => __($status->value)],
            self::cases()
        );
    }
}
