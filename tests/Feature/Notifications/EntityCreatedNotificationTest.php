<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\SystemEntityCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Lead;
use Modules\Finance\Services\InvoiceService;
use Modules\Support\Models\Ticket;
use Modules\Support\Models\TicketCategory;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class EntityCreatedNotificationTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    public function test_notification_is_queued(): void
    {
        $this->assertContains(ShouldQueue::class, class_implements(SystemEntityCreatedNotification::class));
    }

    public function test_creating_a_lead_notifies_users_with_the_mapped_permission(): void
    {
        Notification::fake();

        $sales = $this->createAdminWithPermissions(['crm.leads.view']);
        $finance = $this->createAdminWithPermissions(['finance.invoices.view']);

        $lead = Lead::query()->create([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'source' => Lead::SOURCE_WEBSITE,
            'status' => Lead::STATUS_NEW,
        ]);

        Notification::assertSentTo(
            $sales,
            SystemEntityCreatedNotification::class,
            fn (SystemEntityCreatedNotification $notification): bool => $notification->model->is($lead)
                && $notification->eventConfig['permission'] === 'crm.leads.view',
        );
        Notification::assertNotSentTo($finance, SystemEntityCreatedNotification::class);
    }

    public function test_the_creating_user_is_notified_by_default(): void
    {
        Notification::fake();

        $actor = $this->createAdminWithPermissions(['crm.leads.view']);
        $this->actingAs($actor);

        Lead::query()->create([
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
        ]);

        Notification::assertSentTo($actor, SystemEntityCreatedNotification::class);
    }

    public function test_the_creating_user_can_be_excluded_when_configured(): void
    {
        Notification::fake();
        config(['notifications.exclude_actor' => true]);

        $actor = $this->createAdminWithPermissions(['crm.leads.view']);
        $peer = $this->createAdminWithPermissions(['crm.leads.view']);
        $this->actingAs($actor);

        Lead::query()->create([
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
        ]);

        Notification::assertNotSentTo($actor, SystemEntityCreatedNotification::class);
        Notification::assertSentTo($peer, SystemEntityCreatedNotification::class);
    }

    public function test_customers_notify_sales_but_admin_users_do_not(): void
    {
        Notification::fake();

        $sales = $this->createAdminWithPermissions(['sales.customers.view']);

        $customer = User::factory()->create([
            'name' => 'Portal Customer',
            'type' => User::TYPE_CUSTOMER,
        ]);
        $admin = User::factory()->admin()->create([
            'name' => 'Staff Admin',
        ]);

        Notification::assertSentTo(
            $sales,
            SystemEntityCreatedNotification::class,
            fn (SystemEntityCreatedNotification $notification): bool => $notification->model->is($customer),
        );
        Notification::assertNotSentTo(
            $sales,
            SystemEntityCreatedNotification::class,
            fn (SystemEntityCreatedNotification $notification): bool => $notification->model->is($admin),
        );
    }

    public function test_only_task_activities_notify_and_notes_do_not(): void
    {
        Notification::fake();

        $crm = $this->createAdminWithPermissions(['crm.activities.view']);
        $lead = Lead::query()->create(['name' => 'Activity Subject']);

        CrmActivity::query()->create([
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'type' => CrmActivity::TYPE_NOTE,
            'title' => 'Internal note',
        ]);

        Notification::assertNotSentTo($crm, SystemEntityCreatedNotification::class);

        $task = CrmActivity::query()->create([
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'type' => CrmActivity::TYPE_TASK,
            'title' => 'Follow up call',
        ]);

        Notification::assertSentTo(
            $crm,
            SystemEntityCreatedNotification::class,
            fn (SystemEntityCreatedNotification $notification): bool => $notification->model->is($task)
                && $notification->eventConfig['entity'] === 'task',
        );
    }

    public function test_creating_an_invoice_notifies_finance_users(): void
    {
        Notification::fake();

        $finance = $this->createAdminWithPermissions(['finance.invoices.view']);
        $this->createAdminWithPermissions(['crm.companies.view']);

        $owner = User::factory()->create();
        $company = Company::query()->create([
            'user_id' => $owner->id,
            'name' => 'Acme Co',
            'status' => Company::STATUS_ACTIVE,
        ]);

        $invoice = app(InvoiceService::class)->createManual([
            'company_id' => $company->id,
            'currency' => 'USD',
            'lines' => [
                [
                    'description' => 'Sprint',
                    'quantity' => 1,
                    'unit_price' => 100,
                    'tax_percent' => 0,
                ],
            ],
        ]);

        Notification::assertSentTo(
            $finance,
            SystemEntityCreatedNotification::class,
            fn (SystemEntityCreatedNotification $notification): bool => $notification->model->is($invoice)
                && $notification->eventConfig['permission'] === 'finance.invoices.view',
        );
    }

    public function test_creating_a_ticket_notifies_support_users(): void
    {
        Notification::fake();

        $support = $this->createAdminWithPermissions(['support.tickets.view']);
        $customer = User::factory()->create();
        $category = TicketCategory::query()->create([
            'name' => ['en' => 'Billing'],
            'is_active' => true,
        ]);

        $ticket = Ticket::query()->create([
            'ticket_number' => 'TKT-2026-00001',
            'user_id' => $customer->id,
            'ticket_category_id' => $category->id,
            'subject' => 'Cannot download invoice',
            'description' => 'The PDF link returns 404.',
            'priority' => Ticket::PRIORITY_MEDIUM,
            'status' => Ticket::STATUS_OPEN,
        ]);

        Notification::assertSentTo(
            $support,
            SystemEntityCreatedNotification::class,
            fn (SystemEntityCreatedNotification $notification): bool => $notification->model->is($ticket),
        );
    }

    public function test_disabled_config_does_not_dispatch_notifications(): void
    {
        Notification::fake();
        config(['notifications.enabled' => false]);

        $this->createAdminWithPermissions(['crm.leads.view']);

        Lead::query()->create([
            'name' => 'Silent Lead',
            'email' => 'silent@example.com',
        ]);

        Notification::assertNothingSent();
    }
}
