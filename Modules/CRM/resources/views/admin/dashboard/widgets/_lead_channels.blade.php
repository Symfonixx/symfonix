<div class="{{ $widget['span'] }} crm-widget" data-widget-id="{{ $widget['id'] }}">
    <div class="card crm-panel-card h-100">
        @include('crm::admin.dashboard.widgets._card_header', [
            'widget' => $widget,
            'title' => __('crm::dashboard.charts.lead_channels'),
            'subtitle' => __('crm::dashboard.charts.lead_channels_hint'),
        ])
        <div class="card-body pt-4">
            <div class="crm-chart-box crm-chart-box--donut mb-6">
                <canvas id="crmChannelsChart"></canvas>
            </div>
            @forelse($analytics['lead_channels'] as $index => $channel)
                <div class="crm-channel-item">
                    <div class="d-flex align-items-center gap-3">
                        <span class="crm-channel-dot" style="background: {{ $analytics['chart_colors'][$index % count($analytics['chart_colors'])] }}"></span>
                        <div>
                            <div class="fw-semibold text-gray-800">{{ $channel['label'] }}</div>
                            <div class="text-muted fs-8">
                                <i class="bi bi-people me-1"></i>{{ $channel['count'] }} {{ __('crm::dashboard.charts.leads') }}
                            </div>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fs-7">{{ $channel['percentage'] }}%</span>
                </div>
            @empty
                <p class="text-muted mb-0">{{ __('No data available') }}</p>
            @endforelse
        </div>
    </div>
</div>
