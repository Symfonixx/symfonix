@section('title', __('finance::finance.pages.dashboard_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::finance.pages.dashboard_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::finance.pages.dashboard_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
  <livewire:finance.financial-dashboard />
</x-admin-layout>

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const labels = {
            profit: @json(__('finance::finance.charts.profit')),
            expenses: @json(__('finance::finance.charts.expenses')),
            losses: @json(__('finance::finance.charts.losses')),
        };
        const colors = {
            profit: '#50cd89',
            profitMuted: 'rgba(80, 205, 137, 0.55)',
            expenses: '#f6aa33',
            expensesMuted: 'rgba(246, 170, 51, 0.55)',
            losses: '#f1416c',
            lossesMuted: 'rgba(241, 65, 108, 0.55)',
        };

        const renderFinanceChart = () => {
            const chartPayload = window.__financeChartPayload || [];
            const canvas = document.getElementById('finance-performance-chart');

            if (typeof Chart === 'undefined') {
                return;
            }

            if (!canvas) {
                if (window.__financePerformanceChart) {
                    window.__financePerformanceChart.destroy();
                    window.__financePerformanceChart = null;
                }

                return;
            }

            if (window.__financePerformanceChart) {
                window.__financePerformanceChart.destroy();
                window.__financePerformanceChart = null;
            }

            if (!chartPayload.length) {
                return;
            }

            const profits = chartPayload.map(item => item.profit);
            const expenses = chartPayload.map(item => item.expenses);
            const losses = chartPayload.map(item => item.losses);
            const maxProfit = Math.max(...profits);
            const maxExpenses = Math.max(...expenses);
            const maxLosses = Math.max(...losses);

            window.__financePerformanceChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartPayload.map(item => item.label),
                    datasets: [
                        {
                            label: labels.profit,
                            data: profits,
                            backgroundColor: profits.map(value => value === maxProfit && maxProfit > 0 ? colors.profit : colors.profitMuted),
                            borderRadius: 6,
                            maxBarThickness: 28,
                        },
                        {
                            label: labels.expenses,
                            data: expenses,
                            backgroundColor: expenses.map(value => value === maxExpenses && maxExpenses > 0 ? colors.expenses : colors.expensesMuted),
                            borderRadius: 6,
                            maxBarThickness: 28,
                        },
                        {
                            label: labels.losses,
                            data: losses,
                            backgroundColor: losses.map(value => value === maxLosses && maxLosses > 0 ? colors.losses : colors.lossesMuted),
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

        const scheduleFinanceChartRender = () => {
            requestAnimationFrame(renderFinanceChart);
        };

        document.addEventListener('finance-chart-render', scheduleFinanceChartRender);
        scheduleFinanceChartRender();

        const initFinanceMonthSelect = () => {
            const select = document.getElementById('finance-month-filter');
            if (!select || typeof $ === 'undefined' || !$.fn.select2) {
                return;
            }

            const $select = $(select);
            const root = select.closest('[wire\\:id]');
            const component = root ? Livewire.find(root.getAttribute('wire:id')) : null;

            if (!component) {
                return;
            }

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.off('change.financeMonths');
                $select.select2('destroy');
            }

            $select.select2({
                placeholder: select.getAttribute('data-placeholder') || '',
                allowClear: true,
                closeOnSelect: false,
                width: '100%',
            });

            $select.val(component.get('selectedMonths') || []).trigger('change.select2');

            $select.on('change.financeMonths', function () {
                component.set('selectedMonths', $(this).val() || []);
            });
        };

        const clearFinanceMonthSelect = () => {
            const select = document.getElementById('finance-month-filter');
            if (!select || typeof $ === 'undefined') {
                return;
            }

            $(select).val(null).trigger('change.select2');
        };

        const setupFinanceMonthFilter = () => {
            initFinanceMonthSelect();
            Livewire.on('finance-month-filter-cleared', clearFinanceMonthSelect);
        };

        if (window.Livewire) {
            setupFinanceMonthFilter();
        }

        document.addEventListener('livewire:initialized', setupFinanceMonthFilter);
    })();
</script>
@endsection
