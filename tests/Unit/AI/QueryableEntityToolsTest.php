<?php

namespace Tests\Unit\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AI\Services\Assistant\AssistantToolRegistry;
use Modules\AI\Services\Assistant\Tools\GetEntityRecordTool;
use Modules\AI\Services\Assistant\Tools\ListQueryableEntitiesTool;
use Modules\AI\Services\Assistant\Tools\QueryEntityTool;
use Modules\AI\Support\QueryableEntityRegistry;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Lead;
use Modules\Finance\Models\Invoice;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class QueryableEntityToolsTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    public function test_list_queryable_entities_only_includes_authorized_entities(): void
    {
        $user = $this->createAdminWithPermissions(['crm.contacts.view', 'finance.invoices.view']);

        $result = app(ListQueryableEntitiesTool::class)->handle($user, []);

        $this->assertTrue($result->ok);
        $keys = collect($result->data['entities'])->pluck('key');

        $this->assertContains('contact', $keys);
        $this->assertContains('invoice', $keys);
        $this->assertNotContains('lead', $keys);
        $this->assertNotContains('salary', $keys);
    }

    public function test_query_entity_denies_unauthorized_entity(): void
    {
        $user = $this->createAdminWithPermissions(['ai.assistant.view']);

        $result = app(QueryEntityTool::class)->handle($user, [
            'entity' => 'contact',
            'action' => 'list',
        ]);

        $this->assertTrue($result->denied);
    }

    public function test_query_entity_searches_and_gets_contact_records(): void
    {
        $user = $this->createAdminWithPermissions(['crm.contacts.view']);
        $company = Company::query()->create([
            'user_id' => $user->id,
            'name' => 'Acme Co',
            'status' => Company::STATUS_ACTIVE,
        ]);
        $contact = Contact::query()->create([
            'company_id' => $company->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-0100',
            'source' => Lead::SOURCE_WEBSITE,
        ]);

        $search = app(QueryEntityTool::class)->handle($user, [
            'entity' => 'contact',
            'action' => 'search',
            'query' => 'Jane',
        ]);

        $this->assertTrue($search->ok);
        $this->assertSame($contact->id, $search->data['results'][0]['id']);
        $this->assertSame('jane@example.com', $search->data['results'][0]['email']);

        $count = app(QueryEntityTool::class)->handle($user, [
            'entity' => 'contact',
            'action' => 'count',
        ]);
        $this->assertTrue($count->ok);
        $this->assertSame(1, $count->data['count']);

        $detail = app(GetEntityRecordTool::class)->handle($user, [
            'entity' => 'contact',
            'id' => $contact->id,
        ]);
        $this->assertTrue($detail->ok);
        $this->assertSame('Jane Doe', $detail->data['record']['name']);
    }

    public function test_query_entity_filters_invoices_by_status(): void
    {
        $user = $this->createAdminWithPermissions(['finance.invoices.view']);
        $company = Company::query()->create([
            'user_id' => $user->id,
            'name' => 'Billable Co',
            'status' => Company::STATUS_ACTIVE,
        ]);

        Invoice::query()->create([
            'invoice_number' => 'INV-Q-1',
            'company_id' => $company->id,
            'status' => Invoice::STATUS_PAID,
            'subtotal' => 100,
            'tax_amount' => 0,
            'total' => 100,
            'currency' => 'USD',
            'issued_at' => now()->toDateString(),
            'due_at' => now()->toDateString(),
            'paid_at' => now()->toDateString(),
        ]);
        Invoice::query()->create([
            'invoice_number' => 'INV-Q-2',
            'company_id' => $company->id,
            'status' => Invoice::STATUS_SENT,
            'subtotal' => 50,
            'tax_amount' => 0,
            'total' => 50,
            'currency' => 'USD',
            'issued_at' => now()->toDateString(),
            'due_at' => now()->toDateString(),
        ]);

        $result = app(QueryEntityTool::class)->handle($user, [
            'entity' => 'invoice',
            'action' => 'list',
            'filters' => ['status' => Invoice::STATUS_PAID],
        ]);

        $this->assertTrue($result->ok);
        $this->assertSame(1, $result->data['count']);
        $this->assertSame('INV-Q-1', $result->data['results'][0]['invoice_number']);
    }

    public function test_registry_exposes_entity_tools_when_user_has_data_access(): void
    {
        $user = $this->createAdminWithPermissions(['crm.leads.view']);

        $names = collect(app(AssistantToolRegistry::class)->definitionsFor($user))->pluck('name');

        $this->assertContains('list_queryable_entities', $names);
        $this->assertContains('query_entity', $names);
        $this->assertContains('get_entity_record', $names);
    }

    public function test_registry_covers_major_business_domains(): void
    {
        $keys = app(QueryableEntityRegistry::class)->keys();

        foreach ([
            'lead', 'contact', 'customer', 'deal', 'quote', 'invoice', 'project',
            'email_campaign', 'whatsapp_campaign', 'employee', 'ticket', 'product', 'tax_rate',
        ] as $key) {
            $this->assertContains($key, $keys);
        }
    }
}
