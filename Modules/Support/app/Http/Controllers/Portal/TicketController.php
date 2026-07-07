<?php

namespace Modules\Support\app\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Base\Support\Meta;
use Modules\Support\app\Http\Requests\Portal\StoreTicketReplyRequest;
use Modules\Support\app\Http\Requests\Portal\StoreTicketRequest;
use Modules\Support\app\Services\TicketService;
use Modules\Support\Models\Ticket;
use Modules\Support\Models\TicketCategory;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);

        /** @var User $user */
        $user = $request->user();

        $query = Ticket::query()
            ->where('user_id', $user->id)
            ->with('category:id,name')
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

        $sort = $request->string('sort', 'newest')->toString();
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $tickets = $query
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Ticket $ticket) => $this->formatTicketSummary($ticket));

        $categories = TicketCategory::query()
            ->active()
            ->ordered()
            ->get()
            ->map(fn (TicketCategory $category) => [
                'id' => $category->id,
                'name' => $category->getTranslation('name', app()->getLocale()),
            ]);

        return $this->inertia('Support::Portal/Tickets/Index', [
            'tickets' => $tickets,
            'categories' => $categories,
            'filters' => [
                'status' => $request->string('status')->toString() ?: null,
                'priority' => $request->string('priority')->toString() ?: null,
                'sort' => $sort,
            ],
            'statuses' => Ticket::statuses(),
            'priorities' => Ticket::priorities(),
        ], (new Meta)
            ->title(__('user::portal.pages.tickets_title'))
            ->description(__('user::portal.pages.tickets_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    public function create(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $categories = TicketCategory::query()
            ->active()
            ->ordered()
            ->get()
            ->map(fn (TicketCategory $category) => [
                'id' => $category->id,
                'name' => $category->getTranslation('name', app()->getLocale()),
            ]);

        return $this->inertia('Support::Portal/Tickets/Create', [
            'categories' => $categories,
            'priorities' => Ticket::priorities(),
        ], (new Meta)
            ->title(__('user::portal.tickets.create_title'))
            ->description(__('user::portal.pages.tickets_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        $ticket = $this->ticketService->createTicket(
            $request->user(),
            $request->validated(),
            $request->file('attachment')
        );

        return redirect()
            ->route('portal.tickets.show', $ticket)
            ->with('success', __('user::portal.tickets.created_success'));
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'category:id,name',
            'messages.user:id,name,type',
        ]);

        return $this->inertia('Support::Portal/Tickets/Show', [
            'ticket' => $this->formatTicketDetail($ticket),
        ], (new Meta)
            ->title($ticket->subject.' | '.__('user::portal.pages.tickets_title'))
            ->description(__('user::portal.pages.ticket_show_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    public function reply(StoreTicketReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('reply', $ticket);

        $this->ticketService->addReply(
            $ticket,
            $request->user(),
            $request->validated('body'),
            $request->file('attachment')
        );

        return redirect()
            ->route('portal.tickets.show', $ticket)
            ->with('success', __('user::portal.tickets.reply_sent'));
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);

        $this->ticketService->closeTicket($ticket, $request->user());

        return redirect()
            ->route('portal.tickets.show', $ticket)
            ->with('success', __('user::portal.tickets.closed_success'));
    }

    private function formatTicketSummary(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'category' => $ticket->category?->getTranslation('name', app()->getLocale()),
            'created_at' => $ticket->created_at?->toDateTimeString(),
        ];
    }

    private function formatTicketDetail(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'category' => $ticket->category?->getTranslation('name', app()->getLocale()),
            'created_at' => $ticket->created_at?->toDateTimeString(),
            'closed_at' => $ticket->closed_at?->toDateTimeString(),
            'can_reply' => $ticket->canCustomerReply(),
            'attachment' => $this->formatAttachment($ticket->attachment_path, $ticket->attachment_name),
            'messages' => $ticket->messages->map(fn ($message) => [
                'id' => $message->id,
                'body' => $message->body,
                'author' => $message->user?->name,
                'is_staff' => $message->user?->isAdmin() ?? false,
                'created_at' => $message->created_at?->toDateTimeString(),
                'attachment' => $this->formatAttachment($message->attachment_path, $message->attachment_name),
            ])->values()->all(),
        ];
    }

    private function formatAttachment(?string $path, ?string $name): ?array
    {
        if (! $path) {
            return null;
        }

        return [
            'name' => $name ?? basename($path),
            'url' => asset('storage/'.$path),
        ];
    }
}
