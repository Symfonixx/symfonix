<?php

namespace Modules\Support\app\Policies;

use App\Models\User;
use Modules\Support\Models\Ticket;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isCustomer()) {
            return true;
        }

        return $user->can('Support Management');
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isCustomer()) {
            return $ticket->user_id === $user->id;
        }

        return $user->can('Support Management');
    }

    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        if ($ticket->isClosed()) {
            return false;
        }

        if ($user->isCustomer()) {
            return $ticket->user_id === $user->id;
        }

        return $user->can('Support Management');
    }

    public function close(User $user, Ticket $ticket): bool
    {
        if ($ticket->isClosed()) {
            return false;
        }

        if ($user->isCustomer()) {
            return $ticket->user_id === $user->id;
        }

        return $user->can('Support Management');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->can('Support Management');
    }
}
