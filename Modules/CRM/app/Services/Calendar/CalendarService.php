<?php

namespace Modules\CRM\Services\Calendar;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Quote;
use Modules\CRM\Models\Subscription;
use Modules\CRM\Support\CrmSubjectResolver;
use Modules\Project\Models\Project;

class CalendarService
{
    /**
     * @param  list<string>|null  $types
     * @return list<array<string, mixed>>
     */
    public function events(Carbon $start, Carbon $end, ?array $types = null): array
    {
        $types ??= ['activities', 'leads', 'deals', 'quotes', 'subscriptions', 'projects'];

        if ($types === []) {
            return [];
        }

        $events = collect();

        if (in_array('activities', $types, true)) {
            $events = $events->merge($this->activityEvents($start, $end, excludeLeads: in_array('leads', $types, true)));
        }

        if (in_array('leads', $types, true)) {
            $events = $events->merge($this->leadActivityEvents($start, $end));
        }

        if (in_array('deals', $types, true)) {
            $events = $events->merge($this->dealEvents($start, $end));
        }

        if (in_array('quotes', $types, true)) {
            $events = $events->merge($this->quoteEvents($start, $end));
        }

        if (in_array('subscriptions', $types, true)) {
            $events = $events->merge($this->subscriptionEvents($start, $end));
        }

        if (in_array('projects', $types, true) && class_exists(Project::class)) {
            $events = $events->merge($this->projectEvents($start, $end));
        }

        return $events->values()->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function activityEvents(Carbon $start, Carbon $end, bool $excludeLeads = false): Collection
    {
        $query = CrmActivity::query()
            ->with(['user:id,name'])
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$start, $end])
            ->orderBy('scheduled_at');

        if ($excludeLeads) {
            $query->where('subject_type', '!=', Lead::class);
        }

