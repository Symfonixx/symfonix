<?php

namespace Modules\Finance\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Subscription;
use Modules\Finance\Events\JournalEntryPosted;
use Modules\Finance\Models\Commission;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Models\Salary;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSale;
use Modules\Project\Events\ProjectPaymentStatusChanged;
use Modules\Project\Models\Project;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxCalculationService;

class FinanceService
{
    public function __construct(
        private readonly CurrencyService $currencyService
    ) {}

    /**
     * Sum all revenue converted into the active display currency.
     */
    public function getTotalIncome(?array $dateRange = null, ?array $monthKeys = null): float
    {
        return $this->sumJournalAmountInDisplayCurrency(
            $this->baseJournalEntryQuery($dateRange, $monthKeys)->revenue()
        );
    }

    /**
     * Sum all expenses converted into the active display currency.
     */
    public function getTotalExpenses(?array $dateRange = null, ?array $monthKeys = null): float
    {
        return $this->sumJournalAmountInDisplayCurrency(
            $this->baseJournalEntryQuery($dateRange, $monthKeys)->expense()
        );
    }

    /**
     * @return array{revenue: float, expenses: float, profit: float, losses: float}
     */
    public function getMetricsSummary(?array $monthKeys = null): array
    {
        $revenue = $this->getTotalIncome(monthKeys: $monthKeys);
        $expenses = $this->getTotalExpenses(monthKeys: $monthKeys);
        $profit = $revenue - $expenses;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'losses' => max(0, $expenses - $revenue),
        ];
    }

    /**
     * @return array<int, array{key: string, label: string}>
     */
    public function getAvailableMonths(): array
    {
        return JournalEntry::query()
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->map(function ($row) {
                $date = Carbon::create((int) $row->year, (int) $row->month, 1);

                return [
                    'key' => $date->format('Y-m'),
                    'label' => $date->translatedFormat('F Y'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, profit: float, expenses: float, losses: float, revenue: float}>
     */
    public function getMonthlyChartData(?array $monthKeys = null): array
    {
        $query = JournalEntry::query()->selectRaw(
            'flow, currency, exchange_rate, YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(amount) as amount, SUM(base_amount) as base_amount, COUNT(*) as row_count, SUM(CASE WHEN base_amount IS NOT NULL THEN 1 ELSE 0 END) as base_count'
        );

        $this->applyMonthFilter($query, $monthKeys);

        $grouped = [];

        foreach ($query->groupByRaw('flow, currency, exchange_rate, YEAR(transaction_date), MONTH(transaction_date)')->get() as $row) {
            $key = sprintf('%04d-%02d', (int) $row->year, (int) $row->month);

            if (! isset($grouped[$key])) {
                $date = Carbon::create((int) $row->year, (int) $row->month, 1);
                $grouped[$key] = [
                    'label' => $date->translatedFormat('M Y'),
                    'sort' => $key,
                    'revenue' => 0.0,
                    'expenses' => 0.0,
                ];
            }

            $amount = $this->groupedAmountInDisplayCurrency($row);

            if ($row->flow === JournalEntry::FLOW_REVENUE) {
                $grouped[$key]['revenue'] += $amount;
            } else {
                $grouped[$key]['expenses'] += $amount;
            }
        }

        ksort($grouped);

        return collect($grouped)
            ->map(function (array $row) {
                $revenue = round($row['revenue'], 2);
                $expenses = round($row['expenses'], 2);
                $net = $revenue - $expenses;

                return [
                    'label' => $row['label'],
                    'profit' => max(0, $net),
                    'expenses' => $expenses,
                    'losses' => max(0, -$net),
                    'revenue' => $revenue,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, int>
     */
    public function getAvailableYears(): array
    {
        $years = JournalEntry::query()
            ->selectRaw('YEAR(transaction_date) as year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->values()
            ->all();

        if ($years === []) {
            return [(int) now()->year];
        }

        return $years;
    }

    /**
     * @return array<int, array{label: string, month: int, revenue: float, expenses: float}>
     */
    public function getMonthlyTrendForYear(int $year): array
    {
        $rows = JournalEntry::query()
            ->whereYear('transaction_date', $year)
            ->selectRaw(
                'flow, currency, exchange_rate, MONTH(transaction_date) as month, SUM(amount) as amount, SUM(base_amount) as base_amount, COUNT(*) as row_count, SUM(CASE WHEN base_amount IS NOT NULL THEN 1 ELSE 0 END) as base_count'
            )
            ->groupByRaw('flow, currency, exchange_rate, MONTH(transaction_date)')
            ->get();

        $byMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            $byMonth[$month] = ['revenue' => 0.0, 'expenses' => 0.0];
        }

        foreach ($rows as $row) {
            $month = (int) $row->month;
            $amount = $this->groupedAmountInDisplayCurrency($row);

            if ($row->flow === JournalEntry::FLOW_REVENUE) {
                $byMonth[$month]['revenue'] += $amount;
            } else {
                $byMonth[$month]['expenses'] += $amount;
            }
        }

        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($year, $month, 1);

            $months[] = [
                'label' => $date->translatedFormat('M'),
                'month' => $month,
                'revenue' => round($byMonth[$month]['revenue'], 2),
                'expenses' => round($byMonth[$month]['expenses'], 2),
            ];
        }

        return $months;
    }

    public function updateProjectPaymentStatus(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);
        $budget = (float) ($project->budget ?? 0);
        $invoiced = $this->sumProjectInvoicedAmount($project);
        $remaining = max(0, round($budget - $invoiced, 2));
        $previousStatus = $project->payment_status;

        if ($budget <= 0 || $invoiced <= 0) {
            $status = Project::PAYMENT_UNPAID;
        } elseif ($remaining <= 0) {
            $status = Project::PAYMENT_FULLY_PAID;
        } else {
            $status = Project::PAYMENT_PARTIALLY_PAID;
        }

        $project->update(['payment_status' => $status]);

        if ($previousStatus !== $status) {
            ProjectPaymentStatusChanged::dispatch($project->fresh(), $previousStatus, $status);
        }
    }

    /**
     * @return array{invoiced: float, remaining: float, budget: float, currency: string, payment_status: string, collection_rate: float, collected: float}
     */
    public function getProjectCollectionSummary(Project $project): array
    {
        $project->loadMissing('deal');

        $invoiced = $this->sumProjectInvoicedAmount($project);
        $budget = (float) ($project->budget ?? 0);
        $remaining = max(0, round($budget - $invoiced, 2));
        $currency = $project->currency
            ?? $project->deal?->currency
            ?? $this->defaultCurrency();
        $collected = $this->sumProjectRelatedIncome($project);

        return [
            'invoiced' => $invoiced,
            'remaining' => $remaining,
            'collected' => $collected,
            'budget' => $budget,
            'currency' => $currency,
            'payment_status' => $project->payment_status,
            'collection_rate' => $budget > 0 ? min(100, round(($invoiced / $budget) * 100, 1)) : 0.0,
        ];
    }

    public function sumProjectInvoicedAmount(Project $project): float
    {
        return round((float) Invoice::query()
            ->where('project_id', $project->id)
            ->where('status', '!=', Invoice::STATUS_VOID)
            ->sum('total'), 2);
    }

    public function resolveDealRecognizedRevenue(Deal $deal): float
    {
        $serviceTotal = $this->sumDealServiceLineItems($deal);

        if ($serviceTotal > 0) {
            return $serviceTotal;
        }

        return (float) ($deal->value ?? 0);
    }

    public function recordDealWonFinance(Deal $deal): void
    {
        $deal->loadMissing(['project', 'assignee', 'services']);

        $recognizedRevenue = $this->resolveDealRecognizedRevenue($deal);

        if ($recognizedRevenue <= 0) {
            return;
        }

        DB::transaction(function () use ($deal, $recognizedRevenue) {
            $this->recordDealIncome($deal, $recognizedRevenue);
            $this->ensureDealCommission($deal, $recognizedRevenue);

            if ($deal->project) {
                $this->updateProjectPaymentStatus($deal->project->id);
            }
        });
    }

    /**
     * @return array{
     *     expected_revenue: float,
     *     ledger_total: float,
     *     currency: string,
     *     variance: float,
     *     is_reconciled: bool,
     *     transactions: Collection<int, JournalEntry>
     * }
     */
    public function getDealLedgerSummary(Deal $deal): array
    {
        $expectedRevenue = $this->resolveDealRecognizedRevenue($deal);
        $ledgerTotal = $this->sumDealLedgerIncome($deal);
        $currency = $deal->currency ?? $this->defaultCurrency();
        $variance = round($expectedRevenue - $ledgerTotal, 2);

        return [
            'expected_revenue' => $expectedRevenue,
            'ledger_total' => $ledgerTotal,
            'currency' => $currency,
            'variance' => $variance,
            'is_reconciled' => abs($variance) < 0.01,
            'transactions' => $this->getDealIncomeJournalEntries($deal),
        ];
    }

    public function sumDealLedgerIncome(Deal $deal): float
    {
        $total = (float) JournalEntry::query()
            ->revenue()
            ->where('reference_type', Deal::class)
            ->where('reference_id', $deal->id)
            ->sum('amount');

        $productSaleIds = ProductSale::query()
            ->where('deal_id', $deal->id)
            ->pluck('id');

        if ($productSaleIds->isNotEmpty()) {
            $total += (float) JournalEntry::query()
                ->revenue()
                ->where('reference_type', ProductSale::class)
                ->whereIn('reference_id', $productSaleIds)
                ->sum('amount');
        }

        return round($total, 2);
    }

    public function recordInvoicePayment(Invoice $invoice, ?string $paidAt = null): void
    {
        $paidAt = $paidAt ?? now()->toDateString();

        if ($this->journalEntryExists(Invoice::class, $invoice->id)) {
            return;
        }

        if (ProductSale::query()->where('invoice_id', $invoice->id)->exists()) {
            return;
        }

        $invoice->loadMissing('company');

        $this->postRevenueEntry([
            'amount' => (float) $invoice->total,
            'currency' => $invoice->currency ?? $this->defaultCurrency(),
            'reference_type' => Invoice::class,
            'reference_id' => $invoice->id,
            'description' => __('finance::invoice.messages.payment_description', [
                'number' => $invoice->invoice_number,
                'company' => $invoice->company?->name ?? '#'.$invoice->company_id,
            ]),
            'transaction_date' => $paidAt,
        ]);
    }

    public function getSubscriptionMetrics(): array
    {
        $subscriptions = Subscription::query()
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
            ->where('billing_cycle', '!=', Subscription::BILLING_ONE_TIME)
            ->get(['amount', 'currency', 'billing_cycle']);

        $totals = [];

        foreach ($subscriptions as $subscription) {
            $currency = $subscription->currency ?? $this->defaultCurrency();
            $mrr = $this->subscriptionAmountToMrr($subscription);

            if (! isset($totals[$currency])) {
                $totals[$currency] = ['mrr' => 0.0, 'arr' => 0.0, 'active_count' => 0];
            }

            $totals[$currency]['mrr'] += $mrr;
            $totals[$currency]['active_count']++;
        }

        foreach ($totals as $currency => $data) {
            $totals[$currency]['mrr'] = round($data['mrr'], 2);
            $totals[$currency]['arr'] = round($data['mrr'] * 12, 2);
        }

        $primaryCurrency = $this->currencyService->displayCurrency();
        $primaryMrr = 0.0;
        $primaryCount = 0;

        foreach ($totals as $currency => $data) {
            $primaryMrr += $this->currencyService->convert($data['mrr'], $currency, $primaryCurrency);
            $primaryCount += $data['active_count'];
        }

        return [
            'totals' => $totals,
            'primary_currency' => $primaryCurrency,
            'mrr' => round($primaryMrr, 2),
            'arr' => round($primaryMrr * 12, 2),
            'active_count' => $primaryCount,
        ];
    }

    public function getAccountsReceivableAging(?string $currency = null): array
    {
        $currency = $currency ?? $this->defaultCurrency();

        $invoices = Invoice::query()
            ->open()
            ->where('currency', $currency)
            ->with('company:id,name')
            ->orderBy('due_at')
            ->get();

        $projects = Project::query()
            ->whereNotNull('budget')
            ->where('budget', '>', 0)
            ->whereIn('payment_status', [Project::PAYMENT_UNPAID, Project::PAYMENT_PARTIALLY_PAID])
            ->with(['company:id,name', 'deal:id,currency'])
            ->orderBy('due_date')
            ->get();

        $invoicedByProject = Invoice::query()
            ->whereIn('project_id', $projects->pluck('id'))
            ->where('status', '!=', Invoice::STATUS_VOID)
            ->selectRaw('project_id, COALESCE(SUM(total), 0) as invoiced')
            ->groupBy('project_id')
            ->pluck('invoiced', 'project_id');

        $projects = $projects
            ->map(function (Project $project) use ($invoicedByProject) {
                $invoiced = round((float) ($invoicedByProject[$project->id] ?? 0), 2);
                $remaining = max(0, round((float) $project->budget - $invoiced, 2));

                return [
                    'project' => $project,
                    'remaining' => $remaining,
                    'currency' => $project->currency
                        ?? $project->deal?->currency
                        ?? $this->defaultCurrency(),
                    'payment_status' => $project->payment_status,
                ];
            })
            ->filter(fn (array $row) => $row['remaining'] > 0 && $row['currency'] === $currency)
            ->values();

        $buckets = [
            'current' => 0.0,
            'days_1_30' => 0.0,
            'days_31_60' => 0.0,
            'days_61_90' => 0.0,
            'over_90' => 0.0,
        ];

        foreach ($invoices as $invoice) {
            $bucket = $this->resolveAgingBucket($invoice);
            $buckets[$bucket] += (float) $invoice->total;
        }

        foreach ($projects as $row) {
            $bucket = $this->resolveProjectAgingBucket($row['project']);
            $buckets[$bucket] += (float) $row['remaining'];
        }

        foreach ($buckets as $key => $amount) {
            $buckets[$key] = round($amount, 2);
        }

        return [
            'buckets' => $buckets,
            'total_outstanding' => round(array_sum($buckets), 2),
            'currency' => $currency,
            'invoices' => $invoices,
            'projects' => $projects,
        ];
    }

    public function recordSalaryPayout(Salary $salary, ?string $paidAt = null): void
    {
        if ($salary->status === Salary::STATUS_PAID) {
            return;
        }

        $paidAt = $paidAt ?? now()->toDateString();

        DB::transaction(function () use ($salary, $paidAt) {
            $category = $this->resolveExpenseCategory('salaries');

            $this->postExpenseEntry([
                'amount' => (float) $salary->base_salary,
                'currency' => $this->defaultCurrency(),
                'reference_type' => Salary::class,
                'reference_id' => $salary->id,
                'description' => __('finance::salary.messages.payout_description', [
                    'name' => $salary->employee?->name ?? '#'.$salary->employee_id,
                    'period' => $salary->period?->format('Y-m') ?? '',
                ]),
                'transaction_date' => $paidAt,
                'expense_category_id' => $category?->id,
            ]);

            $salary->update([
                'status' => Salary::STATUS_PAID,
                'paid_at' => $paidAt,
            ]);
        });
    }

    public function deleteSalary(Salary $salary): void
    {
        DB::transaction(function () use ($salary) {
            $this->deleteJournalEntriesFor(Salary::class, $salary->id);
            $salary->delete();
        });
    }

    public function recordCommissionPayout(Commission $commission): void
    {
        if ($commission->status === Commission::STATUS_PAID) {
            return;
        }

        DB::transaction(function () use ($commission) {
            $category = $this->resolveExpenseCategory('commissions');
            $commission->loadMissing('deal');

            $this->postExpenseEntry([
                'amount' => (float) $commission->commission_amount,
                'currency' => $commission->deal?->currency ?? $this->defaultCurrency(),
                'reference_type' => Commission::class,
                'reference_id' => $commission->id,
                'description' => __('finance::commission.messages.payout_description', [
                    'name' => $commission->employee?->name ?? '#'.$commission->employee_id,
                    'deal' => $commission->deal?->title ?? '#'.$commission->deal_id,
                ]),
                'transaction_date' => now()->toDateString(),
                'expense_category_id' => $category?->id,
            ]);

            $commission->update(['status' => Commission::STATUS_PAID]);
        });
    }

    public function recordProductSale(array $data, bool $skipIfExists = false): ?ProductSale
    {
        $product = Product::query()->findOrFail($data['product_id']);
        $quantity = (int) ($data['quantity'] ?? 1);
        $unitPrice = array_key_exists('unit_price', $data) && $data['unit_price'] !== null
            ? (float) $data['unit_price']
            : (float) $product->price;

        $taxRate = ! empty($data['tax_rate_id'])
            ? TaxRate::query()->active()->find((int) $data['tax_rate_id'])
            : ($product->tax_rate_id ? TaxRate::query()->active()->find($product->tax_rate_id) : null);

        $amounts = app(TaxCalculationService::class)->calculateLine($quantity, $unitPrice, $taxRate);
        $totalAmount = $amounts['amount'];
        $taxAmount = $amounts['tax_amount'];

        if ($totalAmount <= 0) {
            return null;
        }

        if ($skipIfExists && ! empty($data['deal_id'])) {
            $existingSale = ProductSale::query()
                ->where('deal_id', $data['deal_id'])
                ->where('product_id', $product->id)
                ->first();

            if ($existingSale) {
                return $existingSale;
            }
        }

        return DB::transaction(function () use ($data, $product, $quantity, $totalAmount, $taxRate, $taxAmount, $unitPrice) {
            $sale = ProductSale::query()->create([
                'product_id' => $product->id,
                'company_id' => $data['company_id'] ?? null,
                'deal_id' => $data['deal_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_rate_id' => $taxRate?->id,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'currency' => $data['currency'] ?? $product->currency,
                'notes' => $data['notes'] ?? null,
                'sold_at' => $data['sold_at'] ?? now()->toDateString(),
            ]);

            if (! $this->journalEntryExists(ProductSale::class, $sale->id)) {
                $this->postRevenueEntry([
                    'amount' => $totalAmount,
                    'currency' => $data['currency'] ?? $product->currency ?? $this->defaultCurrency(),
                    'reference_type' => ProductSale::class,
                    'reference_id' => $sale->id,
                    'description' => __('finance::product_sale.messages.income', [
                        'product' => $product->name,
                        'quantity' => $quantity,
                    ]),
                    'transaction_date' => $sale->sold_at->toDateString(),
                ]);
            }

            return $sale;
        });
    }

    public function deleteProductSale(ProductSale $sale): void
    {
        $dealId = $sale->deal_id;
        $invoiceId = $sale->invoice_id;

        DB::transaction(function () use ($sale, $invoiceId) {
            $this->deleteJournalEntriesFor(ProductSale::class, $sale->id);

            if ($invoiceId) {
                $invoice = Invoice::query()->find($invoiceId);

                if ($invoice && ! in_array($invoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_VOID], true)) {
                    $invoice->update(['status' => Invoice::STATUS_VOID]);
                }
            }

            $sale->delete();
        });

        if ($dealId) {
            $project = Project::query()->where('deal_id', $dealId)->first();

            if ($project) {
                $this->updateProjectPaymentStatus($project->id);
            }
        }
    }

    /**
     * Log a manual journal entry from the daily log.
     *
     * @param  array{flow: string, amount: float|string, currency?: string, expense_category_id?: int, reference_type?: string, reference_id?: int, description?: string, transaction_date?: string}  $data
     */
    public function logTransaction(array $data): JournalEntry
    {
        $flow = $this->normalizeFlow($data['flow'] ?? $data['type'] ?? JournalEntry::FLOW_EXPENSE);
        $entryData = [
            'amount' => (float) $data['amount'],
            'currency' => $data['currency'] ?? $this->defaultCurrency(),
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'description' => $data['description'] ?? null,
            'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
            'expense_category_id' => $data['expense_category_id'] ?? null,
            'tax_rate_id' => $data['tax_rate_id'] ?? null,
            'tax_amount' => (float) ($data['tax_amount'] ?? 0),
        ];

        $entry = $flow === JournalEntry::FLOW_REVENUE
            ? $this->postRevenueEntry($entryData)
            : $this->postExpenseEntry($entryData);

        if (
            $flow === JournalEntry::FLOW_REVENUE
            && ($data['reference_type'] ?? null) === Project::class
            && ! empty($data['reference_id'])
        ) {
            $this->updateProjectPaymentStatus((int) $data['reference_id']);
        }

        return $entry;
    }

    /**
     * Delete a manually logged journal entry from the daily log.
     * System-generated entries (salary, product sale, invoice, deal, etc.) are rejected.
     */
    public function deleteJournalEntry(JournalEntry $entry): void
    {
        if ($entry->reference_type && $entry->reference_type !== Project::class) {
            throw new \InvalidArgumentException(
                __('finance::finance.messages.cannot_delete_system_entry')
            );
        }

        $projectId = (
            $entry->flow === JournalEntry::FLOW_REVENUE
            && $entry->reference_type === Project::class
            && $entry->reference_id
        ) ? (int) $entry->reference_id : null;

        DB::transaction(function () use ($entry) {
            $entry->lines()->delete();
            $entry->delete();
        });

        if ($projectId) {
            $this->updateProjectPaymentStatus($projectId);
        }
    }

    /**
     * Post a balanced revenue entry: Debit Cash, Credit Revenue.
     */
    public function postRevenueEntry(array $data): JournalEntry
    {
        return $this->postBalancedEntry($data, JournalEntry::FLOW_REVENUE);
    }

    /**
     * Post a balanced expense entry: Debit Expense, Credit Cash.
     */
    public function postExpenseEntry(array $data): JournalEntry
    {
        return $this->postBalancedEntry($data, JournalEntry::FLOW_EXPENSE);
    }

    private function postBalancedEntry(array $data, string $flow): JournalEntry
    {
        $amount = round((float) $data['amount'], 2);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Journal entry amount must be greater than zero.');
        }

        return DB::transaction(function () use ($data, $flow, $amount) {
            $currency = strtoupper((string) ($data['currency'] ?? $this->defaultCurrency()));
            $exchangeRate = isset($data['exchange_rate'])
                ? (float) $data['exchange_rate']
                : $this->currencyService->snapshotRateToBase($currency);
            $baseAmount = isset($data['base_amount'])
                ? round((float) $data['base_amount'], 2)
                : round($amount * $exchangeRate, 2);

            $entry = JournalEntry::query()->create([
                'flow' => $flow,
                'amount' => $amount,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'base_amount' => $baseAmount,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'description' => $data['description'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                'tax_rate_id' => $data['tax_rate_id'] ?? null,
                'tax_amount' => round((float) ($data['tax_amount'] ?? 0), 2),
            ]);

            if ($flow === JournalEntry::FLOW_REVENUE) {
                $this->createLine($entry, JournalLine::SIDE_DEBIT, JournalLine::ACCOUNT_CASH, $amount);
                $this->createLine($entry, JournalLine::SIDE_CREDIT, JournalLine::ACCOUNT_REVENUE, $amount);
            } else {
                $this->createLine(
                    $entry,
                    JournalLine::SIDE_DEBIT,
                    JournalLine::ACCOUNT_EXPENSE,
                    $amount,
                    $data['expense_category_id'] ?? null
                );
                $this->createLine($entry, JournalLine::SIDE_CREDIT, JournalLine::ACCOUNT_CASH, $amount);
            }

            $entry = $entry->load('lines');

            JournalEntryPosted::dispatch($entry);

            return $entry;
        });
    }

    private function createLine(
        JournalEntry $entry,
        string $side,
        string $account,
        float $amount,
        ?int $expenseCategoryId = null
    ): JournalLine {
        return JournalLine::query()->create([
            'journal_entry_id' => $entry->id,
            'side' => $side,
            'account' => $account,
            'expense_category_id' => $expenseCategoryId,
            'amount' => $amount,
        ]);
    }

    private function normalizeFlow(string $flow): string
    {
        return in_array($flow, ['credit', 'revenue', JournalEntry::FLOW_REVENUE], true)
            ? JournalEntry::FLOW_REVENUE
            : JournalEntry::FLOW_EXPENSE;
    }

    private function journalEntryExists(string $referenceType, int $referenceId): bool
    {
        return JournalEntry::query()
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->exists();
    }

    public function deleteJournalEntriesForReference(string $referenceType, int $referenceId): void
    {
        $this->deleteJournalEntriesFor($referenceType, $referenceId);
    }

    private function deleteJournalEntriesFor(string $referenceType, int $referenceId): void
    {
        $ids = JournalEntry::query()
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        JournalLine::query()->whereIn('journal_entry_id', $ids)->delete();
        JournalEntry::query()->whereIn('id', $ids)->delete();
    }

    private function baseJournalEntryQuery(?array $dateRange = null, ?array $monthKeys = null)
    {
        $query = JournalEntry::query();

        if ($dateRange) {
            if (! empty($dateRange['from'])) {
                $query->whereDate('transaction_date', '>=', $dateRange['from']);
            }
            if (! empty($dateRange['to'])) {
                $query->whereDate('transaction_date', '<=', $dateRange['to']);
            }
        }

        $this->applyMonthFilter($query, $monthKeys);

        return $query;
    }

    private function applyMonthFilter($query, ?array $monthKeys): void
    {
        if (empty($monthKeys)) {
            return;
        }

        $query->where(function ($builder) use ($monthKeys) {
            foreach ($monthKeys as $monthKey) {
                [$year, $month] = explode('-', $monthKey);

                $builder->orWhere(function ($monthQuery) use ($year, $month) {
                    $monthQuery->whereYear('transaction_date', (int) $year)
                        ->whereMonth('transaction_date', (int) $month);
                });
            }
        });
    }

    private function resolveExpenseCategory(string $slug): ?ExpenseCategory
    {
        return ExpenseCategory::query()->where('slug', $slug)->first();
    }

    private function defaultCurrency(): string
    {
        return $this->currencyService->defaultCurrency();
    }

    private function sumJournalAmountInDisplayCurrency($query): float
    {
        $rows = $query
            ->selectRaw('currency, exchange_rate, SUM(amount) as amount, SUM(base_amount) as base_amount, COUNT(*) as row_count, SUM(CASE WHEN base_amount IS NOT NULL THEN 1 ELSE 0 END) as base_count')
            ->groupBy('currency', 'exchange_rate')
            ->get();

        $total = 0.0;

        foreach ($rows as $row) {
            $total += $this->groupedAmountInDisplayCurrency($row);
        }

        return round($total, 2);
    }

    private function groupedAmountInDisplayCurrency(object $row): float
    {
        $entry = new JournalEntry([
            'amount' => $row->amount,
            'currency' => $row->currency,
            'exchange_rate' => $row->exchange_rate,
            'base_amount' => (int) ($row->base_count ?? 0) === (int) ($row->row_count ?? 0) ? $row->base_amount : null,
        ]);

        return $this->entryAmountInDisplayCurrency($entry);
    }

    private function entryAmountInDisplayCurrency(JournalEntry $entry): float
    {
        if ($entry->base_amount !== null) {
            return $this->currencyService->convertFromBaseAmount((float) $entry->base_amount);
        }

        if ($entry->exchange_rate !== null) {
            return $this->currencyService->convertUsingHistoricalRate(
                (float) $entry->amount,
                (string) $entry->currency,
                (float) $entry->exchange_rate
            );
        }

        return $this->currencyService->convert(
            (float) $entry->amount,
            (string) ($entry->currency ?: $this->defaultCurrency())
        );
    }

    private function sumDealServiceLineItems(Deal $deal): float
    {
        $deal->loadMissing('services');

        if ($deal->services->isEmpty()) {
            return 0.0;
        }

        return round($deal->services->sum(
            fn ($service) => (int) $service->pivot->quantity * (float) $service->pivot->unit_price
        ), 2);
    }

    private function recordDealIncome(Deal $deal, float $amount): void
    {
        if ($this->journalEntryExists(Deal::class, $deal->id)) {
            return;
        }

        $this->postRevenueEntry([
            'amount' => $amount,
            'currency' => $deal->currency ?? $this->defaultCurrency(),
            'reference_type' => Deal::class,
            'reference_id' => $deal->id,
            'description' => __('finance::finance.messages.sale_income', ['title' => $deal->title]),
            'transaction_date' => now()->toDateString(),
        ]);
    }

    private function ensureDealCommission(Deal $deal, float $baseAmount): void
    {
        if (! $deal->assigned_to) {
            return;
        }

        $percentage = (float) config('finance.default_commission_percentage', 10);

        Commission::query()->firstOrCreate(
            ['deal_id' => $deal->id],
            [
                'employee_id' => $deal->assigned_to,
                'commission_percentage' => $percentage,
                'commission_amount' => round($baseAmount * ($percentage / 100), 2),
                'status' => Commission::STATUS_PENDING,
            ]
        );
    }

    private function sumProjectRelatedIncome(Project $project): float
    {
        $total = (float) JournalEntry::query()
            ->revenue()
            ->where('reference_type', Project::class)
            ->where('reference_id', $project->id)
            ->sum('amount');

        if ($project->deal_id) {
            $total += (float) JournalEntry::query()
                ->revenue()
                ->where('reference_type', Deal::class)
                ->where('reference_id', $project->deal_id)
                ->sum('amount');

            $productSaleIds = ProductSale::query()
                ->where('deal_id', $project->deal_id)
                ->pluck('id');

            if ($productSaleIds->isNotEmpty()) {
                $total += (float) JournalEntry::query()
                    ->revenue()
                    ->where('reference_type', ProductSale::class)
                    ->whereIn('reference_id', $productSaleIds)
                    ->sum('amount');
            }
        }

        return round($total, 2);
    }

    private function getDealIncomeJournalEntries(Deal $deal): Collection
    {
        $productSaleIds = ProductSale::query()
            ->where('deal_id', $deal->id)
            ->pluck('id');

        return JournalEntry::query()
            ->revenue()
            ->with('lines')
            ->where(function ($query) use ($deal, $productSaleIds) {
                $query->where(function ($dealQuery) use ($deal) {
                    $dealQuery->where('reference_type', Deal::class)
                        ->where('reference_id', $deal->id);
                });

                if ($productSaleIds->isNotEmpty()) {
                    $query->orWhere(function ($saleQuery) use ($productSaleIds) {
                        $saleQuery->where('reference_type', ProductSale::class)
                            ->whereIn('reference_id', $productSaleIds);
                    });
                }
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();
    }

    private function subscriptionAmountToMrr(Subscription $subscription): float
    {
        $amount = (float) $subscription->amount;

        return match ($subscription->billing_cycle) {
            Subscription::BILLING_MONTHLY => $amount,
            Subscription::BILLING_QUARTERLY => round($amount / 3, 2),
            Subscription::BILLING_YEARLY => round($amount / 12, 2),
            default => 0.0,
        };
    }

    private function resolveAgingBucket(Invoice $invoice): string
    {
        return $this->resolveDateAgingBucket($invoice->due_at);
    }

    private function resolveProjectAgingBucket(Project $project): string
    {
        return $this->resolveDateAgingBucket($project->due_date);
    }

    private function resolveDateAgingBucket(?Carbon $dueDate): string
    {
        if ($dueDate === null || ! $dueDate->isPast()) {
            return 'current';
        }

        $daysPastDue = $dueDate->diffInDays(now());

        return match (true) {
            $daysPastDue <= 30 => 'days_1_30',
            $daysPastDue <= 60 => 'days_31_60',
            $daysPastDue <= 90 => 'days_61_90',
            default => 'over_90',
        };
    }
}
