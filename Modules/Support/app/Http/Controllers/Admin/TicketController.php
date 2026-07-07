<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Support\app\Http\Requests\Admin\StoreTicketReplyRequest;
use Modules\Support\app\Http\Requests\Admin\UpdateTicketRequest;
use Modules\Support\app\Events\TicketStatusChanged;
use Modules\Support\app\Services\TicketService;
use Modules\Support\Models\Ticket;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService)
    {
        $this->setActive('support');
        $this->setActive('tickets');
    }

    public function index(Request $request): View
    {
        $query = Ticket::query()
            ->with(['user:id,name,email', 'category:id,name', 'assignee:id,name'])
            ->latest();

        if ($status = $request->string('status')->toString()) {
            if (in_array($status, Ticket::statuses(), true)) {
                $query->where('status', $status);
            }
        }

        if ($priority = $request->string('priority')->toString()) {
            if (in_array($priority, Ticket::priorities(), true)) {
                $query->where('priority', $priority);
            }
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $model = $query->paginate(config('core.page_size'))->withQueryString();

        return view('support::admin.ticket.index', [
            'model' => $model,
            'statuses' => Ticket::statuses(),
            'priorities' => Ticket::priorities(),
            'filters' => [
                'status' => $request->string('status')->toString() ?: null,
                'priority' => $request->string('priority')->toString() ?: null,
                'search' => $request->string('search')->toString() ?: null,
            ],
        ]);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'user:id,name,email',
            'category:id,name',
            'assignee:id,name',
            'messages.user:id,name,type',
        ]);

        $admins = User::query()
            ->where('type', User::TYPE_ADMIN)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('support::admin.ticket.show', [
            'ticket' => $ticket,
            'admins' => $admins,
            'statuses' => Ticket::statuses(),
            'priorities' => Ticket::priorities(),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === Ticket::STATUS_CLOSED && $ticket->status !== Ticket::STATUS_CLOSED) {
            $data['closed_at'] = now();
        }

        if ($data['status'] !== Ticket::STATUS_CLOSED) {
            $data['closed_at'] = null;
        }

        $previousStatus = $ticket->status;
        $ticket->update($data);

        if ($previousStatus !== $ticket->status) {
            event(new TicketStatusChanged($ticket->fresh(), $previousStatus, $ticket->status, $request->user()));
        }

        session()->flushMessage(true);

        return redirect()->route('admin.tickets.show', $ticket);
    }

    public function reply(StoreTicketReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->ticketService->addReply(
            $ticket,
            $request->user(),
            $request->validated('body'),
            $request->file('attachment')
        );

        session()->flushMessage(true);

        return redirect()->route('admin.tickets.show', $ticket);
    }
}
