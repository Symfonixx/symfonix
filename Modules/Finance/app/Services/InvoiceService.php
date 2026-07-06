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
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\SubscriptionBilling;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService
{
    public function __construct(
        private readonly FinanceService $financeService,
        private readonly SubscriptionService $subscriptionService,
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
            $subtotal = $this->sumLineAmounts($lines);
            $taxAmount = (float) ($data['tax_amount'] ?? 0);
            $total = round($subtotal + $taxAmount, 2);
            $issuedAt = $data['issued_at'] ?? now()->toDateString();
            $paymentTerms = (int) config('finance.invoice_payment_terms_days', 30);

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'company_id' => $data['company_id'],
                'deal_id' => $data['deal_id'] ?? null,
                'status' => Invoice::STATUS_DRAFT,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'currency' => strtoupper($data['currency'] ?? config('finance.default_currency', 'USD')),
                'issued_at' => $issuedAt,
                'due_at' => $data['due_at'] ?? Carbon::parse($issuedAt)->addDays($paymentTerms)->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncLines($invoice, $lines);

            return $invoice->load('lines');
        });
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

        return DB::transaction(function () use ($subscription, $billingDate) {
            $amount = (float) $subscription->amount;
            $issuedAt = $billingDate;
            $paymentTerms = (int) config('finance.invoice_payment_terms_days', 30);

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'status' => Invoice::STATUS_SENT,
                'subtotal' => $amount,
                'tax_amount' => 0,
                'total' => $amount,
                'currency' => $subscription->currency ?? config('finance.default_currency', 'USD'),
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
                'amount' => $amount,
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
    }

    public function createFromDeal(Deal $deal): ?Invoice
    {
        if ($deal->value === null || (float) $deal->value <= 0 || ! $deal->company_id) {
            return null;
        }

        $deal->loadMissing('services');

        return DB::transaction(function () use ($deal) {
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
                'currency' => $deal->currency ?? config('finance.default_currency', 'USD'),
                'issued_at' => $issuedAt,
                'due_at' => Carbon::parse($issuedAt)->addDays($paymentTerms)->toDateString(),
                'notes' => __('finance::invoice.messages.deal_invoice_note', ['title' => $deal->title]),
            ]);

            $this->syncLines($invoice, $lines);

            return $invoice->load('lines', 'company');
        });
    }

    public function markAsSent(Invoice $invoice): Invoice
    {
        if ($invoice->status === Invoice::STATUS_VOID) {
            return $invoice;
        }

        $invoice->update(['status' => Invoice::STATUS_SENT]);

        return $invoice->fresh();
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
        });

        return $invoice->fresh();
    }

    public function voidInvoice(Invoice $invoice): Invoice
    {
        if ($invoice->status === Invoice::STATUS_PAID) {
            return $invoice;
        }

        $invoice->update(['status' => Invoice::STATUS_VOID]);

        return $invoice->fresh();
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

        foreach ($lines as $index => $line) {
            $quantity = (int) ($line['quantity'] ?? 1);
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $amount = array_key_exists('amount', $line)
                ? (float) $line['amount']
                : round($quantity * $unitPrice, 2);

            InvoiceLine::query()->create([
                'invoice_id' => $invoice->id,
                'service_id' => $line['service_id'] ?? null,
                'description' => $line['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'amount' => $amount,
                'sort_order' => $line['sort_order'] ?? $index,
            ]);
        }
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
