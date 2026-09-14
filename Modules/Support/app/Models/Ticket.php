<?php

namespace Modules\Support\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Enums\TicketPriority;
use Modules\Support\Enums\TicketStatus;

class Ticket extends Model
{
    public const STATUS_OPEN = TicketStatus::OPEN->value;

    public const STATUS_IN_PROGRESS = TicketStatus::IN_PROGRESS->value;

    public const STATUS_RESOLVED = TicketStatus::RESOLVED->value;

    public const STATUS_CLOSED = TicketStatus::CLOSED->value;

    public const PRIORITY_LOW = TicketPriority::LOW->value;

    public const PRIORITY_MEDIUM = TicketPriority::MEDIUM->value;

    public const PRIORITY_HIGH = TicketPriority::HIGH->value;

    public const PRIORITY_URGENT = TicketPriority::URGENT->value;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'ticket_category_id',
        'assigned_to',
        'subject',
        'description',
        'attachment_path',
        'attachment_name',
        'priority',
        'status',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function isClosed(): bool
    {
        return in_array($this->status, TicketStatus::closedValues(), true);
    }

    public function canCustomerReply(): bool
    {
        return ! $this->isClosed();
    }

    public static function statuses(): array
    {
        return TicketStatus::values();
    }

    public static function priorities(): array
    {
        return TicketPriority::values();
    }
}
