<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Subscription;
use Modules\User\Support\PermissionCatalog;

class StoreSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'company_id' => ['required', Rule::exists('companies', 'id')],
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', Rule::exists('services', 'id')],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'status' => ['required', Rule::in(Subscription::STATUSES)],
            'billing_cycle' => ['required', Rule::in(Subscription::BILLING_CYCLES)],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'renewal_at' => ['nullable', 'date'],
            'auto_renew' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'auto_renew' => $this->boolean('auto_renew'),
            'service_id' => $this->input('service_id') ?: ($this->input('service_ids.0') ?: null),
        ]);
    }
}