        return $query
            ->get()
            ->map(function (CrmActivity $activity) {
                $completed = $activity->completed_at !== null;
                $type = $activity->type;
                $title = $activity->title ?: __('crm::timeline.activity_types.'.$type);
                $url = $this->subjectUrl($activity->subject_type, (int) $activity->subject_id);

                return [
                    'id' => 'activity-'.$activity->id,
                    'title' => $title,
                    'start' => $activity->scheduled_at->toIso8601String(),
                    'allDay' => false,
                    'url' => $url,
                    'classNames' => array_values(array_filter([
                        'fc-event-'.$this->activityColor($type, $completed),
                        $completed ? 'fc-event-completed' : null,
                    ])),
                    'extendedProps' => [
                        'category' => 'activities',
                        'category_label' => __('crm::calendar.categories.activities'),
                        'type' => $type,
                        'type_label' => __('crm::timeline.activity_types.'.$type),
                        'description' => $activity->body,
                        'status' => $completed
                            ? __('crm::calendar.status.completed')
                            : __('crm::calendar.status.scheduled'),
                        'user' => $activity->user?->name,
                    ],
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function leadActivityEvents(Carbon $start, Carbon $end): Collection
    {
        return CrmActivity::query()
            ->with(['user:id,name', 'subject'])
            ->where('subject_type', Lead::class)
            ->where(function ($query) use ($start, $end) {
                $query
                    ->whereBetween('scheduled_at', [$start, $end])
                    ->orWhere(function ($inner) use ($start, $end) {
                        $inner
                            ->whereNull('scheduled_at')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderByRaw('COALESCE(scheduled_at, created_at)')
            ->get()
            ->map(function (CrmActivity $activity) {
                $completed = $activity->completed_at !== null;
                $type = $activity->type;
                $typeLabel = __('crm::timeline.activity_types.'.$type);
                $leadName = $activity->subject instanceof Lead
                    ? ($activity->subject->name ?: '#'.$activity->subject->getKey())
                    : null;
                $title = $activity->title ?: $typeLabel;
                if ($leadName) {
                    $title = $title.' — '.$leadName;
                }

                $startsAt = $activity->scheduled_at ?? $activity->created_at;
                $allDay = $activity->scheduled_at === null;

                return [
                    'id' => 'lead-activity-'.$activity->id,
                    'title' => $title,
                    'start' => $allDay
                        ? $startsAt->toDateString()
                        : $startsAt->toIso8601String(),
                    'allDay' => $allDay,
                    'url' => route('admin.leads.show', $activity->subject_id),
                    'classNames' => array_values(array_filter([
                        'fc-event-'.$this->activityColor($type, $completed),
                        'fc-event-solid-info',
                        $completed ? 'fc-event-completed' : null,
                    ])),
                    'extendedProps' => [
                        'category' => 'leads',
                        'category_label' => __('crm::calendar.categories.leads'),
                        'type' => $type,
                        'type_label' => $typeLabel,
                        'description' => $activity->body,
                        'status' => $completed
                            ? __('crm::calendar.status.completed')
                            : ($activity->scheduled_at
                                ? __('crm::calendar.status.scheduled')
                                : __('crm::calendar.status.logged')),
                        'user' => $activity->user?->name,
                    ],
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function dealEvents(Carbon $start, Carbon $end): Collection
    {
        return Deal::query()
            ->visibleTo()
            ->open()
            ->whereNotNull('expected_close_date')
            ->whereBetween('expected_close_date', [$start->toDateString(), $end->toDateString()])
            ->get(['id', 'title', 'expected_close_date', 'value', 'currency'])
            ->map(function (Deal $deal) {
                return [
                    'id' => 'deal-'.$deal->id,
                    'title' => $deal->title,
                    'start' => $deal->expected_close_date->toDateString(),
                    'allDay' => true,
                    'url' => route('admin.deals.show', $deal),
                    'classNames' => ['fc-event-primary'],
                    'extendedProps' => [
                        'category' => 'deals',
                        'category_label' => __('crm::calendar.categories.deals'),
                        'type_label' => __('crm::calendar.event_types.expected_close'),
                        'description' => $deal->value !== null
                            ? number_format((float) $deal->value, 2).' '.($deal->currency ?: '')
                            : null,
                        'status' => __('crm::calendar.status.open'),
                    ],
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function quoteEvents(Carbon $start, Carbon $end): Collection
    {
        return Quote::query()
            ->whereNotNull('expires_at')
            ->whereIn('status', [Quote::STATUS_DRAFT, Quote::STATUS_SENT])
            ->whereBetween('expires_at', [$start->toDateString(), $end->toDateString()])
            ->get(['id', 'quote_number', 'expires_at', 'status', 'total', 'currency'])
            ->map(function (Quote $quote) {
                return [
                    'id' => 'quote-'.$quote->id,
                    'title' => $quote->quote_number,
                    'start' => $quote->expires_at->toDateString(),
                    'allDay' => true,
                    'url' => route('admin.quotes.show', $quote),
                    'classNames' => ['fc-event-warning'],
                    'extendedProps' => [
                        'category' => 'quotes',
                        'category_label' => __('crm::calendar.categories.quotes'),
                        'type_label' => __('crm::calendar.event_types.quote_expiry'),
                        'description' => number_format((float) $quote->total, 2).' '.($quote->currency ?: ''),
                        'status' => $quote->status,
                    ],
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function subscriptionEvents(Carbon $start, Carbon $end): Collection
    {
        return Subscription::query()
            ->whereNotNull('renewal_at')
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
            ->whereBetween('renewal_at', [$start->toDateString(), $end->toDateString()])
            ->get(['id', 'name', 'renewal_at', 'status', 'amount', 'currency'])
            ->map(function (Subscription $subscription) {
                return [
                    'id' => 'subscription-'.$subscription->id,
                    'title' => $subscription->name,
                    'start' => $subscription->renewal_at->toDateString(),
                    'allDay' => true,
                    'url' => route('admin.subscriptions.show', $subscription),
                    'classNames' => ['fc-event-success'],
                    'extendedProps' => [
                        'category' => 'subscriptions',
                        'category_label' => __('crm::calendar.categories.subscriptions'),
                        'type_label' => __('crm::calendar.event_types.renewal'),
                        'description' => $subscription->amount !== null
                            ? number_format((float) $subscription->amount, 2).' '.($subscription->currency ?: '')
                            : null,
                        'status' => $subscription->status,
                    ],
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function projectEvents(Carbon $start, Carbon $end): Collection
    {
        $events = collect();
        $rangeStart = $start->toDateString();
        $rangeEnd = $end->toDateString();

        $projects = Project::query()
            ->where(function ($query) use ($rangeStart, $rangeEnd) {
                $query
                    ->whereBetween('start_date', [$rangeStart, $rangeEnd])
                    ->orWhereBetween('due_date', [$rangeStart, $rangeEnd]);
            })
            ->get(['id', 'title', 'start_date', 'due_date']);

        foreach ($projects as $project) {
            if ($project->start_date) {
                $startDate = $project->start_date->toDateString();
                if ($startDate >= $rangeStart && $startDate <= $rangeEnd) {
                    $events->push([
                        'id' => 'project-start-'.$project->id,
                        'title' => $project->title,
                        'start' => $startDate,
                        'allDay' => true,
                        'url' => route('admin.projects.show', $project),
                        'classNames' => ['fc-event-info'],
                        'extendedProps' => [
                            'category' => 'projects',
                            'category_label' => __('crm::calendar.categories.projects'),
                            'type_label' => __('crm::calendar.event_types.project_start'),
                            'status' => null,
                        ],
                    ]);
                }
            }

            if ($project->due_date) {
                $dueDate = $project->due_date->toDateString();
                if ($dueDate >= $rangeStart && $dueDate <= $rangeEnd) {
                    $events->push([
                        'id' => 'project-due-'.$project->id,
                        'title' => $project->title,
                        'start' => $dueDate,
                        'allDay' => true,
                        'url' => route('admin.projects.show', $project),
                        'classNames' => ['fc-event-danger'],
                        'extendedProps' => [
                            'category' => 'projects',
                            'category_label' => __('crm::calendar.categories.projects'),
                            'type_label' => __('crm::calendar.event_types.project_due'),
                            'status' => null,
                        ],
                    ]);
                }
            }
        }

        return $events;
    }

    protected function subjectUrl(?string $subjectType, int $subjectId): ?string
    {
        if (! $subjectType || ! $subjectId) {
            return null;
        }

        $map = array_flip(CrmSubjectResolver::MAP);
        $key = $map[$subjectType] ?? (isset(CrmSubjectResolver::MAP[$subjectType]) ? $subjectType : null);

        return match ($key) {
            'company' => route('admin.companies.show', $subjectId),
            'contact' => route('admin.contacts.show', $subjectId),
            'deal' => route('admin.deals.show', $subjectId),
            'subscription' => route('admin.subscriptions.show', $subjectId),
            'lead' => route('admin.leads.show', $subjectId),
            default => null,
        };
    }

    protected function activityColor(string $type, bool $completed): string
    {
        if ($completed) {
            return 'secondary';
        }

        return match ($type) {
            CrmActivity::TYPE_MEETING => 'primary',
            CrmActivity::TYPE_TASK => 'warning',
            CrmActivity::TYPE_CALL => 'info',
            CrmActivity::TYPE_EMAIL => 'success',
            default => 'dark',
        };
    }
}
