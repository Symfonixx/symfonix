<?php

namespace Modules\Support\app\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Support\app\Events\TicketClosed;
use Modules\Support\app\Events\TicketCreated;
use Modules\Support\app\Events\TicketReplied;
use Modules\Support\Models\Ticket;
use Modules\Support\Models\TicketMessage;

class TicketService
{
    public function generateTicketNumber(): string
    {
        $prefix = 'TKT-'.now()->format('Y').'-';
        $lastTicket = Ticket::query()
            ->where('ticket_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($lastTicket) {
            $lastSequence = (int) substr($lastTicket->ticket_number, strlen($prefix));
            $sequence = $lastSequence + 1;
        }

        return $prefix.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }

    public function createTicket(User $user, array $data, ?UploadedFile $attachment = null): Ticket
    {
        return DB::transaction(function () use ($user, $data, $attachment) {
            $attachmentData = $this->storeAttachment($attachment);

            $ticket = Ticket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'user_id' => $user->id,
                'ticket_category_id' => $data['ticket_category_id'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? Ticket::PRIORITY_MEDIUM,
                'status' => Ticket::STATUS_OPEN,
                ...$attachmentData,
            ]);

            event(new TicketCreated($ticket));

            return $ticket;
        });
    }

    public function addReply(Ticket $ticket, User $user, string $body, ?UploadedFile $attachment = null): TicketMessage
    {
        $attachmentData = $this->storeAttachment($attachment);

        $message = $ticket->messages()->create([
            'user_id' => $user->id,
            'body' => $body,
            ...$attachmentData,
        ]);

        if ($ticket->status === Ticket::STATUS_OPEN && $user->isAdmin()) {
            $ticket->update(['status' => Ticket::STATUS_IN_PROGRESS]);
        }

        event(new TicketReplied($ticket->fresh(), $message, $user));

        return $message;
    }

    public function closeTicket(Ticket $ticket, User $closedBy): void
    {
        $ticket->update([
            'status' => Ticket::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        event(new TicketClosed($ticket->fresh(), $closedBy));
    }

    /**
     * @return array{attachment_path: ?string, attachment_name: ?string}
     */
    private function storeAttachment(?UploadedFile $file): array
    {
        if (! $file) {
            return [
                'attachment_path' => null,
                'attachment_name' => null,
            ];
        }

        $path = $file->store('tickets/attachments', 'public');

        return [
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
        ];
    }
}
