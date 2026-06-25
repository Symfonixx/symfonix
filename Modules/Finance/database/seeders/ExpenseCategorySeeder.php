<?php

namespace Modules\Finance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Finance\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Seed default expense categories for the central ledger.
     */
    public function run(): void
    {
        $categories = [
            'Software',
            'Marketing',
            'Office',
            'Salaries',
            'Commissions',
            'Utilities',
            'Travel',
            'Other',
        ];

        foreach ($categories as $name) {
            ExpenseCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
