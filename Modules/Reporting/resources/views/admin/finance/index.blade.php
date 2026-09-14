@section('title', __('reporting::report.pages.finance_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('reporting::report.menu.reports')],
            ['label' => __('reporting::report.pages.finance_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('reporting::report.pages.finance_title')" :breadcrumbItems="$breadcrumbItems"/>
    @include('reporting::admin._export_buttons', ['department' => 'finance', 'filters' => $filters])
@endsection

<x-admin-layout>
    @include('reporting::admin._tabs', ['active' => 'finance'])
    @include('reporting::admin._filters', ['filters' => $filters])
    @include('reporting::admin._kpi_cards', ['kpis' => $report['kpis'], 'currency' => $report['currency'] ?? null, 'columns' => 3])

    <div class="row g-5 mb-5">
        <div class="col-lg-8">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.monthly_trend') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-monthly-trend', 'height' => 280])
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.expense_breakdown') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-expense-breakdown', 'height' => 280])
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.ar_aging') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-ar-aging', 'height' => 260])
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.tax_summary') }}</h3></div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
                                <th>{{ __('tax::report.fields.tax_rate') }}</th>
                                <th>{{ __('tax::report.fields.output_tax') }}</th>
                                <th>{{ __('tax::report.fields.input_tax') }}</th>
                                <th>{{ __('tax::report.fields.net_tax_payable') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($report['tables']['tax_by_rate'] as $row)
                                <tr>
                                    <td>{{ $row['tax_rate_name'] }}</td>
                                    <td>{{ number_format($row['output_tax'], 2) }}</td>
                                    <td>{{ number_format($row['input_tax'], 2) }}</td>
                                    <td class="fw-bold">{{ number_format($row['net_tax'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-6">{{ __('reporting::report.no_data') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('reporting::admin._chart_js')
        <script>
        (function () {
            var colors = @json($report['chart_colors']);
            var monthly = @json($report['charts']['monthly_trend']);
            var expenses = @json($report['charts']['expense_breakdown']);
            var arAging = @json($report['charts']['ar_aging']);
            var noDataText = @json(__('reporting::report.no_data'));
            var showMessage = function (canvas, message, danger) {
                if (!canvas || !canvas.parentNode) {
                    return;
                }
                canvas.parentNode.innerHTML = '<div class="' + (danger ? 'text-danger' : 'text-muted') + ' text-center py-10">' + message + '</div>';
            };

            if (typeof Chart === 'undefined') {
                var missing = document.querySelectorAll('canvas[id^="chart-"]');
                for (var i = 0; i < missing.length; i++) {
                    showMessage(missing[i], 'Unable to load chart library', true);
                }
                return;
            }

            var monthlyCanvas = document.getElementById('chart-monthly-trend');
            var expensesCanvas = document.getElementById('chart-expense-breakdown');
            var arCanvas = document.getElementById('chart-ar-aging');

            if (monthlyCanvas && Array.isArray(monthly) && monthly.length) {
                new Chart(monthlyCanvas, {
                    type: 'bar',
                    data: {
                        labels: monthly.map(function (r) { return r.label; }),
                        datasets: [
                            { label: '{{ __("reporting::report.kpis.revenue") }}', data: monthly.map(function (r) { return r.revenue; }), backgroundColor: colors[1] },
                            { label: '{{ __("reporting::report.kpis.expenses") }}', data: monthly.map(function (r) { return r.expenses; }), backgroundColor: colors[4] },
                            { label: '{{ __("reporting::report.kpis.profit") }}', data: monthly.map(function (r) { return r.profit; }), type: 'line', borderColor: colors[0], backgroundColor: 'transparent', tension: 0.3 },
                        ],
                    },
                    options: { scales: { y: { beginAtZero: true } } },
                });
            } else {
                showMessage(monthlyCanvas, noDataText, false);
            }

            if (expensesCanvas && Array.isArray(expenses) && expenses.length) {
                new Chart(expensesCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: expenses.map(function (r) { return r.category; }),
                        datasets: [{ data: expenses.map(function (r) { return r.amount; }), backgroundColor: colors }],
                    },
                    options: { plugins: { legend: { position: 'bottom' } } },
                });
            } else {
                showMessage(expensesCanvas, noDataText, false);
            }

            if (arCanvas && arAging && Array.isArray(arAging.values) && arAging.values.some(function (v) { return Number(v) > 0; })) {
                new Chart(arCanvas, {
                    type: 'bar',
                    data: {
                        labels: arAging.labels,
                        datasets: [{ data: arAging.values, backgroundColor: colors[2] }],
                    },
                    options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } },
                });
            } else {
                showMessage(arCanvas, noDataText, false);
            }
        })();
        </script>
    @endpush
</x-admin-layout>
