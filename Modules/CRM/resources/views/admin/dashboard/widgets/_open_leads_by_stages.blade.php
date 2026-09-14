@php
    $stages = $analytics['open_leads_by_stages'] ?? [];
    $stageCount = count($stages);
@endphp

<div class="{{ $widget['span'] }} crm-widget" data-widget-id="{{ $widget['id'] }}">
    <div class="card crm-panel-card h-100">
        @include('crm::admin.dashboard.widgets._card_header', [
            'widget' => $widget,
            'title' => __('crm::dashboard.charts.open_leads_by_stages'),
            'subtitle' => __('crm::dashboard.charts.open_leads_by_stages_hint'),
        ])
        <div class="card-body pt-2 pb-5">
            @if($stageCount > 0)
                <div class="crm-open-leads-funnel">
                    @foreach($stages as $index => $stage)
                        <div class="crm-open-leads-funnel__row">
                            <div class="crm-open-leads-funnel__meta">
                                <div class="crm-open-leads-funnel__count">{{ $stage['count'] }}</div>
                                <div class="crm-open-leads-funnel__name">{{ $stage['name'] }}</div>
                            </div>
                            <div class="crm-open-leads-funnel__track">
                                <div
                                    class="crm-open-leads-funnel__seg {{ $index === 0 ? 'crm-open-leads-funnel__seg--first' : 'crm-open-leads-funnel__seg--rest' }}"
                                    style="--seg-color: {{ $stage['color'] }};"
                                    title="{{ $stage['name'] }}: {{ $stage['count'] }}"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">{{ __('No data available') }}</p>
            @endif
        </div>
    </div>
</div>
