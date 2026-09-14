<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Finance\Services\FinanceService;
use Modules\Project\Models\Project;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxCalculationService;

class StoreProjectInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('finance.invoices.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'currency' => ['nullable', 'string', 'size:3'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.service_id' => ['nullable', 'exists:services,id'],
            'lines.*.tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'lines.*.tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var Project|null $project */
            $project = $this->route('project');

            if (! $project || ! $validator->errors()->isEmpty()) {
                return;
            }

            $lines = $this->input('lines', []);
            $headerTaxRate = $this->input('tax_rate_id')
                ? TaxRate::query()->active()->find((int) $this->input('tax_rate_id'))
                : ($project->tax_rate_id ? TaxRate::query()->active()->find($project->tax_rate_id) : null);

            $calculated = app(TaxCalculationService::class)->calculateDocument($lines, $headerTaxRate);
            $total = $calculated['tax_amount'] > 0
                ? $calculated['total']
                : round($calculated['subtotal'] + (float) ($this->input('tax_amount') ?? 0), 2);

            $financeService = app(FinanceService::class);
            $remaining = $financeService->getProjectCollectionSummary($project)['remaining'];

            if ($remaining > 0 && $total > $remaining) {
                $validator->errors()->add(
                    'lines',
                    __('project::project.messages.invoice_exceeds_remaining', [
                        'remaining' => number_format($remaining, 2),
                    ])
                );
            }
        });
    }
}
