@section('title', __('reporting::report.pages.marketing_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('reporting::report.menu.reports')],
            ['label' => __('reporting::report.pages.marketing_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('reporting::report.pages.marketing_title')" :breadcrumbItems="$breadcrumbItems"/>
    @include('reporting::admin._export_buttons', ['department' => 'marketing', 'filters' => $filters])
@endsection

<x-admin-layout>
    @include('reporting::admin._tabs', ['active' => 'marketing'])
    @include('reporting::admin._filters', ['filters' => $filters])
    @include('reporting::admin._kpi_cards', ['kpis' => $report['kpis'], 'columns' => 3])

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.campaign_volume') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-campaign-volume', 'height' => 280])
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.lead_sources') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-lead-sources', 'height' => 280])
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-4">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.whatsapp_delivery') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-whatsapp-delivery', 'height' => 260])
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.conversion_by_source') }}</h3></div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
                                <th>{{ __('Source') }}</th>
                                <th>{{ __('Leads') }}</th>
                                <th>{{ __('Conversions') }}</th>
                                <th>{{ __('Rate') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($report['charts']['conversion_by_source'] as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td>{{ $row['leads'] }}</td>
                                    <td>{{ $row['conversions'] }}</td>
                                    <td class="fw-bold">{{ $row['rate'] }}%</td>
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
            var volume = @json($report['charts']['campaign_volume']);
            var sources = @json($report['charts']['lead_sources']);
            var delivery = @json($report['charts']['whatsapp_delivery']);
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

            var volumeCanvas = document.getElementById('chart-campaign-volume');
            var sourcesCanvas = document.getElementById('chart-lead-sources');
            var deliveryCanvas = document.getElementById('chart-whatsapp-delivery');

            if (volumeCanvas && Array.isArray(volume) && volume.length) {
                new Chart(volumeCanvas, {
                    type: 'bar',
                    data: {
                        labels: volume.map(function (r) { return r.label; }),
                        datasets: [
                            { label: '{{ __("Email") }}', data: volume.map(function (r) { return r.email; }), backgroundColor: colors[0] },
                            { label: 'WhatsApp', data: volume.map(function (r) { return r.whatsapp; }), backgroundColor: colors[1] },
                        ],
                    },
                    options: { scales: { y: { beginAtZero: true } } },
                });
            } else {
                showMessage(volumeCanvas, noDataText, false);
            }

            if (sourcesCanvas && Array.isArray(sources) && sources.length) {
                new Chart(sourcesCanvas, {
                    type: 'pie',
                    data: {
                        labels: sources.map(function (r) { return r.label; }),
                        datasets: [{ data: sources.map(function (r) { return r.count; }), backgroundColor: colors }],
                    },
                    options: { plugins: { legend: { position: 'bottom' } } },
                });
            } else {
                showMessage(sourcesCanvas, noDataText, false);
            }

            var deliveryTotal = Number((delivery && delivery.sent) || 0) + Number((delivery && delivery.failed) || 0) + Number((delivery && delivery.pending) || 0);
            if (deliveryCanvas && deliveryTotal > 0) {
                new Chart(deliveryCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sent', 'Failed', 'Pending'],
                        datasets: [{ data: [delivery.sent, delivery.failed, delivery.pending], backgroundColor: [colors[1], colors[4], colors[6]] }],
                    },
                    options: { plugins: { legend: { position: 'bottom' } } },
                });
            } else {
                showMessage(deliveryCanvas, noDataText, false);
            }
        })();
        </script>
    @endpush
</x-admin-layout>
