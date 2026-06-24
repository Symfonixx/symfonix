@section('title', __('crm::dashboard.title'))

@section('css')
<style>
    .crm-dash-hero {
        background: linear-gradient(135deg, #1e1e2d 0%, #3f4254 55%, #1b2559 100%);
        border-radius: 1rem;
        color: #fff;
    }
    .crm-metric-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1.5rem rgba(24, 28, 50, 0.06);
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
    }
    .crm-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.75rem 2rem rgba(24, 28, 50, 0.1);
    }
    .crm-metric-icon {
        width: 3rem;
        height: 3rem;
        border-radius: .85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
    .crm-trend-up { color: #50cd89; }
    .crm-trend-down { color: #f1416c; }
    .crm-funnel-row + .crm-funnel-row { margin-top: 1rem; }
    .crm-funnel-bar {
        height: .65rem;
        border-radius: 999px;
        background: #f1f1f4;
        overflow: hidden;
    }
    .crm-funnel-bar > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        transition: width .6s ease;
    }
    .crm-channel-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .85rem 0;
        border-bottom: 1px dashed #eff2f5;
    }
    .crm-channel-item:last-child { border-bottom: 0; }
    .crm-activity-item {
        display: flex;
        gap: .85rem;
        padding: .85rem 0;
        border-bottom: 1px solid #eff2f5;
    }
    .crm-activity-item:last-child { border-bottom: 0; }
    .crm-activity-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: .65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .crm-filter-card {
        border: 1px solid #eff2f5;
        border-radius: 1rem;
        background: #fff;
    }
    .crm-progress-thin {
        height: .45rem;
        border-radius: 999px;
    }
</style>
@endsection

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('CRM'), 'url' => route('admin.companies.index')],
            ['label' => __('crm::dashboard.menu')],
        ];
        $f = $analytics['filters'];
        $s = $analytics['summary'];
        $currency = $s['pipeline_value']['currency'] ?? 'USD';
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::dashboard.title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="crm-dash-hero p-6 p-lg-8 mb-8">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div>
                <h2 class="text-white fw-bold fs-2 mb-2">{{ __('crm::dashboard.title') }}</h2>
                <p class="text-white opacity-75 mb-0">{{ __('crm::dashboard.subtitle') }}</p>
            </div>
            <div class="text-white opacity-75 fs-7">
                {{ $f['date_from'] }} — {{ $f['date_to'] }}
            </div>
        </div>
    </div>

    <div class="crm-filter-card card card-body mb-8">
        <form method="GET" action="{{ route('admin.crm.dashboard') }}" class="row g-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">{{ __('crm::dashboard.filters.period') }}</label>
                <select name="period" id="crm-period" class="form-select form-select-solid" data-control="select2">
                    @foreach(__('crm::dashboard.filters.periods') as $key => $label)
                        <option value="{{ $key }}" @selected(($f['period'] ?? 'this_month') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 custom-range {{ ($f['period'] ?? '') === 'custom' ? '' : 'd-none' }}">
                <label class="form-label fw-semibold">{{ __('Start Date') }}</label>
                <input type="date" name="date_from" value="{{ $f['date_from'] ?? '' }}" class="form-control form-control-solid"/>
            </div>
            <div class="col-md-2 custom-range {{ ($f['period'] ?? '') === 'custom' ? '' : 'd-none' }}">
                <label class="form-label fw-semibold">{{ __('End Date') }}</label>
                <input type="date" name="date_to" value="{{ $f['date_to'] ?? '' }}" class="form-control form-control-solid"/>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">{{ __('crm::dashboard.filters.assignee') }}</label>
                <select name="assigned_to" class="form-select form-select-solid" data-control="select2">
                    <option value="">{{ __('crm::dashboard.filters.all_reps') }}</option>
                    @foreach($analytics['assignees'] as $assignee)
                        <option value="{{ $assignee->id }}" @selected((int) ($f['assigned_to'] ?? 0) === $assignee->id)>
                            {{ $assignee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>{{ __('crm::dashboard.filters.apply') }}
                </button>
            </div>
        </form>
    </div>

    @php
        $metricCards = [
            [
                'title' => __('crm::dashboard.metrics.total_leads'),
                'value' => number_format($s['total_leads']['value']),
                'trend' => $s['total_leads']['trend'],
                'icon' => 'person-lines-fill',
                'color' => 'primary',
                'hint' => null,
            ],
            [
                'title' => __('crm::dashboard.metrics.conversion_rate'),
                'value' => $s['conversion_rate']['value'].'%',
                'trend' => $s['conversion_rate']['trend'],
                'icon' => 'graph-up-arrow',
                'color' => 'success',
                'hint' => __('crm::dashboard.metrics.leads_converted', [
                    'converted' => $s['conversion_rate']['converted'],
                    'total' => $s['conversion_rate']['total'],
                ]),
            ],
            [
                'title' => __('crm::dashboard.metrics.pipeline_value'),
                'value' => number_format($s['pipeline_value']['value'], 0).' '.$currency,
                'trend' => $s['pipeline_value']['trend'],
                'icon' => 'cash-stack',
                'color' => 'warning',
                'hint' => null,
            ],
            [
                'title' => __('crm::dashboard.metrics.won_deals'),
                'value' => number_format($s['won_deals']['count']),
                'trend' => $s['won_deals']['trend'],
                'icon' => 'trophy',
                'color' => 'info',
                'hint' => __('crm::dashboard.metrics.won_value', [
                    'count' => $s['won_deals']['count'],
                    'value' => number_format($s['won_deals']['value'], 0).' '.$currency,
                ]),
            ],
        ];
    @endphp

    <div class="row g-5 g-xl-8 mb-8">
        @foreach($metricCards as $card)
            @php
                $trend = $card['trend'];
                $trendUp = $trend >= 0;
            @endphp
            <div class="col-sm-6 col-xl-3">
                <div class="card crm-metric-card">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="crm-metric-icon bg-light-{{ $card['color'] }} text-{{ $card['color'] }}">
                                <i class="bi bi-{{ $card['icon'] }}"></i>
                            </div>
                            <span class="fw-semibold fs-7 {{ $trendUp ? 'crm-trend-up' : 'crm-trend-down' }}">
                                <i class="bi bi-arrow-{{ $trendUp ? 'up' : 'down' }}-short"></i>
                                {{ ($trendUp ? '+' : '').$trend }}%
                            </span>
                        </div>
                        <div class="fs-2hx fw-bold text-gray-900 lh-1 mb-2">{{ $card['value'] }}</div>
                        <div class="text-gray-600 fw-semibold mb-1">{{ $card['title'] }}</div>
                        <div class="text-muted fs-8">{{ $card['hint'] ?: __('crm::dashboard.metrics.vs_previous') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('crm::dashboard.charts.pipeline_funnel') }}</h3>
                    <span class="text-muted fs-7">{{ __('crm::dashboard.charts.pipeline_hint') }}</span>
                </div>
                <div class="card-body pt-2">
                    <div class="row g-6">
                        <div class="col-lg-7">
                            @forelse($analytics['pipeline_funnel'] as $stage)
                                <div class="crm-funnel-row">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-semibold text-gray-800">
                                            <span class="badge badge-light-{{ $stage['color'] }} me-2">&nbsp;</span>
                                            {{ $stage['name'] }}
                                        </div>
                                        <div class="text-muted fs-7">
                                            <span class="fw-bold text-gray-800">{{ $stage['count'] }}</span>
                                            @if($stage['value'] > 0)
                                                · {{ number_format($stage['value'], 0) }} {{ $currency }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="crm-funnel-bar">
                                        <span class="bg-{{ $stage['color'] }}" style="width: {{ max($stage['percentage'], 4) }}%"></span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No data available') }}</p>
                            @endforelse
                        </div>
                        <div class="col-lg-5">
                            <canvas id="crmPipelineChart" height="280"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('crm::dashboard.charts.lead_channels') }}</h3>
                    <span class="text-muted fs-7">{{ __('crm::dashboard.charts.lead_channels_hint') }}</span>
                </div>
                <div class="card-body pt-2">
                    <canvas id="crmChannelsChart" height="220" class="mb-6"></canvas>
                    @forelse($analytics['lead_channels'] as $channel)
                        <div class="crm-channel-item">
                            <div>
                                <div class="fw-semibold text-gray-800">{{ $channel['label'] }}</div>
                                <div class="text-muted fs-8">{{ $channel['count'] }} {{ __('crm::dashboard.charts.leads') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-gray-900">{{ $channel['percentage'] }}%</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No data available') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-8">
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('crm::dashboard.leaderboard.title') }}</h3>
                    <span class="text-muted fs-7">{{ __('crm::dashboard.leaderboard.subtitle') }}</span>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gy-4 mb-0">
                            <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase">
                                <th>{{ __('crm::dashboard.leaderboard.rep') }}</th>
                                <th>{{ __('crm::dashboard.leaderboard.closed_deals') }}</th>
                                <th>{{ __('crm::dashboard.leaderboard.closed_value') }}</th>
                                <th>{{ __('crm::dashboard.leaderboard.target') }}</th>
                                <th>{{ __('crm::dashboard.leaderboard.achievement') }}</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-semibold">
                            @forelse($analytics['team_leaderboard'] as $rep)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="symbol symbol-35px symbol-circle bg-light-primary text-primary fw-bold">
                                                {{ strtoupper(substr($rep['name'], 0, 1)) }}
                                            </span>
                                            <span>{{ $rep['name'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $rep['closed_deals'] }}</td>
                                    <td>{{ number_format($rep['closed_value'], 0) }} {{ $currency }}</td>
                                    <td>{{ $rep['target'] }}</td>
                                    <td style="min-width: 140px;">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="progress flex-grow-1 crm-progress-thin bg-light">
                                                <div class="progress-bar bg-success" style="width: {{ $rep['achievement'] }}%"></div>
                                            </div>
                                            <span class="fs-7 fw-bold">{{ $rep['achievement'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-10">
                                        {{ __('crm::dashboard.leaderboard.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('crm::dashboard.activity.title') }}</h3>
                    <span class="text-muted fs-7">{{ __('crm::dashboard.activity.subtitle') }}</span>
                </div>
                <div class="card-body pt-0">
                    @forelse($analytics['recent_activity'] as $activity)
                        <div class="crm-activity-item">
                            <span class="crm-activity-icon bg-light-{{ $activity['color'] }} text-{{ $activity['color'] }}">
                                <i class="bi bi-{{ $activity['icon'] }}"></i>
                            </span>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-gray-800">{{ $activity['message'] }}</div>
                                <div class="text-muted fs-8">
                                    {{ $activity['user'] }} · {{ $activity['occurred_at']->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0 py-6 text-center">{{ __('crm::dashboard.activity.empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.getElementById('crm-period')?.addEventListener('change', function () {
        document.querySelectorAll('.custom-range').forEach(el => {
            el.classList.toggle('d-none', this.value !== 'custom');
        });
    });

    const chartColors = @json($analytics['chart_colors']);
    const funnelData = @json($analytics['pipeline_funnel']);
    const channelData = @json($analytics['lead_channels']);

    const pipelineCtx = document.getElementById('crmPipelineChart');
    if (pipelineCtx && funnelData.length) {
        new Chart(pipelineCtx, {
            type: 'bar',
            data: {
                labels: funnelData.map(item => item.name),
                datasets: [{
                    label: @json(__('crm::dashboard.charts.deals')),
                    data: funnelData.map(item => item.count),
                    backgroundColor: chartColors,
                    borderRadius: 8,
                    maxBarThickness: 36,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const channelsCtx = document.getElementById('crmChannelsChart');
    if (channelsCtx && channelData.length) {
        new Chart(channelsCtx, {
            type: 'doughnut',
            data: {
                labels: channelData.map(item => item.label),
                datasets: [{
                    data: channelData.map(item => item.count),
                    backgroundColor: chartColors,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 14 } }
                }
            }
        });
    }
</script>
@endsection
