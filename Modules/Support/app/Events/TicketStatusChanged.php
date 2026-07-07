<?php

namespace Modules\Support\app\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Support\Models\Ticket;

class TicketStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly string $fromStatus,
        public readonly string $toStatus,
        public readonly ?User $changedBy = null,
    ) {}
}
