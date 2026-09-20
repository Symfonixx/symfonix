<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mockery\MockInterface;
use Modules\AI\Services\GeminiTextService;
use Modules\AI\Services\OpenAITextService;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Lead;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectStatus;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class LeadFollowUpTest extends TestCase
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

    public function test_user_without_permission_cannot_generate_a_follow_up(): void
    {
        $user = $this->createAdminWithPermissions(['crm.leads.view']);
        $lead = Lead::query()->create([
            'name' => 'Locked lead',
            'status' => Lead::STATUS_NEW,
        ]);

        $this->actingAs($user)
            ->postJson(route('admin.ai.leads.follow-up', $lead))
            ->assertForbidden();
    }

    public function test_it_generates_a_follow_up_from_lead_data_and_project_activity(): void
    {
        $user = $this->createAdminWithPermissions([
            'crm.leads.view',
            'crm.activities.create',
            'crm.activities.view',
            'project.projects.view',
        ]);

        $company = Company::query()->create([
            'user_id' => $user->id,
            'name' => 'Helios Agency',
            'status' => Company::STATUS_ACTIVE,
        ]);

        $lead = Lead::query()->create([
            'name' => 'Omar Saleh',
            'email' => 'omar@example.com',
            'status' => Lead::STATUS_QUALIFIED,
            'problem_statement' => 'Wants a booking platform',
            'company_id' => $company->id,
        ]);

        CrmActivity::query()->create([
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'type' => CrmActivity::TYPE_EMAIL,
            'title' => 'Sent proposal',
            'body' => 'Shared the first quote.',
            'user_id' => $user->id,
        ]);

        $status = ProjectStatus::query()->create([
            'name' => 'Discovery',
            'sort_order' => 1,
        ]);

        Project::query()->create([
            'title' => 'Helios booking platform',
            'description' => 'MVP for online reservations',
            'company_id' => $company->id,
            'project_status_id' => $status->id,
            'payment_status' => Project::PAYMENT_UNPAID,
        ]);

        $scheduled = now()->addDays(2)->setTime(11, 0)->format('Y-m-d\TH:i');

        $this->mock(OpenAITextService::class, function (MockInterface $mock) use ($scheduled): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateStructured')
                ->once()
                ->withArgs(function (string $systemPrompt, string $userMessage) {
                    return str_contains($userMessage, 'Omar Saleh')
                        && str_contains($userMessage, 'Wants a booking platform')
                        && str_contains($userMessage, 'Sent proposal')
                        && str_contains($userMessage, 'Helios booking platform');
                })
                ->andReturn([
                    'success' => true,
                    'content' => json_encode([
                        'type' => 'call',
                        'title' => 'Call about the booking platform',
                        'body' => 'Follow up on the quote and discovery scope.',
                        'scheduled_at' => $scheduled,
                    ]),
                    'error' => null,
                ]);
        });

        $this->mock(GeminiTextService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(false);
        });

        $this->actingAs($user)
            ->postJson(route('admin.ai.leads.follow-up', $lead), [
                'prompt' => 'Call them about the quote',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('fields.type', 'call')
            ->assertJsonPath('fields.title', 'Call about the booking platform')
            ->assertJsonPath('fields.body', 'Follow up on the quote and discovery scope.')
            ->assertJsonPath('fields.scheduled_at', $scheduled);
    }
}
