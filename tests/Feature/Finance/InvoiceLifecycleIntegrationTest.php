<?php

namespace Tests\Feature\Finance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CRM\Models\Company;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Services\InvoiceService;
use Tests\TestCase;

class InvoiceLifecycleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_invoice_creation_and_payment_posts_ledger_entry(): void
    {
        $owner = User::factory()->create();
        $company = Company::query()->create([
            'user_id' => $owner->id,
            'name' => 'Acme Co',
            'status' => Company::STATUS_ACTIVE,
        ]);

        $service = app(InvoiceService::class);

        $invoice = $service->createManual([
            'company_id' => $company->id,
            'currency' => 'USD',
            'lines' => [
                [
                    'description' => 'Development Sprint',
                    'quantity' => 2,
                    'unit_price' => 500,
                    'tax_percent' => 10,
                ],
            ],
        ]);

        $this->assertSame(Invoice::STATUS_DRAFT, $invoice->status);
        $this->assertCount(1, $invoice->lines);
        $this->assertSame('1000.00', $invoice->subtotal);
        $this->assertSame('1100.00', $invoice->total);

        $paid = $service->markAsPaid($invoice->fresh());

        $this->assertSame(Invoice::STATUS_PAID, $paid->status);
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => Invoice::class,
            'reference_id' => $invoice->id,
            'flow' => JournalEntry::FLOW_REVENUE,
        ]);
        $this->assertDatabaseCount('journal_lines', 2);
    }
}
