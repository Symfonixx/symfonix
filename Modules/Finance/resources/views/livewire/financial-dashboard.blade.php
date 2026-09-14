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
                    {{ number_format($lifetimeProfit, 2) }} {{ $displayCurrency }}
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
                        <span class="text-white fw-bold fs-4">{{ number_format($totalRevenue, 2) }} {{ $displayCurrency }}</span>
                    </div>
                    <div>
                        <span class="text-white opacity-75 d-block fs-8">{{ __('finance::finance.metrics.total_expenses') }}</span>
                        <span class="text-white fw-bold fs-4">{{ number_format($totalExpenses, 2) }} {{ $displayCurrency }}</span>
                    </div>
                    <div>
                        <span class="text-white opacity-75 d-block fs-8">{{ __('finance::finance.metrics.total_losses') }}</span>
                        <span class="text-white fw-bold fs-4">{{ number_format($totalLosses, 2) }} {{ $displayCurrency }}</span>
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
                <div wire:ignore.self id="finance-month-filter-wrap">
                    <select
                        id="finance-month-filter"
                        class="form-select form-select-solid"
                        multiple
                        data-placeholder="{{ __('finance::finance.filters.months_placeholder') }}"
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
                'value' => number_format($totalRevenue, 2).' '.$displayCurrency,
                'icon' => 'arrow-down-circle',
                'color' => 'success',
                'valueClass' => 'fin-color-profit',
            ],
            [
                'title' => __('finance::finance.metrics.total_expenses'),
                'value' => number_format($totalExpenses, 2).' '.$displayCurrency,
                'icon' => 'arrow-up-circle',
                'color' => 'warning',
                'valueClass' => 'fin-color-expense',
            ],
            [
                'title' => __('finance::finance.metrics.total_losses'),
                'value' => number_format($totalLosses, 2).' '.$displayCurrency,
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

    <div class="card border-0 shadow-sm mb-8">
        <div class="card-header border-0 pt-6">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100">
                <div>
                    <h3 class="card-title fw-bold mb-1">{{ __('finance::finance.charts.trend_title') }}</h3>
                    <span class="text-muted fs-7">{{ __('finance::finance.charts.trend_hint') }}</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <label class="form-label fw-semibold mb-0" for="finance-year-filter">
                        {{ __('finance::finance.charts.year') }}
                    </label>
                    <select
                        id="finance-year-filter"
                        wire:model.live="selectedYear"
                        class="form-select form-select-solid w-auto"
                    >
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body pt-2">
            @if(empty($trendChartData) || collect($trendChartData)->every(fn ($row) => $row['revenue'] == 0 && $row['expenses'] == 0))
                <p class="text-muted text-center py-10 mb-0">{{ __('finance::finance.charts.trend_empty', ['year' => $selectedYear]) }}</p>
            @else
                <div class="fin-chart-wrap" wire:ignore>
                    <canvas id="finance-trend-chart"></canvas>
                </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
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
                <div class="fin-chart-wrap" wire:ignore>
                    <canvas id="finance-performance-chart"></canvas>
                </div>
            @endif
        </div>
    </div>

    @php
        $chartLabels = [
            'profit' => __('finance::finance.charts.profit'),
            'expenses' => __('finance::finance.charts.expenses'),
            'losses' => __('finance::finance.charts.losses'),
            'revenue' => __('finance::finance.metrics.total_revenue'),
        ];
    @endphp

    @script
    <script>
        const chartLabels = @json($chartLabels);

        const chartColors = {
            profit: '#50cd89',
            profitMuted: 'rgba(80, 205, 137, 0.55)',
            expenses: '#f6aa33',
            expensesMuted: 'rgba(246, 170, 51, 0.55)',
            losses: '#f1416c',
            lossesMuted: 'rgba(241, 65, 108, 0.55)',
            revenue: '#3e97ff',
            revenueFill: 'rgba(62, 151, 255, 0.12)',
            expensesLine: '#f6aa33',
            expensesFill: 'rgba(246, 170, 51, 0.12)',
        };

        let performanceChart = null;
        let trendChart = null;
        let chartJsPromise = null;

        const ensureChartJs = () => {
            if (typeof Chart !== 'undefined') {
                return Promise.resolve();
            }

            if (!chartJsPromise) {
                chartJsPromise = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
                    script.async = true;
                    script.onload = () => resolve();
                    script.onerror = () => reject(new Error('Chart.js failed to load'));
                    document.head.appendChild(script);
                });
            }

            return chartJsPromise;
        };

        const destroyChart = (chart) => {
            if (chart) {
                chart.destroy();
            }

            return null;
        };

        const renderPerformanceChart = () => {
            const canvas = document.getElementById('finance-performance-chart');
            const payload = $wire.chartData ?? [];

            if (!canvas) {
                performanceChart = destroyChart(performanceChart);
                return;
            }

            if (!payload.length) {
                performanceChart = destroyChart(performanceChart);
                return;
            }

            const profits = payload.map((item) => Number(item.profit));
            const expenses = payload.map((item) => Number(item.expenses));
            const losses = payload.map((item) => Number(item.losses));
            const maxProfit = Math.max(...profits);
            const maxExpenses = Math.max(...expenses);
            const maxLosses = Math.max(...losses);

            performanceChart = destroyChart(performanceChart);
            performanceChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: payload.map((item) => item.label),
                    datasets: [
                        {
                            label: chartLabels.profit,
                            data: profits,
                            backgroundColor: profits.map((value) => value === maxProfit && maxProfit > 0 ? chartColors.profit : chartColors.profitMuted),
                            borderRadius: 6,
                            maxBarThickness: 28,
                        },
                        {
                            label: chartLabels.expenses,
                            data: expenses,
                            backgroundColor: expenses.map((value) => value === maxExpenses && maxExpenses > 0 ? chartColors.expenses : chartColors.expensesMuted),
                            borderRadius: 6,
                            maxBarThickness: 28,
                        },
                        {
                            label: chartLabels.losses,
                            data: losses,
                            backgroundColor: losses.map((value) => value === maxLosses && maxLosses > 0 ? chartColors.losses : chartColors.lossesMuted),
                            borderRadius: 6,
                            maxBarThickness: 28,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, padding: 16, usePointStyle: true },
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const value = context.parsed.y ?? 0;
                                    return `${context.dataset.label}: ${value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(226, 232, 240, 0.8)' },
                            ticks: {
                                callback: (value) => Number(value).toLocaleString(),
                            },
                        },
                        x: {
                            grid: { display: false },
                        },
                    },
                },
            });
        };

        const renderTrendChart = () => {
            const canvas = document.getElementById('finance-trend-chart');
            const payload = $wire.trendChartData ?? [];

            if (!canvas) {
                trendChart = destroyChart(trendChart);
                return;
            }

            const hasValues = payload.some((item) => Number(item.revenue) > 0 || Number(item.expenses) > 0);

            if (!payload.length || !hasValues) {
                trendChart = destroyChart(trendChart);
                return;
            }

            const revenues = payload.map((item) => Number(item.revenue));
            const expenses = payload.map((item) => Number(item.expenses));

            trendChart = destroyChart(trendChart);
            trendChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: payload.map((item) => item.label),
                    datasets: [
                        {
                            label: chartLabels.revenue,
                            data: revenues,
                            borderColor: chartColors.revenue,
                            backgroundColor: chartColors.revenueFill,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2,
                        },
                        {
                            label: chartLabels.expenses,
                            data: expenses,
                            borderColor: chartColors.expensesLine,
                            backgroundColor: chartColors.expensesFill,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, padding: 16, usePointStyle: true },
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const value = context.parsed.y ?? 0;
                                    return `${context.dataset.label}: ${value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(226, 232, 240, 0.8)' },
                            ticks: {
                                callback: (value) => Number(value).toLocaleString(),
                            },
                        },
                        x: {
                            grid: { display: false },
                        },
                    },
                },
            });
        };

        const renderFinanceCharts = () => {
            ensureChartJs()
                .then(() => {
                    requestAnimationFrame(() => {
                        renderPerformanceChart();
                        renderTrendChart();
                    });
                })
                .catch(() => {});
        };

        renderFinanceCharts();
        $wire.$watch('chartData', () => renderFinanceCharts());
        $wire.$watch('trendChartData', () => renderFinanceCharts());
        $wire.$watch('selectedYear', () => renderFinanceCharts());

        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    renderFinanceCharts();
                });
            });
        });
    </script>
    @endscript
</div>
