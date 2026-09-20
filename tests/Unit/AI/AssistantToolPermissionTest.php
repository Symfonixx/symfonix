<?php

namespace Tests\Unit\AI;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AI\Services\Assistant\AssistantToolRegistry;
use Modules\AI\Services\Assistant\Tools\GetBestSellingServicesTool;
use Modules\AI\Services\Assistant\Tools\GetEmployeeReportTool;
use Modules\AI\Services\Assistant\Tools\GetLeadStatsTool;
use Modules\AI\Services\Assistant\Tools\GetOverdueWorkTool;
use Modules\AI\Services\Assistant\Tools\GetPaymentStatsTool;
use Modules\AI\Services\Assistant\Tools\GetTopCustomersTool;
use Modules\AI\Services\Assistant\Tools\GetVisitorStatsTool;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Lead;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\JournalEntry;
use Modules\Services\Enums\ServiceStatus;
use Modules\Services\Models\Service;
use Modules\User\Models\Employee;
use MonishRoy\VisitorTracking\Models\VisitorTable;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class AssistantToolPermissionTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    public function test_finance_tool_denies_users_without_finance_permission(): void
    {
        $user = $this->createAdminWithPermissions(['ai.assistant.view']);
        $tool = app(GetPaymentStatsTool::class);

        $this->assertFalse($tool->authorized($user));

        $result = $tool->handle($user, ['period' => 'this_month']);

        $this->assertTrue($result->denied);
        $this->assertFalse($result->ok);
        $this->assertArrayNotHasKey('revenue', $result->data);
    }

    public function test_finance_tool_allows_users_with_dashboard_permission(): void
    {
        $user = $this->createAdminWithPermissions(['finance.dashboard.view']);
        $tool = app(GetPaymentStatsTool::class);

        $this->assertTrue($tool->authorized($user));
    }

    public function test_payment_stats_tool_calculates_year_over_year_growth_rate(): void
    {
        $user = $this->createAdminWithPermissions(['finance.dashboard.view']);

        JournalEntry::query()->create([
            'flow' => JournalEntry::FLOW_REVENUE,
            'amount' => 1000,
            'currency' => 'USD',
            'exchange_rate' => 1,
            'base_amount' => 1000,
            'description' => 'This year',
            'transaction_date' => now()->toDateString(),
        ]);
        JournalEntry::query()->create([
            'flow' => JournalEntry::FLOW_REVENUE,
            'amount' => 500,
            'currency' => 'USD',
            'exchange_rate' => 1,
            'base_amount' => 500,
            'description' => 'Last year',
            'transaction_date' => now()->subYear()->toDateString(),
        ]);

        $result = app(GetPaymentStatsTool::class)->handle($user, ['period' => 'this_year']);

        $this->assertTrue($result->ok);
        $growth = $result->data['growth']['this_year_vs_last_year_ytd'];
        $this->assertSame(1000.0, $growth['this_year']);
        $this->assertSame(500.0, $growth['last_year_ytd']);
        $this->assertSame(100.0, $growth['growth_rate_percent']);
        $this->assertSame('up', $growth['trend']);
    }

    public function test_best_selling_services_tool_denies_users_without_sales_data_access(): void
    {
        $user = $this->createAdminWithPermissions(['ai.assistant.view']);
        $tool = app(GetBestSellingServicesTool::class);

        $this->assertFalse($tool->authorized($user));
        $result = $tool->handle($user, ['period' => 'this_month']);
        $this->assertTrue($result->denied);
        $this->assertArrayNotHasKey('paid_invoice_services', $result->data);
    }

    public function test_visitor_and_employee_report_tools_respect_permissions(): void
    {
        $user = $this->createAdminWithPermissions(['overview.dashboard.view', 'hr.employees.view']);

        $this->assertTrue(app(GetVisitorStatsTool::class)->authorized($user));
        $this->assertTrue(app(GetEmployeeReportTool::class)->authorized($user));

        $report = app(GetEmployeeReportTool::class)->handle($user, ['period' => 'this_month']);
        $this->assertTrue($report->ok);
        $this->assertArrayHasKey('headcount', $report->data);
        $this->assertArrayNotHasKey('kpis', $report->data);
    }

    public function test_visitor_stats_tool_returns_hits_and_top_pages(): void
    {
        $user = $this->createAdminWithPermissions(['overview.dashboard.view']);

        VisitorTable::query()->create([
            'ip' => '1.1.1.1',
            'url' => '/services/web',
            'page_title' => 'Web Development',
        ]);
        VisitorTable::query()->create([
            'ip' => '1.1.1.1',
            'url' => '/services/web',
            'page_title' => 'Web Development',
        ]);
        VisitorTable::query()->create([
            'ip' => '8.8.8.8',
            'url' => '/about',
            'page_title' => 'About',
        ]);

        $result = app(GetVisitorStatsTool::class)->handle($user, ['period' => 'this_month']);

        $this->assertTrue($result->ok);
        $this->assertSame(3, $result->data['hits_in_period']);
        $this->assertSame(2, $result->data['unique_visitors_in_period']);
        $this->assertSame('/services/web', $result->data['top_pages'][0]['url']);
        $this->assertSame(2, $result->data['top_pages'][0]['visits']);
    }

    public function test_best_selling_services_tool_ranks_paid_invoice_lines(): void
    {
        $user = $this->createAdminWithPermissions(['finance.invoices.view', 'services.catalog.view']);
        $company = Company::query()->create([
            'user_id' => $user->id,
            'name' => 'Acme Co',
            'status' => Company::STATUS_ACTIVE,
        ]);

        $web = $this->makeService('Web Development', 'web-development', 12);
        $mobile = $this->makeService('Mobile Apps', 'mobile-apps', 80);

        $invoice = Invoice::query()->create([
            'invoice_number' => 'INV-TEST-1',
            'company_id' => $company->id,
            'status' => Invoice::STATUS_PAID,
            'subtotal' => 500,
            'tax_amount' => 0,
            'total' => 500,
            'currency' => 'USD',
            'issued_at' => now()->toDateString(),
            'due_at' => now()->toDateString(),
            'paid_at' => now()->toDateString(),
        ]);

        InvoiceLine::query()->create([
            'invoice_id' => $invoice->id,
            'service_id' => $web->id,
            'description' => 'Web Development',
            'quantity' => 2,
            'unit_price' => 200,
            'amount' => 400,
        ]);
        InvoiceLine::query()->create([
            'invoice_id' => $invoice->id,
            'service_id' => $mobile->id,
            'description' => 'Mobile Apps',
            'quantity' => 1,
            'unit_price' => 100,
            'amount' => 100,
        ]);

        $result = app(GetBestSellingServicesTool::class)->handle($user, ['period' => 'this_month']);

        $this->assertTrue($result->ok);
        $this->assertSame($web->id, $result->data['paid_invoice_services'][0]['service_id']);
        $this->assertSame(400.0, $result->data['paid_invoice_services'][0]['revenue']);
        $this->assertSame($mobile->id, $result->data['most_visited_service_pages'][0]['service_id']);
        $this->assertSame(80, $result->data['most_visited_service_pages'][0]['page_visits']);
    }

    public function test_employee_report_tool_returns_kpis_with_reporting_permission(): void
    {
        Employee::factory()->create(['status' => Employee::STATUS_ACTIVE]);
        $user = $this->createAdminWithPermissions(['reporting.employee.view']);

        $result = app(GetEmployeeReportTool::class)->handle($user, ['period' => 'this_month']);

        $this->assertTrue($result->ok);
        $this->assertSame(1, $result->data['headcount']['active']);
        $this->assertArrayHasKey('attendance_days', $result->data['kpis']);
        $this->assertArrayHasKey('utilization_rate', $result->data['kpis']);
        $this->assertArrayNotHasKey('salary_paid_total', $result->data['kpis']);
    }

    public function test_registry_exposes_visitor_sales_and_employee_tools(): void
    {
        $user = $this->createAdminWithPermissions([
            'overview.dashboard.view',
            'finance.invoices.view',
            'hr.employees.view',
        ]);

        $names = collect(app(AssistantToolRegistry::class)->definitionsFor($user))->pluck('name');

        $this->assertContains('get_visitor_stats', $names);
        $this->assertContains('get_best_selling_services', $names);
        $this->assertContains('get_employee_report', $names);
        $this->assertContains('get_top_customers', $names);
    }

    public function test_top_customers_tool_ranks_paid_invoice_revenue(): void
    {
        $user = $this->createAdminWithPermissions(['finance.invoices.view']);
        $best = Company::query()->create([
            'user_id' => $user->id,
            'name' => 'Best Co',
            'status' => Company::STATUS_ACTIVE,
        ]);
        $other = Company::query()->create([
            'user_id' => $user->id,
            'name' => 'Other Co',
            'status' => Company::STATUS_ACTIVE,
        ]);

        $this->makePaidInvoice($best, 'INV-TOP-1', 900);
        $this->makePaidInvoice($other, 'INV-TOP-2', 120);

        $result = app(GetTopCustomersTool::class)->handle($user, ['period' => 'this_month']);

        $this->assertTrue($result->ok);
        $this->assertSame($best->id, $result->data['top_customers'][0]['company_id']);
        $this->assertSame(900.0, $result->data['top_customers'][0]['revenue']);
    }

    public function test_overdue_work_tool_ranks_people_with_most_overdue_tasks(): void
    {
        $busy = $this->createAdminWithPermissions(['crm.activities.view']);
        $other = User::factory()->admin()->create();
        $lead = Lead::query()->create([
            'name' => 'Follow me',
            'status' => Lead::STATUS_NEW,
        ]);

        foreach ([1, 2, 3] as $i) {
            CrmActivity::query()->create([
                'subject_type' => Lead::class,
                'subject_id' => $lead->id,
                'type' => CrmActivity::TYPE_TASK,
                'title' => 'Overdue '.$i,
                'scheduled_at' => now()->subDays($i),
                'user_id' => $busy->id,
            ]);
        }

        CrmActivity::query()->create([
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'type' => CrmActivity::TYPE_TASK,
            'title' => 'Other overdue',
            'scheduled_at' => now()->subDay(),
            'user_id' => $other->id,
        ]);

        $result = app(GetOverdueWorkTool::class)->handle($busy, []);

        $this->assertTrue($result->ok);
        $this->assertSame(4, $result->data['overdue_crm_task_count']);
        $this->assertSame($busy->id, $result->data['people_with_most_overdue_tasks'][0]['user_id']);
        $this->assertSame(3, $result->data['people_with_most_overdue_tasks'][0]['overdue_tasks']);
    }

    public function test_lead_stats_tool_lists_follow_ups_due_today(): void
    {
        $user = $this->createAdminWithPermissions(['crm.leads.view', 'crm.activities.view']);
        $lead = Lead::query()->create([
            'name' => 'Call today',
            'status' => Lead::STATUS_CONTACTED,
        ]);

        CrmActivity::query()->create([
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'type' => CrmActivity::TYPE_TASK,
            'title' => 'Follow up',
            'scheduled_at' => now(),
            'user_id' => $user->id,
        ]);

        $result = app(GetLeadStatsTool::class)->handle($user, ['period' => 'today']);

        $this->assertTrue($result->ok);
        $this->assertSame($lead->id, $result->data['follow_up_today'][0]['id']);
    }

    private function makePaidInvoice(Company $company, string $number, float $total): Invoice
    {
        return Invoice::query()->create([
            'invoice_number' => $number,
            'company_id' => $company->id,
            'status' => Invoice::STATUS_PAID,
            'subtotal' => $total,
            'tax_amount' => 0,
            'total' => $total,
            'currency' => 'USD',
            'issued_at' => now()->toDateString(),
            'due_at' => now()->toDateString(),
            'paid_at' => now()->toDateString(),
        ]);
    }

    private function makeService(string $title, string $slug, int $visits): Service
    {
        return Service::query()->create([
            'title' => ['en' => $title],
            'slug' => $slug,
            'image' => 'services/placeholder.jpg',
            'content' => ['en' => $title],
            'status' => ServiceStatus::PUBLISHED->value,
            'visits' => $visits,
        ]);
    }
}
