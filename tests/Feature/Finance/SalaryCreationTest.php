<?php

namespace Tests\Feature\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Salary;
use Modules\Finance\Services\FinanceService;
use Modules\User\Models\Employee;
use Tests\TestCase;

class SalaryCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_salary_is_created_without_a_ledger_entry(): void
    {
        $employee = Employee::factory()->create();

        $salary = app(FinanceService::class)->createSalary([
            'employee_id' => $employee->id,
            'base_salary' => 1500,
            'period' => now()->startOfMonth()->toDateString(),
        ]);

        $this->assertSame(Salary::STATUS_PENDING, $salary->status);
        $this->assertDatabaseMissing('journal_entries', [
            'reference_type' => Salary::class,
            'reference_id' => $salary->id,
        ]);
    }

    public function test_paid_salary_posts_an_expense_ledger_entry(): void
    {
        $employee = Employee::factory()->create();

        $salary = app(FinanceService::class)->createSalary([
            'employee_id' => $employee->id,
            'base_salary' => 2000,
            'period' => now()->startOfMonth()->toDateString(),
            'status' => Salary::STATUS_PAID,
        ]);

        $this->assertSame(Salary::STATUS_PAID, $salary->status);
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => Salary::class,
            'reference_id' => $salary->id,
            'flow' => JournalEntry::FLOW_EXPENSE,
        ]);
    }
}
