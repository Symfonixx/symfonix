<?php

namespace Modules\Finance\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Deal;
use Modules\Finance\Models\Commission;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\Salary;
use Modules\Finance\Models\Transaction;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSale;
use Modules\Project\Models\Project;

class FinanceService
{
    /**
     * Sum all income (credit) transactions.
     */
    public function getTotalIncome(?array $dateRange = null, ?array $monthKeys = null): float
    {
        $query = $this->baseTransactionQuery($dateRange, $monthKeys)->income();

        return (float) $query->sum('amount');
    }

    /**
     * Sum all expense (debit) transactions.
     */
    public function getTotalExpenses(?array $dateRange = null, ?array $monthKeys = null): float
    {
        $query = $this->baseTransactionQuery($dateRange, $monthKeys)->expense();

        return (float) $query->sum('amount');
    }

    /**
     * Net profit = total income minus total expenses.
     */
    public function getNetProfit(?array $dateRange = null, ?array $monthKeys = null): float
    {
        return $this->getTotalIncome($dateRange, $monthKeys) - $this->getTotalExpenses($dateRange, $monthKeys);
    }

    /**
     * Aggregate loss amount when expenses exceed revenue.
     */
    public function getTotalLosses(?array $dateRange = null, ?array $monthKeys = null): float
    {
        $income = $this->getTotalIncome($dateRange, $monthKeys);
        $expenses = $this->getTotalExpenses($dateRange, $monthKeys);

        return max(0, $expenses - $income);
    }

