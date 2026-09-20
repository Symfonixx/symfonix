<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mockery\MockInterface;
use Modules\AI\Services\GeminiTextService;
use Modules\AI\Services\OpenAITextService;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;
use Modules\Services\Enums\ServiceStatus;
use Modules\Services\Models\Service;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class QuoteGenerationTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_user_without_permission_cannot_generate_a_quote(): void
    {
        $user = $this->createAdminWithPermissions(['sales.quotes.view']);
        [$company, $deal] = $this->makeCompanyAndDeal($user->id);

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-quote'), [
                'company_id' => $company->id,
                'deal_id' => $deal->id,
            ])
            ->assertForbidden();
    }

    public function test_it_requires_a_deal_that_belongs_to_the_company(): void
    {
        $user = $this->createAdminWithPermissions(['sales.quotes.create']);
        [$company] = $this->makeCompanyAndDeal($user->id);
        [, $otherDeal] = $this->makeCompanyAndDeal($user->id, 'Other Co', 'Other deal');

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-quote'), [
                'company_id' => $company->id,
                'deal_id' => $otherDeal->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['deal_id']);
    }

    public function test_it_generates_contract_terms_and_lines_from_company_and_deal(): void
    {
        $user = $this->createAdminWithPermissions(['sales.quotes.create', 'sales.quotes.view']);
        [$company, $deal] = $this->makeCompanyAndDeal($user->id);
        $service = $this->makeService('Website rebuild', 'website-rebuild');
        $deal->services()->attach($service->id, [
            'quantity' => 1,
            'unit_price' => 4500,
        ]);

        $this->mock(OpenAITextService::class, function (MockInterface $mock) use ($company, $deal, $service): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateStructured')
                ->once()
                ->withArgs(function (string $systemPrompt, string $userMessage) use ($company, $deal) {
                    return str_contains($systemPrompt, 'binding service contract')
                        && str_contains($userMessage, $company->name)
                        && str_contains($userMessage, $deal->title)
                        && str_contains($userMessage, 'Website rebuild');
                })
                ->andReturn([
                    'success' => true,
                    'content' => json_encode([
                        'terms' => 'This quote is the contract between us and Helios Agency for the website rebuild.',
                        'notes' => 'Prepared from the open website deal.',
                        'validity_days' => 21,
                        'currency' => 'USD',
                        'lines' => [[
                            'item_type' => 'service',
                            'service_id' => $service->id,
                            'description' => 'Website rebuild including discovery and launch',
                            'quantity' => 1,
                            'unit_price' => 4500,
                            'discount_percent' => 0,
                            'tax_percent' => 0,
                        ]],
                    ]),
                    'error' => null,
                ]);
        });

        $this->mock(GeminiTextService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(false);
        });

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-quote'), [
                'company_id' => $company->id,
                'deal_id' => $deal->id,
                'prompt' => 'Keep payment net 14',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('fields.currency', 'USD')
            ->assertJsonPath('fields.notes', 'Prepared from the open website deal.')
            ->assertJsonPath('fields.expires_at', now()->addDays(21)->toDateString())
            ->assertJsonPath('fields.lines.0.service_id', $service->id)
            ->assertJsonPath('fields.lines.0.unit_price', 4500)
            ->assertJsonPath('fields.lines.0.description', 'Website rebuild including discovery and launch');
    }

    public function test_quote_form_includes_the_ai_generate_button(): void
    {
        $this->assertTrue(Route::has('admin.ai.content.generate-quote'));

        $html = view('ai::components.generate-quote-button')->render();

        $this->assertStringContainsString('ai-generate-quote-trigger', $html);
        $this->assertStringContainsString(__('crm::quote.ai.button'), $html);
    }

    /**
     * @return array{0: Company, 1: Deal}
     */
    private function makeCompanyAndDeal(int $userId, string $companyName = 'Helios Agency', string $dealTitle = 'Website rebuild'): array
    {
        $company = Company::query()->create([
            'user_id' => $userId,
            'name' => $companyName,
            'activity_type' => Company::ACTIVITY_TECHNOLOGY,
            'country' => 'Syria',
            'status' => Company::STATUS_ACTIVE,
        ]);

        $stage = PipelineStage::query()->first() ?? PipelineStage::query()->create([
            'name' => 'Proposal',
            'slug' => 'proposal-'.uniqid(),
            'sort_order' => 1,
            'probability' => 40,
            'is_active' => true,
            'is_won' => false,
            'is_lost' => false,
        ]);

        $deal = Deal::query()->create([
            'title' => $dealTitle,
            'company_id' => $company->id,
            'pipeline_stage_id' => $stage->id,
            'value' => 4500,
            'currency' => 'USD',
            'description' => 'Rebuild the marketing site and launch in 6 weeks.',
            'status' => Deal::STATUS_OPEN,
        ]);

        return [$company, $deal];
    }

    private function makeService(string $title, string $slug): Service
    {
        return Service::query()->create([
            'title' => ['en' => $title],
            'slug' => $slug,
            'image' => 'services/placeholder.jpg',
            'description' => ['en' => $title.' for agencies'],
            'content' => ['en' => $title],
            'status' => ServiceStatus::PUBLISHED->value,
        ]);
    }
}
