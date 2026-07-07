<?php

namespace Modules\Support\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Support\Models\Ticket;

class TicketCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Ticket $ticket) {}
}
