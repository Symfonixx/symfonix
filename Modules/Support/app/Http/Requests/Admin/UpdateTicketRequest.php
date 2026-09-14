<?php

namespace Modules\Support\app\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Support\Models\Ticket;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('support.tickets.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(Ticket::statuses())],
            'priority' => ['required', Rule::in(Ticket::priorities())],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
