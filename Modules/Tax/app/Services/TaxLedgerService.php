<?php

namespace Modules\Tax\Services;

use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Services\CurrencyService;
use Modules\Project\Models\Project;
use Modules\Tax\Models\TaxLedgerEntry;
use Modules\Tax\Models\TaxRate;

class TaxLedgerService
{
    public function __construct(
        private readonly CurrencyService $currencyService,
    ) {}

    public function recordOutputTaxForInvoice(Invoice $invoice): void
    {
        if ((float) $invoice->tax_amount <= 0) {
            return;
        }

        if ($this->entryExists(Invoice::class, $invoice->id, TaxLedgerEntry::DIRECTION_OUTPUT)) {
            return;
        }

        $invoice->loadMissing(['lines.taxRate', 'company', 'project', 'subscription']);

        DB::transaction(function () use ($invoice) {
            if ($invoice->lines->where('tax_amount', '>', 0)->isNotEmpty()) {
                foreach ($invoice->lines as $line) {
                    if ((float) $line->tax_amount <= 0) {
                        continue;
                    }

                    $this->createEntry([
                        'tax_rate_id' => $line->tax_rate_id,
                        'direction' => TaxLedgerEntry::DIRECTION_OUTPUT,
                        'amount' => (float) $line->tax_amount,
                        'currency' => $invoice->currency,
                        'transaction_date' => $invoice->issued_at?->toDateString() ?? now()->toDateString(),
                        'source_type' => Invoice::class,
                        'source_id' => $invoice->id,
                        'company_id' => $invoice->company_id,
                        'project_id' => $invoice->project_id,
                        'subscription_id' => $invoice->subscription_id,
                        'description' => __('tax::ledger.descriptions.invoice_line', [
                            'number' => $invoice->invoice_number,
                            'line' => $line->description,
                        ]),
                    ]);
                }

                return;
            }

            $defaultRate = TaxRate::resolveDefault();

            $this->createEntry([
                'tax_rate_id' => $defaultRate?->id,
                'direction' => TaxLedgerEntry::DIRECTION_OUTPUT,
                'amount' => (float) $invoice->tax_amount,
                'currency' => $invoice->currency,
                'transaction_date' => $invoice->issued_at?->toDateString() ?? now()->toDateString(),
                'source_type' => Invoice::class,
                'source_id' => $invoice->id,
                'company_id' => $invoice->company_id,
                'project_id' => $invoice->project_id,
                'subscription_id' => $invoice->subscription_id,
                'description' => __('tax::ledger.descriptions.invoice', [
                    'number' => $invoice->invoice_number,
                ]),
            ]);
        });
    }

    public function recordInputTaxForExpense(JournalEntry $entry): void
    {
        if ($entry->flow !== JournalEntry::FLOW_EXPENSE || (float) $entry->tax_amount <= 0) {
            return;
        }

        if ($this->entryExists(JournalEntry::class, $entry->id, TaxLedgerEntry::DIRECTION_INPUT)) {
            return;
        }

        $projectId = $entry->reference_type === Project::class
            ? $entry->reference_id
            : null;

        $this->createEntry([
            'tax_rate_id' => $entry->tax_rate_id,
            'direction' => TaxLedgerEntry::DIRECTION_INPUT,
            'amount' => (float) $entry->tax_amount,
            'currency' => $entry->currency,
            'transaction_date' => $entry->transaction_date?->toDateString() ?? now()->toDateString(),
            'source_type' => JournalEntry::class,
            'source_id' => $entry->id,
            'project_id' => $projectId,
            'description' => $entry->description ?? __('tax::ledger.descriptions.expense'),
        ]);
    }

    public function reverseForSource(string $sourceType, int $sourceId): void
    {
        TaxLedgerEntry::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createEntry(array $data): TaxLedgerEntry
    {
        $currency = strtoupper((string) ($data['currency'] ?? $this->currencyService->defaultCurrency()));
        $amount = round((float) $data['amount'], 2);
        $exchangeRate = $this->currencyService->snapshotRateToBase($currency);

        return TaxLedgerEntry::query()->create([
            ...$data,
            'currency' => $currency,
            'amount' => $amount,
            'base_amount' => round($amount * $exchangeRate, 2),
        ]);
    }

    private function entryExists(string $sourceType, int $sourceId, string $direction): bool
    {
        return TaxLedgerEntry::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('direction', $direction)
            ->exists();
    }
}
