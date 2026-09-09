@php
    $currency = $analytics['currency'] ?? 'USD';
    $rows = $analytics['team_leaderboard'] ?? [];
@endphp

<div class="{{ $widget['span'] }} crm-widget" data-widget-id="{{ $widget['id'] }}">
    <div class="card crm-panel-card h-100">
        @include('crm::admin.dashboard.widgets._card_header', [
            'widget' => $widget,
            'title' => __('crm::dashboard.leaderboard.title'),
            'subtitle' => __('crm::dashboard.leaderboard.subtitle'),
        ])
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gy-4 mb-0">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase">
                        <th><i class="bi bi-person me-1"></i>{{ __('crm::dashboard.leaderboard.rep') }}</th>
                        <th><i class="bi bi-trophy me-1"></i>{{ __('crm::dashboard.leaderboard.closed_deals') }}</th>
                        <th><i class="bi bi-currency-dollar me-1"></i>{{ __('crm::dashboard.leaderboard.closed_value') }}</th>
                        <th><i class="bi bi-bullseye me-1"></i>{{ __('crm::dashboard.leaderboard.target') }}</th>
                        <th><i class="bi bi-graph-up-arrow me-1"></i>{{ __('crm::dashboard.leaderboard.achievement') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                    @forelse($rows as $rep)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="symbol symbol-35px symbol-circle">
                                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                                            {{ strtoupper(substr($rep['name'], 0, 1)) }}
                                        </span>
                                    </span>
                                    <span>{{ $rep['name'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-success">{{ $rep['closed_deals'] }}</span>
                            </td>
                            <td>{{ number_format($rep['closed_value'], 0) }} {{ $currency }}</td>
                            <td>{{ $rep['target'] }}</td>
                            <td style="min-width: 140px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="progress flex-grow-1 crm-progress-thin bg-light-success">
                                        <div class="progress-bar bg-success" style="width: {{ $rep['achievement'] }}%"></div>
                                    </div>
                                    <span class="fs-7 fw-bold {{ $rep['achievement'] >= 100 ? 'text-success' : 'text-gray-800' }}">
                                        {{ $rep['achievement'] }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-10">
                                <i class="bi bi-bar-chart fs-2 d-block mb-3 text-gray-400"></i>
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
