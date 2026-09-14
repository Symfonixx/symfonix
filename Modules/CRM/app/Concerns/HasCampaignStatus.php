<?php

namespace Modules\CRM\Concerns;

trait HasCampaignStatus
{
    public static function statusBadgeClass(?string $status): string
    {
        return match ($status) {
            self::STATUS_FINISHED => 'badge-light-success',
            self::STATUS_FAILED => 'badge-light-danger',
            'sending' => 'badge-light-info', // WhatsAppCampaign::STATUS_SENDING
            default => 'badge-light-warning',
        };
    }
}
