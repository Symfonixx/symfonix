<?php

namespace Tests\Feature\Sales;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Services\Deal\DealService;
use Tests\TestCase;

class DealStageTransitionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_moving_deal_stage_updates_status_and_tracks_history(): void
    {
        $actor = User::factory()->admin()->create();
        $this->actingAs($actor);

        $openStage = PipelineStage::query()->create([
            'name' => 'Open',
            'slug' => 'open',
            'sort_order' => 1,
            'probability' => 10,
            'is_active' => true,
            'is_won' => false,
            'is_lost' => false,
        ]);
        $wonStage = PipelineStage::query()->create([
            'name' => 'Won',
            'slug' => 'won',
            'sort_order' => 2,
            'probability' => 100,
            'is_active' => true,
            'is_won' => true,
            'is_lost' => false,
        ]);

        $deal = Deal::query()->create([
            'title' => 'Retainer Contract',
            'pipeline_stage_id' => $openStage->id,
            'value' => 5000,
            'currency' => 'USD',
            'status' => Deal::STATUS_OPEN,
        ]);

        $updated = app(DealService::class)->moveStage($deal, $wonStage->id, 'Closed after contract sign');

        $this->assertSame(Deal::STATUS_WON, $updated->status);
        $this->assertNotNull($updated->won_at);
        $this->assertDatabaseHas('deal_stage_histories', [
            'deal_id' => $deal->id,
            'from_pipeline_stage_id' => $openStage->id,
            'to_pipeline_stage_id' => $wonStage->id,
        ]);
    }
}
