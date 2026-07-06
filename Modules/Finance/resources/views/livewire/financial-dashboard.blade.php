<div>
    <style>
        .fin-dash-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 45%, #0b3d2e 100%);
            border-radius: 1rem;
            color: #fff;
            box-shadow: 0 1rem 2.5rem rgba(15, 23, 42, 0.18);
        }
        .fin-metric-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(24, 28, 50, 0.06);
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
        }
        .fin-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.75rem 2rem rgba(24, 28, 50, 0.1);
        }
        .fin-metric-icon {
            width: 3rem;
            height: 3rem;
            border-radius: .85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .fin-filter-card {
            border: 1px solid #eff2f5;
            border-radius: 1rem;
            background: #fff;
        }
        .fin-hero-profit {
            font-size: clamp(2rem, 4vw, 3.25rem);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
        .fin-chart-wrap {
            position: relative;
            height: 360px;
        }
        .fin-color-profit { color: #50cd89; }
        .fin-color-expense { color: #f6aa33; }
        .fin-color-loss { color: #f1416c; }
    </style>

    <div class="fin-dash-hero p-6 p-lg-8 mb-8">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-4">
            <div>
                <span class="text-white opacity-75 fw-semibold fs-7 text-uppercase tracking-wider d-block mb-2">
                    {{ $isLifetimeView
                        ? __('finance::finance.metrics.lifetime_scope')
                        : __('finance::finance.metrics.filtered_scope') }}
                </span>
                <h2 class="text-white fw-bold fs-2 mb-3">
                    {{ __('finance::finance.metrics.lifetime_profit') }}
                </h2>
                <div class="fin-hero-profit fw-bolder {{ $lifetimeProfit >= 0 ? 'fin-color-profit' : 'fin-color-loss' }}">
                    {{ number_format($lifetimeProfit, 2) }}
                </div>
                <p class="text-white opacity-75 mb-0 mt-3 fs-7">
                    {{ $isLifetimeView
                        ? __('finance::finance.metrics.lifetime_hint')
                        : __('finance::finance.metrics.filtered_hint', ['count' => count($selectedMonths)]) }}
                </p>
            </div>
            <div class="text-end">
                <div class="d-flex flex-wrap gap-4 justify-content-end">
                    <div>
                        <span class="text-white opacity-75 d-block fs-8">{{ __('finance::finance.metrics.total_revenue') }}</span>
                        <span class="text-white fw-bold fs-4">{{ number_format($totalRevenue, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-white opacity-75 d-block fs-8">{{ __('finance::finance.metrics.total_expenses') }}</span>
                        <span class="text-white fw-bold fs-4">{{ number_format($totalExpenses, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-white opacity-75 d-block fs-8">{{ __('finance::finance.metrics.total_losses') }}</span>
                        <span class="text-white fw-bold fs-4">{{ number_format($totalLosses, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fin-filter-card card card-body mb-8">
        <div class="row g-4 align-items-end">
            <div class="col-lg-8">
                <label class="form-label fw-semibold" for="finance-month-filter">
                    {{ __('finance::finance.filters.months') }}
                </label>
                <div wire:ignore>
                    <select
                        id="finance-month-filter"
                        class="form-select form-select-solid"
                        multiple
                        data-control="select2"
                        data-placeholder="{{ __('finance::finance.filters.months_placeholder') }}"
                        data-close-on-select="false"
                    >
                        @foreach($availableMonths as $month)
                            <option value="{{ $month['key'] }}" @selected(in_array($month['key'], $selectedMonths, true))>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-text">{{ __('finance::finance.filters.months_hint') }}</div>
            </div>
            <div class="col-lg-4 d-flex gap-3">
                <button type="button" class="btn btn-light-primary flex-grow-1" wire:click="clearMonthFilter">
                    <i class="bi bi-infinity me-1"></i>{{ __('finance::finance.filters.all_months') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-8">
        <div class="card-header border-0 pt-6">
            <div>
                <h3 class="card-title fw-bold mb-1">{{ __('finance::finance.metrics.saas_metrics') }}</h3>
                <span class="text-muted fs-7">{{ __('finance::finance.metrics.saas_hint') }}</span>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="text-muted fs-7">{{ __('finance::finance.metrics.mrr') }}</div>
                    <div class="fs-2hx fw-bold text-primary">{{ number_format($mrr, 2) }} {{ $saasCurrency }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted fs-7">{{ __('finance::finance.metrics.arr') }}</div>
                    <div class="fs-2hx fw-bold text-success">{{ number_format($arr, 2) }} {{ $saasCurrency }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted fs-7">{{ __('finance::finance.metrics.active_subscriptions') }}</div>
                    <div class="fs-2hx fw-bold">{{ $activeSubscriptions }}</div>
                </div>
            </div>
        </div>
    </div>

    @php
        $metricCards = [
            [
                'title' => __('finance::finance.metrics.total_revenue'),
                'value' => number_format($totalRevenue, 2),
                'icon' => 'arrow-down-circle',
                'color' => 'success',
                'valueClass' => 'fin-color-profit',
            ],
            [
                'title' => __('finance::finance.metrics.total_expenses'),
                'value' => number_format($totalExpenses, 2),
                'icon' => 'arrow-up-circle',
                'color' => 'warning',
                'valueClass' => 'fin-color-expense',
            ],
            [
                'title' => __('finance::finance.metrics.total_losses'),
                'value' => number_format($totalLosses, 2),
                'icon' => 'graph-down-arrow',
                'color' => 'danger',
                'valueClass' => 'fin-color-loss',
            ],
        ];
    @endphp

    <div class="row g-5 g-xl-8 mb-8">
        @foreach($metricCards as $card)
            <div class="col-md-4">
                <div class="card fin-metric-card">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="fin-metric-icon bg-light-{{ $card['color'] }} text-{{ $card['color'] }}">
                                <i class="bi bi-{{ $card['icon'] }}"></i>
                            </div>
                        </div>
                        <div class="fs-2hx fw-bold lh-1 mb-2 {{ $card['valueClass'] }}">{{ $card['value'] }}</div>
                        <div class="text-gray-600 fw-semibold">{{ $card['title'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm" wire:key="finance-chart-{{ md5(json_encode($selectedMonths)) }}">
        <div class="card-header border-0 pt-6">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100">
                <div>
                    <h3 class="card-title fw-bold mb-1">{{ __('finance::finance.charts.performance_title') }}</h3>
                    <span class="text-muted fs-7">{{ __('finance::finance.charts.performance_hint') }}</span>
                </div>
                <div class="d-flex flex-wrap gap-4 fs-8 fw-semibold">
                    <span><i class="bi bi-square-fill fin-color-profit me-1"></i>{{ __('finance::finance.charts.profit') }}</span>
                    <span><i class="bi bi-square-fill fin-color-expense me-1"></i>{{ __('finance::finance.charts.expenses') }}</span>
                    <span><i class="bi bi-square-fill fin-color-loss me-1"></i>{{ __('finance::finance.charts.losses') }}</span>
                </div>
            </div>
        </div>
        <div class="card-body pt-2">
            @if(empty($chartData))
                <p class="text-muted text-center py-10 mb-0">{{ __('finance::finance.charts.empty') }}</p>
            @else
                <div class="fin-chart-wrap">
                    <canvas id="finance-performance-chart"></canvas>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        const syncFinanceChartPayload = () => {
            window.__financeChartPayload = $wire.chartData ?? [];
            document.dispatchEvent(new CustomEvent('finance-chart-render'));
        };

        syncFinanceChartPayload();
        $wire.$watch('chartData', () => syncFinanceChartPayload());
    </script>
    @endscript
</div>
