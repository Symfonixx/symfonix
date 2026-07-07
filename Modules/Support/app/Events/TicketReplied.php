<?php

namespace Modules\Support\app\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Support\Models\Ticket;
use Modules\Support\Models\TicketMessage;

class TicketReplied
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly TicketMessage $message,
        public readonly User $replier,
    ) {}
}
