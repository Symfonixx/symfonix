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
                <div class="card-body"><canvas id="chart-campaign-volume" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.lead_sources') }}</h3></div>
                <div class="card-body"><canvas id="chart-lead-sources" height="280"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-4">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.whatsapp_delivery') }}</h3></div>
                <div class="card-body"><canvas id="chart-whatsapp-delivery" height="260"></canvas></div>
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
</x-admin-layout>

@push('scripts')
<script>
(function () {
    var colors = @json($report['chart_colors']);
    var volume = @json($report['charts']['campaign_volume']);
    var sources = @json($report['charts']['lead_sources']);
    var delivery = @json($report['charts']['whatsapp_delivery']);
    var chartJsPromise = null;

    var ensureChartJs = function () {
        if (typeof Chart !== 'undefined') {
            return Promise.resolve();
        }

        if (!chartJsPromise) {
            chartJsPromise = new Promise(function (resolve, reject) {
                var sources = [
                    'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
                    'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js',
                    'https://unpkg.com/chart.js@4.4.1/dist/chart.umd.min.js'
                ];
                var tryLoad = function (index) {
                    if (index >= sources.length) {
                        reject(new Error('Chart.js failed to load'));
                        return;
                    }

                    var script = document.createElement('script');
                    script.src = sources[index];
                    script.async = true;
                    script.onload = function () { resolve(); };
                    script.onerror = function () { tryLoad(index + 1); };
                    document.head.appendChild(script);
                };

                tryLoad(0);
            });
        }

        return chartJsPromise;
    };

    var renderCharts = function () {
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
                options: { responsive: true, scales: { y: { beginAtZero: true } } },
            });
        }

        if (sourcesCanvas && Array.isArray(sources) && sources.length) {
            new Chart(sourcesCanvas, {
                type: 'pie',
                data: {
                    labels: sources.map(function (r) { return r.label; }),
                    datasets: [{ data: sources.map(function (r) { return r.count; }), backgroundColor: colors }],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }

        var deliveryTotal = Number((delivery && delivery.sent) || 0) + Number((delivery && delivery.failed) || 0) + Number((delivery && delivery.pending) || 0);
        if (deliveryCanvas && deliveryTotal > 0) {
            new Chart(deliveryCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Sent', 'Failed', 'Pending'],
                    datasets: [{ data: [delivery.sent, delivery.failed, delivery.pending], backgroundColor: [colors[1], colors[4], colors[6]] }],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }
    };

    ensureChartJs().then(renderCharts).catch(function () {
        var chartHolders = document.querySelectorAll('canvas[id^="chart-"]');
        for (var i = 0; i < chartHolders.length; i++) {
            if (chartHolders[i] && chartHolders[i].parentNode) {
                chartHolders[i].parentNode.innerHTML = '<div class="text-danger text-center py-10">Unable to load chart library</div>';
            }
        }
    });
})();
</script>
@endpush
