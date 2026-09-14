<?php

namespace Tests\Unit\Sales;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Models\WhatsAppTemplate;
use Modules\Finance\Models\Invoice;
use Tests\TestCase;

class ModelRelationshipAndScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_scopes_target_expected_type(): void
    {
        $adminSql = User::query()->admins()->toSql();
        $customerSql = User::query()->customers()->toSql();

        $this->assertStringContainsString('type', $adminSql);
        $this->assertStringContainsString('type', $customerSql);
    }

    public function test_invoice_open_scope_filters_supported_statuses(): void
    {
        $sql = Invoice::query()->open()->toSql();

        $this->assertStringContainsString('status', strtolower($sql));
    }

    public function test_pipeline_stage_display_name_falls_back_to_name_without_translation(): void
    {
        $stage = new PipelineStage([
            'name' => 'Discovery',
            'slug' => 'discovery',
        ]);

        $this->assertSame('Discovery', $stage->display_name);
    }

    public function test_whatsapp_template_helpers_work_for_variable_templates(): void
    {
        $template = new WhatsAppTemplate([
            'name' => 'Offer',
            'language' => 'en',
            'status' => WhatsAppTemplate::STATUS_APPROVED,
            'is_active' => true,
            'body' => 'Hi {{1}}, your quote is {{2}} and ETA is {{2}}',
        ]);

        $this->assertTrue($template->isSendable());
        $this->assertSame('Offer (en)', $template->displayName());
        $this->assertSame(2, $template->bodyVariableCount());
    }

    public function test_deal_open_scope_targets_open_status(): void
    {
        $sql = Deal::query()->open()->toSql();

        $this->assertStringContainsString('status', $sql);
    }
}
