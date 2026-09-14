<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Services\Dashboard\DashboardLayoutService;

class UpdateCrmDashboardLayoutRequest extends FormRequest
{
    public function rules(): array
    {
        $ids = app(DashboardLayoutService::class)->knownIds();

        return [
            'reset' => ['sometimes', 'boolean'],
            'widgets' => ['required_without:reset', 'array', 'min:1'],
            'widgets.*.id' => ['required_with:widgets', 'string', Rule::in($ids)],
            'widgets.*.visible' => ['required_with:widgets', 'boolean'],
            'widgets.*.order' => ['required_with:widgets', 'integer', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('overview.crm_analytics.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $widgets = $this->input('widgets');

        if (! is_array($widgets)) {
            return;
        }

        $this->merge([
            'widgets' => array_values(array_map(function ($widget, $index) {
                return [
                    'id' => $widget['id'] ?? null,
                    'visible' => filter_var($widget['visible'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'order' => (int) ($widget['order'] ?? $index),
                ];
            }, $widgets, array_keys($widgets))),
        ]);
    }
}