    /**
     * Revenue, expenses, net profit, and losses for dashboard KPI cards.
     *
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
     * Distinct months that have ledger activity, newest first.
     *
     * @return array<int, array{key: string, label: string}>
     */
    public function getAvailableMonths(): array
    {
        return Transaction::query()
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
     * Monthly profit, expense, and loss series for dashboard charts.
     *
     * @return array<int, array{label: string, profit: float, expenses: float, losses: float, revenue: float}>
     */
    public function getMonthlyChartData(?array $monthKeys = null): array
    {
        $query = Transaction::query()
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month')
            ->selectRaw("SUM(CASE WHEN type = '".Transaction::TYPE_INCOME."' THEN amount ELSE 0 END) as revenue")
            ->selectRaw("SUM(CASE WHEN type = '".Transaction::TYPE_EXPENSE."' THEN amount ELSE 0 END) as expenses")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month');

        $this->applyMonthFilter($query, $monthKeys);

        return $query->get()
            ->map(function ($row) {
                $revenue = (float) $row->revenue;
                $expenses = (float) $row->expenses;
                $net = $revenue - $expenses;
                $date = Carbon::create((int) $row->year, (int) $row->month, 1);

                return [
                    'label' => $date->translatedFormat('M Y'),
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
     * Recalculate and persist a project's payment status from linked income transactions.
     */
    public function updateProjectPaymentStatus(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);
        $budget = (float) ($project->budget ?? 0);

        $totalPaid = (float) Transaction::query()
            ->income()
            ->where('reference_type', Project::class)
            ->where('reference_id', $projectId)
            ->sum('amount');

        if ($budget <= 0 || $totalPaid <= 0) {
            $status = Project::PAYMENT_UNPAID;
        } elseif ($totalPaid >= $budget) {
            $status = Project::PAYMENT_FULLY_PAID;
        } else {
            $status = Project::PAYMENT_PARTIALLY_PAID;
        }

        $project->update(['payment_status' => $status]);
    }

    /**
     * Log deal income and generate a pending commission for the assigned sales rep.
     */
    public function recordSaleAndCommission(Deal $deal): void
    {
        if ($deal->value === null || (float) $deal->value <= 0) {
            return;
        }

        DB::transaction(function () use ($deal) {
            $existingIncome = Transaction::query()
                ->income()
                ->where('reference_type', Deal::class)
                ->where('reference_id', $deal->id)
                ->exists();

            if (! $existingIncome) {
                Transaction::query()->create([
                    'type' => Transaction::TYPE_INCOME,
                    'amount' => $deal->value,
                    'reference_type' => Deal::class,
                    'reference_id' => $deal->id,
                    'description' => __('finance::finance.messages.sale_income', ['title' => $deal->title]),
                    'transaction_date' => now()->toDateString(),
                ]);
            }

            if ($deal->assigned_to) {
                $percentage = (float) config('finance.default_commission_percentage', 10);

                Commission::query()->firstOrCreate(
                    ['deal_id' => $deal->id],
                    [
                        'employee_id' => $deal->assigned_to,
                        'commission_percentage' => $percentage,
                        'commission_amount' => round((float) $deal->value * ($percentage / 100), 2),
                        'status' => Commission::STATUS_PENDING,
                    ]
                );
            }

            $project = $deal->relationLoaded('project') ? $deal->project : $deal->project()->first();

            if ($project) {
                $existingProjectIncome = Transaction::query()
                    ->income()
                    ->where('reference_type', Project::class)
                    ->where('reference_id', $project->id)
                    ->exists();

                if (! $existingProjectIncome) {
                    Transaction::query()->create([
                        'type' => Transaction::TYPE_INCOME,
                        'amount' => $deal->value,
                        'reference_type' => Project::class,
                        'reference_id' => $project->id,
                        'description' => __('finance::finance.messages.project_income', ['title' => $project->title]),
                        'transaction_date' => now()->toDateString(),
                    ]);
                }

                $this->updateProjectPaymentStatus($project->id);
            }
        });
    }

    /**
     * Record a salary payout as an expense ledger entry.
     */
    public function recordSalaryPayout(Salary $salary): void
    {
        if ($salary->status === Salary::STATUS_PAID) {
            return;
        }

        DB::transaction(function () use ($salary) {
            $category = $this->resolveExpenseCategory('salaries');

            Transaction::query()->create([
                'type' => Transaction::TYPE_EXPENSE,
                'expense_category_id' => $category?->id,
                'amount' => $salary->base_salary,
                'reference_type' => Salary::class,
                'reference_id' => $salary->id,
                'description' => __('finance::salary.messages.payout_description', [
                    'name' => $salary->employee?->name ?? '#'.$salary->employee_id,
                ]),
                'transaction_date' => now()->toDateString(),
            ]);

            $salary->update([
                'status' => Salary::STATUS_PAID,
                'paid_at' => now()->toDateString(),
            ]);
        });
    }

    /**
     * Record a commission payout as an expense ledger entry.
     */
    public function recordCommissionPayout(Commission $commission): void
    {
        if ($commission->status === Commission::STATUS_PAID) {
            return;
        }

        DB::transaction(function () use ($commission) {
            $category = $this->resolveExpenseCategory('commissions');

            Transaction::query()->create([
                'type' => Transaction::TYPE_EXPENSE,
                'expense_category_id' => $category?->id,
                'amount' => $commission->commission_amount,
                'reference_type' => Commission::class,
                'reference_id' => $commission->id,
                'description' => __('finance::commission.messages.payout_description', [
                    'name' => $commission->employee?->name ?? '#'.$commission->employee_id,
                    'deal' => $commission->deal?->title ?? '#'.$commission->deal_id,
                ]),
                'transaction_date' => now()->toDateString(),
            ]);

            $commission->update(['status' => Commission::STATUS_PAID]);
        });
    }

    /**
     * Record a product sale and log matching income in the central ledger.
     */
    public function recordProductSale(array $data, bool $skipIfExists = false): ?ProductSale
    {
        $product = Product::query()->findOrFail($data['product_id']);
        $quantity = (int) ($data['quantity'] ?? 1);
        $unitPrice = array_key_exists('unit_price', $data) && $data['unit_price'] !== null
            ? (float) $data['unit_price']
            : (float) $product->price;
        $totalAmount = round($quantity * $unitPrice, 2);

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

        return DB::transaction(function () use ($data, $product, $quantity, $unitPrice, $totalAmount) {
            $sale = ProductSale::query()->create([
                'product_id' => $product->id,
                'company_id' => $data['company_id'] ?? null,
                'deal_id' => $data['deal_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'currency' => $data['currency'] ?? $product->currency,
                'notes' => $data['notes'] ?? null,
                'sold_at' => $data['sold_at'] ?? now()->toDateString(),
            ]);

            $existingIncome = Transaction::query()
                ->income()
                ->where('reference_type', ProductSale::class)
                ->where('reference_id', $sale->id)
                ->exists();

            if (! $existingIncome) {
                Transaction::query()->create([
                    'type' => Transaction::TYPE_INCOME,
                    'amount' => $totalAmount,
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

    /**
     * Log product sales from deal line items when a deal is won.
     */
    public function recordDealProductSales(Deal $deal): void
    {
        $deal->loadMissing('products');

        foreach ($deal->products as $product) {
            $this->recordProductSale([
                'product_id' => $product->id,
                'company_id' => $deal->company_id,
                'deal_id' => $deal->id,
                'user_id' => $deal->assigned_to,
                'quantity' => (int) $product->pivot->quantity,
                'unit_price' => (float) $product->pivot->unit_price,
                'currency' => $deal->currency,
                'sold_at' => now()->toDateString(),
            ], skipIfExists: true);
        }
    }

    /**
     * Remove a product sale and its linked income ledger entries.
     */
    public function deleteProductSale(ProductSale $sale): void
    {
        DB::transaction(function () use ($sale) {
            Transaction::query()
                ->where('reference_type', ProductSale::class)
                ->where('reference_id', $sale->id)
                ->delete();

            $sale->delete();
        });
    }

    /**
     * Log a manual income or expense transaction from the dashboard.
     */
    public function logTransaction(array $data): Transaction
    {
        $transaction = Transaction::query()->create([
            'type' => $data['type'],
            'expense_category_id' => $data['type'] === Transaction::TYPE_EXPENSE
                ? ($data['expense_category_id'] ?? null)
                : null,
            'amount' => $data['amount'],
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'description' => $data['description'] ?? null,
            'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
        ]);

        if (
            $transaction->type === Transaction::TYPE_INCOME
            && $transaction->reference_type === Project::class
            && $transaction->reference_id
        ) {
            $this->updateProjectPaymentStatus((int) $transaction->reference_id);
        }

        return $transaction;
    }

    private function baseTransactionQuery(?array $dateRange = null, ?array $monthKeys = null)
    {
        $query = Transaction::query();

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
}
