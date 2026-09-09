<div class="{{ $widget['span'] }} crm-widget" data-widget-id="{{ $widget['id'] }}">
    <div class="card crm-panel-card h-100">
        @include('crm::admin.dashboard.widgets._card_header', [
            'widget' => $widget,
            'title' => __('crm::dashboard.activity.title'),
            'subtitle' => __('crm::dashboard.activity.subtitle'),
        ])
        <div class="card-body pt-4">
            @if($analytics['recent_activity'])
                <div class="crm-timeline">
                    @foreach($analytics['recent_activity'] as $activity)
                        <div class="crm-timeline-item">
                            <div class="crm-timeline-rail">
                                <span class="crm-timeline-dot bg-light-{{ $activity['color'] }} text-{{ $activity['color'] }}">
                                    <i class="bi bi-{{ $activity['icon'] }}"></i>
                                </span>
                            </div>
                            <div class="crm-timeline-card">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                    <div class="d-flex align-items-center gap-3 min-w-0">
                                        <span class="symbol symbol-35px symbol-circle bg-light-{{ $activity['color'] }}">
                                            <span class="symbol-label fw-bold text-{{ $activity['color'] }}">{{ $activity['initials'] ?? '?' }}</span>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="fw-semibold text-gray-900 text-truncate">{{ $activity['message'] }}</div>
                                            <div class="text-muted fs-8">
                                                <i class="bi bi-person me-1"></i>{{ $activity['user'] }}
                                                <span class="mx-2">·</span>
                                                <i class="bi bi-clock me-1"></i>{{ $activity['occurred_at']->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-{{ $activity['color'] }} text-nowrap">
                                        {{ $activity['type_label'] ?? ucfirst($activity['event'] ?? 'event') }}
                                    </span>
                                </div>
                                @if(! empty($activity['excerpt']))
                                    <p class="text-gray-600 fs-7 mb-3">{{ $activity['excerpt'] }}</p>
                                @endif
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted fs-8">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $activity['occurred_at']->format('M d, Y H:i') }}
                                    </span>
                                    @if(! empty($activity['url']))
                                        <a href="{{ $activity['url'] }}" class="btn btn-sm btn-light-{{ $activity['color'] }}">
                                            {{ __('crm::dashboard.activity.view') }}
                                            <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-1"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <span class="crm-empty-icon bg-light-secondary text-secondary mb-4">
                        <i class="bi bi-activity fs-2"></i>
                    </span>
                    <p class="text-muted mb-0">{{ __('crm::dashboard.activity.empty') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
