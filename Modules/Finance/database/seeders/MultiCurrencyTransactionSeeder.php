<?php

namespace Modules\Finance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Services\FinanceService;
use Modules\Project\Models\Project;

class MultiCurrencyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        if (JournalEntry::query()->where('description', 'USD retainer payment')->exists()) {
            return;
        }

        $finance = app(FinanceService::class);
        $projects = Project::query()->orderBy('id')->get();
        $expenseCategory = ExpenseCategory::query()->orderBy('id')->first();

        $samples = [
            ['flow' => 'revenue', 'amount' => 2500, 'currency' => 'USD', 'days_ago' => 2, 'description' => 'USD retainer payment'],
            ['flow' => 'revenue', 'amount' => 1800, 'currency' => 'EUR', 'days_ago' => 5, 'description' => 'EUR milestone payment'],
            ['flow' => 'revenue', 'amount' => 1200, 'currency' => 'GBP', 'days_ago' => 8, 'description' => 'GBP consulting invoice'],
            ['flow' => 'expense', 'amount' => 450, 'currency' => 'USD', 'days_ago' => 3, 'description' => 'USD SaaS tools'],
            ['flow' => 'expense', 'amount' => 320, 'currency' => 'EUR', 'days_ago' => 6, 'description' => 'EUR contractor invoice'],
            ['flow' => 'expense', 'amount' => 15000, 'currency' => 'TRY', 'days_ago' => 4, 'description' => 'TRY office expenses'],
        ];

        foreach ($samples as $index => $sample) {
            $project = $projects[$index % max($projects->count(), 1)] ?? null;

            $payload = [
                'flow' => $sample['flow'],
                'amount' => $sample['amount'],
                'currency' => $sample['currency'],
                'description' => $sample['description'],
                'transaction_date' => now()->subDays($sample['days_ago'])->toDateString(),
                'reference_type' => $project ? Project::class : null,
                'reference_id' => $project?->id,
            ];

            if ($sample['flow'] === 'expense' && $expenseCategory) {
                $payload['expense_category_id'] = $expenseCategory->id;
            }

            $finance->logTransaction($payload);
        }
    }
}
