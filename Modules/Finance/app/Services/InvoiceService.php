<?php

namespace Modules\Finance\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Base\Support\CompanyBranding;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Subscription;
use Modules\CRM\Services\Subscription\SubscriptionService;
use Modules\Finance\Events\InvoiceSentToCustomer;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\SubscriptionBilling;
use Modules\Product\Models\ProductSale;
use Modules\Project\Models\Project;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxCalculationService;
use Modules\Tax\Services\TaxLedgerService;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService
{
    public function __construct(
        private readonly FinanceService $financeService,
        private readonly SubscriptionService $subscriptionService,
        private readonly CurrencyService $currencyService,
        private readonly TaxCalculationService $taxCalculationService,
        private readonly TaxLedgerService $taxLedgerService,
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Invoice::query()
            ->with(['company:id,name', 'subscription:id,name'])
            ->latest('issued_at');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['company_id'])) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        return $query->paginate((int) config('core.page_size', 15));
    }

    public function createManual(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $lines = $data['lines'] ?? [];
            $headerTaxRate = ! empty($data['tax_rate_id'])
                ? TaxRate::query()->active()->find((int) $data['tax_rate_id'])
                : null;

            $calculated = $this->taxCalculationService->calculateDocument($lines, $headerTaxRate);
            $lines = $calculated['lines'];
            $subtotal = $calculated['subtotal'];
            $taxAmount = $calculated['tax_amount'] > 0
                ? $calculated['tax_amount']
                : (float) ($data['tax_amount'] ?? 0);
            $total = $calculated['tax_amount'] > 0
                ? $calculated['total']
                : round($subtotal + $taxAmount, 2);
            $issuedAt = $data['issued_at'] ?? now()->toDateString();
            $paymentTerms = (int) config('finance.invoice_payment_terms_days', 30);

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'company_id' => $data['company_id'],
                'deal_id' => $data['deal_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'status' => Invoice::STATUS_DRAFT,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'currency' => strtoupper($data['currency'] ?? $this->currencyService->defaultCurrency()),
                'issued_at' => $issuedAt,
                'due_at' => $data['due_at'] ?? Carbon::parse($issuedAt)->addDays($paymentTerms)->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncLines($invoice, $lines);

            if ($invoice->project_id) {
                $this->financeService->updateProjectPaymentStatus($invoice->project_id);
            }

            return $invoice->load('lines');
        });
    }

    public function createForProject(Project $project, array $data): Invoice
    {
        $project->loadMissing('deal');

        $data['company_id'] = $project->company_id;
        $data['project_id'] = $project->id;
        $data['deal_id'] = $data['deal_id'] ?? $project->deal_id;

        if ($project->tax_rate_id) {
            $data['tax_rate_id'] = $data['tax_rate_id'] ?? $project->tax_rate_id;
            $data['lines'] = collect($data['lines'] ?? [])->map(function (array $line) use ($data, $project) {
                if (empty($line['tax_rate_id'])) {
                    $line['tax_rate_id'] = $data['tax_rate_id'] ?? $project->tax_rate_id;
                }

                return $line;
            })->all();
        }

        if (! isset($data['currency'])) {
            $data['currency'] = $project->currency
                ?? $project->deal?->currency
                ?? $this->currencyService->defaultCurrency();
        }

        if (empty($data['notes'])) {
            $data['notes'] = __('finance::invoice.messages.project_invoice_note', ['title' => $project->title]);
        }

        return $this->markAsSent($this->createManual($data));
    }

    public function billInitialSubscription(Subscription $subscription): ?Invoice
    {
        if (
            (float) $subscription->amount <= 0
            || $subscription->status !== Subscription::STATUS_ACTIVE
        ) {
            return null;
        }

        $billingDate = $subscription->starts_at?->toDateString() ?? now()->toDateString();

        if (SubscriptionBilling::query()
            ->where('subscription_id', $subscription->id)
            ->whereDate('billing_date', $billingDate)
            ->exists()
        ) {
            return null;
        }

        $subscription->loadMissing(['company', 'service']);

        $invoice = DB::transaction(function () use ($subscription, $billingDate) {
            $amount = (float) $subscription->amount;
            $defaultRate = TaxRate::resolveDefault();
            $lineAmounts = $this->taxCalculationService->calculateLine(1, $amount, $defaultRate);
            $issuedAt = now()->toDateString();
            $paymentTerms = (int) config('finance.invoice_payment_terms_days', 30);

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'status' => Invoice::STATUS_SENT,
                'subtotal' => $lineAmounts['subtotal'],
                'tax_amount' => $lineAmounts['tax_amount'],
                'total' => $lineAmounts['amount'],
                'currency' => $subscription->currency ?? $this->currencyService->defaultCurrency(),
                'issued_at' => $issuedAt,
                'due_at' => Carbon::parse($issuedAt)->addDays($paymentTerms)->toDateString(),
                'notes' => __('finance::invoice.messages.subscription_initial_note', [
                    'name' => $subscription->name,
                    'date' => $billingDate,
                ]),
            ]);

            InvoiceLine::query()->create([
                'invoice_id' => $invoice->id,
                'service_id' => $subscription->service_id,
                'description' => $subscription->name,
                'quantity' => 1,
                'unit_price' => $amount,
                'tax_rate_id' => $defaultRate?->id,
                'tax_percent' => $lineAmounts['tax_percent'],
                'tax_amount' => $lineAmounts['tax_amount'],
                'amount' => $lineAmounts['amount'],
                'sort_order' => 0,
            ]);

            SubscriptionBilling::query()->create([
                'subscription_id' => $subscription->id,
                'billing_date' => $billingDate,
                'invoice_id' => $invoice->id,
            ]);

            return $invoice->load('lines', 'company');
        });

        InvoiceSentToCustomer::dispatch($invoice->fresh(['company', 'project', 'subscription']));

        return $invoice;
    }

    public function billSubscriptionRenewal(Subscription $subscription): ?Invoice
    {
        if (
            ! $subscription->renewal_at
            || $subscription->billing_cycle === Subscription::BILLING_ONE_TIME
            || ! $subscription->auto_renew
            || ! in_array($subscription->status, [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL], true)
        ) {
            return null;
        }

        if ($subscription->renewal_at->isFuture()) {
            return null;
        }

        if ($subscription->ends_at && $subscription->ends_at->lt($subscription->renewal_at)) {
            return null;
        }

        $billingDate = $subscription->renewal_at->toDateString();

        if (SubscriptionBilling::query()
            ->where('subscription_id', $subscription->id)
            ->whereDate('billing_date', $billingDate)
            ->exists()
        ) {
            return null;
        }

        $subscription->loadMissing(['company', 'service']);

        $invoice = DB::transaction(function () use ($subscription, $billingDate) {
            $amount = (float) $subscription->amount;
            $defaultRate = TaxRate::resolveDefault();
            $lineAmounts = $this->taxCalculationService->calculateLine(1, $amount, $defaultRate);
            $issuedAt = $billingDate;
            $paymentTerms = (int) config('finance.invoice_payment_terms_days', 30);

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'status' => Invoice::STATUS_SENT,
                'subtotal' => $lineAmounts['subtotal'],
                'tax_amount' => $lineAmounts['tax_amount'],
                'total' => $lineAmounts['amount'],
                'currency' => $subscription->currency ?? $this->currencyService->defaultCurrency(),
                'issued_at' => $issuedAt,
                'due_at' => Carbon::parse($issuedAt)->addDays($paymentTerms)->toDateString(),
                'notes' => __('finance::invoice.messages.subscription_renewal_note', [
                    'name' => $subscription->name,
                    'date' => $billingDate,
                ]),
            ]);

            InvoiceLine::query()->create([
                'invoice_id' => $invoice->id,
                'service_id' => $subscription->service_id,
                'description' => $subscription->name,
                'quantity' => 1,
                'unit_price' => $amount,
                'tax_rate_id' => $defaultRate?->id,
                'tax_percent' => $lineAmounts['tax_percent'],
                'tax_amount' => $lineAmounts['tax_amount'],
                'amount' => $lineAmounts['amount'],
                'sort_order' => 0,
            ]);

            SubscriptionBilling::query()->create([
                'subscription_id' => $subscription->id,
                'billing_date' => $billingDate,
                'invoice_id' => $invoice->id,
            ]);

            $this->subscriptionService->advanceRenewalDate($subscription);

            return $invoice->load('lines', 'company');
        });

        InvoiceSentToCustomer::dispatch($invoice->fresh(['company', 'project', 'subscription']));

        return $invoice;
    }

    public function createFromDeal(Deal $deal): ?Invoice
    {
        if ($deal->value === null || (float) $deal->value <= 0 || ! $deal->company_id) {
            return null;
        }

        $deal->loadMissing('services');

        $invoice = DB::transaction(function () use ($deal) {
            $lines = [];
            $sortOrder = 0;

            if ($deal->services->isNotEmpty()) {
                foreach ($deal->services as $service) {
                    $quantity = (int) $service->pivot->quantity;
                    $unitPrice = (float) $service->pivot->unit_price;
                    $lines[] = [
                        'service_id' => $service->id,
                        'description' => $service->getTranslation('title', app()->getLocale()),
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'amount' => round($quantity * $unitPrice, 2),
                        'sort_order' => $sortOrder++,
                    ];
                }
            } else {
                $lines[] = [
                    'description' => $deal->title,
                    'quantity' => 1,
                    'unit_price' => (float) $deal->value,
                    'amount' => (float) $deal->value,
                    'sort_order' => 0,
                ];
            }

            $subtotal = $this->sumLineAmounts($lines);
            $issuedAt = now()->toDateString();
            $paymentTerms = (int) config('finance.invoice_payment_terms_days', 30);

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'company_id' => $deal->company_id,
                'deal_id' => $deal->id,
                'status' => Invoice::STATUS_SENT,
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total' => $subtotal,
                'currency' => $deal->currency ?? $this->currencyService->defaultCurrency(),
                'issued_at' => $issuedAt,
                'due_at' => Carbon::parse($issuedAt)->addDays($paymentTerms)->toDateString(),
                'notes' => __('finance::invoice.messages.deal_invoice_note', ['title' => $deal->title]),
            ]);

            $this->syncLines($invoice, $lines);

            return $invoice->load('lines', 'company');
        });

        InvoiceSentToCustomer::dispatch($invoice->fresh(['company', 'project']));

        return $invoice;
    }

    public function createFromProductSale(ProductSale $sale): ?Invoice
    {
        if (! $sale->company_id || $sale->invoice_id) {
            return null;
        }

        $sale->loadMissing(['product', 'company', 'deal']);

        if (! $sale->product) {
            return null;
        }

        $invoice = DB::transaction(function () use ($sale) {
            $issuedAt = $sale->sold_at?->toDateString() ?? now()->toDateString();
            $paymentTerms = (int) config('finance.invoice_payment_terms_days', 30);
            $taxAmount = (float) $sale->tax_amount;
            $total = (float) $sale->total_amount;
            $subtotal = round($total - $taxAmount, 2);

            $sale->loadMissing('taxRate');

            $lines = [[
                'product_id' => $sale->product_id,
                'description' => $sale->product->name,
                'quantity' => $sale->quantity,
                'unit_price' => (float) $sale->unit_price,
                'tax_rate_id' => $sale->tax_rate_id,
                'tax_percent' => $sale->taxRate ? (float) $sale->taxRate->percentage : 0,
                'tax_amount' => $taxAmount,
                'amount' => $total,
                'sort_order' => 0,
            ]];

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'company_id' => $sale->company_id,
                'deal_id' => $sale->deal_id,
                'status' => Invoice::STATUS_SENT,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'currency' => $sale->currency ?? $this->currencyService->defaultCurrency(),
                'issued_at' => $issuedAt,
                'due_at' => Carbon::parse($issuedAt)->addDays($paymentTerms)->toDateString(),
                'notes' => __('finance::invoice.messages.product_sale_invoice_note', [
                    'product' => $sale->product->name,
                    'quantity' => $sale->quantity,
                ]),
            ]);

            $this->syncLines($invoice, $lines);

            $sale->update(['invoice_id' => $invoice->id]);

            return $invoice->load('lines', 'company');
        });

        InvoiceSentToCustomer::dispatch($invoice->fresh(['company', 'project']));

        return $invoice;
    }

    public function markAsSent(Invoice $invoice): Invoice
    {
        if ($invoice->status === Invoice::STATUS_VOID) {
            return $invoice;
        }

        $wasSent = $invoice->status === Invoice::STATUS_SENT;

        $invoice->update(['status' => Invoice::STATUS_SENT]);
        $invoice = $invoice->fresh(['company', 'project']);

        if (! $wasSent) {
            InvoiceSentToCustomer::dispatch($invoice);
        }

        return $invoice;
    }

    public function markAsPaid(Invoice $invoice, ?string $paidAt = null): Invoice
    {
        if (in_array($invoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_VOID], true)) {
            return $invoice;
        }

        $paidAt = $paidAt ?? now()->toDateString();

        DB::transaction(function () use ($invoice, $paidAt) {
            $invoice->update([
                'status' => Invoice::STATUS_PAID,
                'paid_at' => $paidAt,
            ]);

            $this->financeService->recordInvoicePayment($invoice->fresh(['company']), $paidAt);

            if ($invoice->project_id) {
                $this->financeService->updateProjectPaymentStatus($invoice->project_id);
            }
        });

        return $invoice->fresh();
    }

    public function voidInvoice(Invoice $invoice): Invoice
    {
        if ($invoice->status === Invoice::STATUS_PAID) {
            return $invoice;
        }

        $projectId = $invoice->project_id;

        $invoice->update(['status' => Invoice::STATUS_VOID]);

        $this->taxLedgerService->reverseForSource(Invoice::class, $invoice->id);

        if ($projectId) {
            $this->financeService->updateProjectPaymentStatus($projectId);
        }

        return $invoice->fresh();
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        $projectId = $invoice->project_id;

        DB::transaction(function () use ($invoice) {
            $this->financeService->deleteJournalEntriesForReference(Invoice::class, $invoice->id);
            $invoice->delete();
        });

        if ($projectId) {
            $this->financeService->updateProjectPaymentStatus($projectId);
        }
    }

    public function markOverdueInvoices(): int
    {
        return Invoice::query()
            ->where('status', Invoice::STATUS_SENT)
            ->whereDate('due_at', '<', now()->toDateString())
            ->update(['status' => Invoice::STATUS_OVERDUE]);
    }

    public function processDueSubscriptionRenewals(): int
    {
        $this->markOverdueInvoices();

        $count = 0;

        Subscription::query()
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
            ->where('auto_renew', true)
            ->where('billing_cycle', '!=', Subscription::BILLING_ONE_TIME)
            ->whereNotNull('renewal_at')
            ->whereDate('renewal_at', '<=', now()->toDateString())
            ->orderBy('id')
            ->chunkById(50, function ($subscriptions) use (&$count) {
                foreach ($subscriptions as $subscription) {
                    if ($this->billSubscriptionRenewal($subscription)) {
                        $count++;
                    }
                }
            });

        return $count;
    }

    public function generateInvoiceNumber(): string
    {
        $prefix = config('finance.invoice_number_prefix', 'INV');
        $year = now()->format('Y');

        $lastNumber = Invoice::query()
            ->where('invoice_number', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value('invoice_number');

        $sequence = 1;

        if ($lastNumber && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        $invoice->load(['company', 'lines', 'subscription']);

        $pdf = Pdf::loadView('finance::admin.invoice.pdf', [
            'invoice' => $invoice,
            'companyBranding' => CompanyBranding::forInvoice(),
        ]);

        return $pdf->download($invoice->invoice_number.'.pdf');
    }

    private function syncLines(Invoice $invoice, array $lines): void
    {
        $invoice->lines()->delete();

        if ($lines === []) {
            return;
        }

        $now = now();
        InvoiceLine::query()->insert(array_map(function (array $line, int $index) use ($invoice, $now) {
            $quantity = (int) ($line['quantity'] ?? 1);
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $amount = array_key_exists('amount', $line)
                ? (float) $line['amount']
                : round($quantity * $unitPrice, 2);

            return [
                'invoice_id' => $invoice->id,
                'service_id' => $line['service_id'] ?? null,
                'product_id' => $line['product_id'] ?? null,
                'description' => $line['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_rate_id' => $line['tax_rate_id'] ?? null,
                'tax_percent' => (float) ($line['tax_percent'] ?? 0),
                'tax_amount' => (float) ($line['tax_amount'] ?? 0),
                'amount' => $amount,
                'sort_order' => $line['sort_order'] ?? $index,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $lines, array_keys($lines)));
    }

    private function sumLineAmounts(array $lines): float
    {
        return round(collect($lines)->sum(function ($line) {
            if (array_key_exists('amount', $line)) {
                return (float) $line['amount'];
            }

            return (int) ($line['quantity'] ?? 1) * (float) ($line['unit_price'] ?? 0);
        }), 2);
    }
}
