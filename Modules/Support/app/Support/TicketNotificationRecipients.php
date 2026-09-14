<?php

namespace Modules\Support\app\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Support\Models\Ticket;

class TicketNotificationRecipients
{
    /**
     * @return Collection<int, User>
     */
    public static function supportAdmins(?User $except = null): Collection
    {
        return User::permission('support.tickets.view')
            ->when($except, fn ($query) => $query->where('id', '!=', $except->id))
            ->get();
    }

    /**
     * @return Collection<int, User>
     */
    public static function forAdminReply(Ticket $ticket, User $replier): Collection
    {
        if ($replier->isCustomer()) {
            $ticket->loadMissing('assignee');

            if ($ticket->assignee && $ticket->assignee->id !== $replier->id) {
                return collect([$ticket->assignee]);
            }

            return self::supportAdmins($replier);
        }

        $ticket->loadMissing('user');

        if ($ticket->user && $ticket->user->id !== $replier->id) {
            return collect([$ticket->user]);
        }

        return collect();
    }
}
