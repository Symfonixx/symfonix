<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Subscription;
use Modules\User\Support\PermissionCatalog;

class SubscriptionIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(Subscription::STATUSES)],
            'billing_cycle' => ['nullable', Rule::in(Subscription::BILLING_CYCLES)],
            'company_id' => ['nullable', 'integer', Rule::exists('companies', 'id')],
            'renewing_soon' => ['nullable', 'boolean'],
            'renewing_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'with_trashed' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }
}
