<?php

namespace Tests\Feature\Sales;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Services\Forecast\ForecastService;
use Tests\TestCase;

class ForecastSnapshotIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_forecast_service_generates_summary_stage_breakdown_and_monthly_snapshots(): void
    {
        $stageQualified = PipelineStage::query()->create([
            'name' => 'Qualified',
            'slug' => 'qualified',
            'sort_order' => 1,
            'probability' => 80,
            'is_active' => true,
            'is_won' => false,
            'is_lost' => false,
        ]);
        $stageProposal = PipelineStage::query()->create([
            'name' => 'Proposal',
            'slug' => 'proposal',
            'sort_order' => 2,
            'probability' => 50,
            'is_active' => true,
            'is_won' => false,
            'is_lost' => false,
        ]);

        Deal::query()->create([
            'title' => 'Website Revamp',
            'pipeline_stage_id' => $stageQualified->id,
            'value' => 1000,
            'currency' => 'USD',
            'expected_close_date' => now()->addMonth()->toDateString(),
            'status' => Deal::STATUS_OPEN,
        ]);
        Deal::query()->create([
            'title' => 'Mobile App',
            'pipeline_stage_id' => $stageProposal->id,
            'value' => 2000,
            'currency' => 'USD',
            'expected_close_date' => now()->addMonths(2)->toDateString(),
            'status' => Deal::STATUS_OPEN,
        ]);

        $forecast = app(ForecastService::class)->build([
            'horizon' => 3,
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->addMonths(2)->endOfMonth()->toDateString(),
        ]);

        $this->assertSame(2, $forecast['summary']['deal_count']);
        $this->assertSame(3000.0, $forecast['summary']['total_pipeline']['value']);
        $this->assertSame(1800.0, $forecast['summary']['forecasted_revenue']['value']);
        $this->assertCount(2, $forecast['stage_breakdown']);
        $this->assertCount(3, $forecast['monthly_predictions']);
    }
}
