<?php

namespace Modules\AI\Support;

use App\Models\User;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Lead;
use Modules\Project\Models\Project;

class LeadFollowUpContext
{
    public function __construct(private readonly CostLimiter $limiter) {}

    /**
     * @return array<string, mixed>
     */
    public function build(Lead $lead, ?User $user = null): array
    {
        $lead->loadMissing([
            'company:id,name',
            'assignee:id,name',
            'service:id,title',
            'services:services.id,title',
            'tags:id,name',
            'deal:id,title,status,value,currency,expected_close_date,pipeline_stage_id,company_id',
            'deal.pipelineStage:id,name',
            'deal.project:id,title,due_date,payment_status,project_status_id,company_id',
            'deal.project.status:id,name',
        ]);

        $data = [
            'lead' => $this->leadPayload($lead),
            'recent_activity' => $this->activities($lead, $user),
            'projects' => $this->projects($lead, $user),
        ];

        return $this->limiter->truncate($data);
    }

    /**
     * @return array<string, mixed>
     */
    private function leadPayload(Lead $lead): array
    {
        $services = $lead->services
            ->map(fn ($service) => $service->getTranslation('title', app()->getLocale()) ?: $service->title)
            ->filter()
            ->values()
            ->all();

        if ($services === [] && $lead->service) {
            $services = [
                $lead->service->getTranslation('title', app()->getLocale()) ?: $lead->service->title,
            ];
        }

        $transcript = collect($lead->chat_transcript ?? [])
            ->take(-6)
            ->map(fn ($message): array => [
                'role' => (string) ($message['role'] ?? 'bot'),
                'message' => (string) ($message['message'] ?? ''),
            ])
            ->values()
            ->all();

        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'job_title' => $lead->job_title,
            'status' => $lead->status,
            'source' => $lead->source,
            'company' => $lead->company?->name ?? $lead->company_name,
            'assignee' => $lead->assignee?->name,
            'services' => $services,
            'service_interest' => $lead->service_interest,
            'project_budget' => $lead->project_budget,
            'problem_statement' => $lead->problem_statement,
            'tags' => $lead->tags->map(fn ($tag) => $tag->display_name)->filter()->values()->all(),
            'locale' => $lead->locale,
            'created_at' => $lead->created_at?->toDateTimeString(),
            'updated_at' => $lead->updated_at?->toDateTimeString(),
            'chat_transcript' => $transcript,
            'deal' => $lead->deal === null ? null : [
                'id' => $lead->deal->id,
                'title' => $lead->deal->title,
                'status' => $lead->deal->status,
                'stage' => $lead->deal->pipelineStage?->name,
                'value' => $lead->deal->value,
                'currency' => $lead->deal->currency,
                'expected_close_date' => $lead->deal->expected_close_date?->toDateString(),
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function activities(Lead $lead, ?User $user): array
    {
        if ($user !== null && ! $user->can('crm.activities.view') && ! $user->can('crm.activities.create')) {
            return [];
        }

        $limit = min(8, $this->limiter->maxListItems());

        return $lead->crmActivities()
            ->with('user:id,name')
            ->limit($limit)
            ->get()
            ->map(fn (CrmActivity $activity): array => [
                'type' => $activity->type,
                'title' => $activity->title,
                'body' => $activity->body,
                'scheduled_at' => $activity->scheduled_at?->toDateTimeString(),
                'completed_at' => $activity->completed_at?->toDateTimeString(),
                'created_at' => $activity->created_at?->toDateTimeString(),
                'user' => $activity->user?->name,
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function projects(Lead $lead, ?User $user): array
    {
        if ($user !== null && ! $user->can('project.projects.view')) {
            return [];
        }

        $limit = min(5, $this->limiter->maxListItems());
        $query = Project::query()
            ->with(['status:id,name', 'employees:id,name'])
            ->latest();

        $query->where(function ($builder) use ($lead): void {
            if ($lead->company_id) {
                $builder->orWhere('company_id', $lead->company_id);
            }

            if ($lead->deal_id) {
                $builder->orWhere('deal_id', $lead->deal_id);
            }

            $dealCompanyId = $lead->deal?->company_id;
            if ($dealCompanyId) {
                $builder->orWhere('company_id', $dealCompanyId);
            }
        });

        if (! $lead->company_id && ! $lead->deal_id && $lead->deal?->company_id === null) {
            return [];
        }

        return $query
            ->limit($limit)
            ->get(['id', 'title', 'description', 'company_id', 'deal_id', 'project_status_id', 'budget', 'currency', 'payment_status', 'start_date', 'due_date'])
            ->unique('id')
            ->values()
            ->map(function (Project $project): array {
                $overdue = $project->due_date !== null
                    && $project->due_date->isPast()
                    && ! $project->isCompleted();

                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'status' => $project->status?->name,
                    'description' => $project->description,
                    'payment_status' => $project->payment_status,
                    'budget' => $project->budget,
                    'currency' => $project->currency,
                    'start_date' => $project->start_date?->toDateString(),
                    'due_date' => $project->due_date?->toDateString(),
                    'overdue' => $overdue,
                    'team' => $project->employees
                        ->pluck('name')
                        ->filter()
                        ->take($this->limiter->maxListItems())
                        ->values()
                        ->all(),
                ];
            })
            ->all();
    }
}
