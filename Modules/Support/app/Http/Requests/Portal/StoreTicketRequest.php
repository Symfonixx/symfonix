<?php

namespace Modules\Support\app\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Support\Models\Ticket;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() ?? false;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'ticket_category_id' => ['required', 'integer', Rule::exists('ticket_categories', 'id')->where('is_active', true)],
            'priority' => ['required', Rule::in(Ticket::priorities())],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip'],
        ];
    }
}
