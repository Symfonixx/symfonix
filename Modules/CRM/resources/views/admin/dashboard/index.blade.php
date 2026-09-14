@php
    $breadcrumbItems = [
        ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
        ['label' => __('CRM'), 'url' => route('admin.companies.index')],
        ['label' => __('crm::dashboard.menu')],
    ];
    $f = $analytics['filters'];
    $visibleWidgets = collect($layout)->where('visible', true)->sortBy('order')->values();
    $layoutStateForJs = collect($layout)->map(function ($w) {
        return [
            'id' => $w['id'],
            'visible' => (bool) $w['visible'],
            'order' => (int) $w['order'],
        ];
    })->values()->all();
@endphp

@section('title', __('crm::dashboard.title'))

@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('crm::dashboard.title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="crm-dash-hero p-6 p-lg-8 mb-8">
        <div class="row g-6 align-items-center">
            <div class="col-xl-6">
                <span class="badge badge-light-primary mb-3">
                    <i class="bi bi-graph-up me-1"></i>{{ __('crm::dashboard.menu') }}
                </span>
                <h2 class="text-white fw-bold fs-2x mb-2">{{ __('crm::dashboard.title') }}</h2>
                <p class="text-white opacity-75 mb-4">{{ __('crm::dashboard.subtitle') }}</p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="crm-hero-chip">
                        <i class="bi bi-calendar3 me-2"></i>{{ $f['date_from'] }} — {{ $f['date_to'] }}
                    </span>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="offcanvas" data-bs-target="#crmDashboardCustomize">
                        <i class="bi bi-sliders me-1"></i>{{ __('crm::dashboard.customize.button') }}
                    </button>
                </div>
            </div>
            <div class="col-xl-6">
                @include('crm::admin.dashboard.widgets._quick_actions')
            </div>
        </div>
    </div>

    <div class="crm-filter-card card card-body mb-8">
        <form method="GET" action="{{ route('admin.crm.dashboard') }}" class="row g-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-calendar3 text-primary me-1"></i>{{ __('crm::dashboard.filters.period') }}
                </label>
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
                <label class="form-label fw-semibold">
                    <i class="bi bi-person-badge text-info me-1"></i>{{ __('crm::dashboard.filters.assignee') }}
                </label>
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

    <div id="crm-widget-grid" class="row g-5 g-xl-8 mb-8">
        @foreach($visibleWidgets as $widget)
            @if($widget['type'] === 'metric')
                @include('crm::admin.dashboard.widgets._metric', ['widget' => $widget, 'analytics' => $analytics])
            @elseif($widget['id'] === 'top_customers')
                @include('crm::admin.dashboard.widgets._top_customers', ['widget' => $widget, 'analytics' => $analytics])
            @elseif($widget['id'] === 'sales_performance')
                @include('crm::admin.dashboard.widgets._sales_performance', ['widget' => $widget, 'analytics' => $analytics])
            @elseif($widget['id'] === 'pipeline_funnel')
                @include('crm::admin.dashboard.widgets._pipeline_funnel', ['widget' => $widget, 'analytics' => $analytics])
            @elseif($widget['id'] === 'open_leads_by_stages')
                @include('crm::admin.dashboard.widgets._open_leads_by_stages', ['widget' => $widget, 'analytics' => $analytics])
            @elseif($widget['id'] === 'lead_channels')
                @include('crm::admin.dashboard.widgets._lead_channels', ['widget' => $widget, 'analytics' => $analytics])
            @elseif($widget['id'] === 'recent_activity')
                @include('crm::admin.dashboard.widgets._recent_activity', ['widget' => $widget, 'analytics' => $analytics])
            @endif
        @endforeach
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="crmDashboardCustomize">
        <div class="offcanvas-header border-bottom">
            <div>
                <h3 class="fw-bold mb-1">{{ __('crm::dashboard.customize.title') }}</h3>
                <div class="text-muted fs-7">{{ __('crm::dashboard.customize.hint') }}</div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="offcanvas">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <div id="crm-widget-toggles" class="flex-grow-1 overflow-auto px-5 py-4">
                @foreach($layout as $widget)
                    @php
                        $label = $widget['type'] === 'metric'
                            ? __('crm::dashboard.metrics.'.$widget['id'])
                            : match ($widget['id']) {
                                'top_customers' => __('crm::dashboard.widgets.top_customers'),
                                'sales_performance' => __('crm::dashboard.leaderboard.title'),
                                'pipeline_funnel' => __('crm::dashboard.charts.pipeline_funnel'),
                                'open_leads_by_stages' => __('crm::dashboard.charts.open_leads_by_stages'),
                                'lead_channels' => __('crm::dashboard.charts.lead_channels'),
                                'recent_activity' => __('crm::dashboard.activity.title'),
                                default => $widget['id'],
                            };
                    @endphp
                    <div class="crm-widget-toggle-item" data-widget-id="{{ $widget['id'] }}">
                        <div class="d-flex align-items-center gap-3">
                            <span class="crm-metric-icon bg-light-{{ $widget['color'] }} text-{{ $widget['color'] }}" style="width:2.25rem;height:2.25rem;font-size:1rem;">
                                <i class="bi bi-{{ $widget['icon'] }}"></i>
                            </span>
                            <span class="fw-semibold text-gray-800">{{ $label }}</span>
                        </div>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input crm-widget-visible" type="checkbox" value="1"
                                   data-widget-id="{{ $widget['id'] }}"
                                   @checked($widget['visible']) />
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-top p-5 d-flex gap-3">
                <button type="button" class="btn btn-light flex-grow-1" id="crm-layout-reset">
                    {{ __('crm::dashboard.customize.reset') }}
                </button>
                <button type="button" class="btn btn-primary flex-grow-1" id="crm-layout-save">
                    {{ __('crm::dashboard.customize.save') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            document.getElementById('crm-period')?.addEventListener('change', function () {
                document.querySelectorAll('.custom-range').forEach(el => {
                    el.classList.toggle('d-none', this.value !== 'custom');
                });
            });

            const layoutUrl = @json(route('admin.crm.dashboard.layout'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || @json(csrf_token());
            const savedMessage = @json(__('crm::dashboard.customize.saved'));
            let layoutState = @json($layoutStateForJs);

            function toast(message, type = 'success') {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                    return;
                }
                if (window.Swal) {
                    Swal.fire({ text: message, icon: type === 'success' ? 'success' : 'error', timer: 1800, showConfirmButton: false });
                    return;
                }
                alert(message);
            }

            function collectLayoutFromDom() {
                const orderIds = [...document.querySelectorAll('#crm-widget-grid .crm-widget')].map(el => el.dataset.widgetId);
                const visibility = {};
                document.querySelectorAll('.crm-widget-visible').forEach(input => {
                    visibility[input.dataset.widgetId] = !!input.checked;
                });

                const ordered = [];
                const seen = new Set();

                orderIds.forEach(id => {
                    ordered.push({ id, visible: visibility[id] !== false, order: ordered.length });
                    seen.add(id);
                });

                layoutState.forEach(item => {
                    if (seen.has(item.id)) return;
                    ordered.push({
                        id: item.id,
                        visible: visibility[item.id] ?? !!item.visible,
                        order: ordered.length,
                    });
                });

                return ordered;
            }

            async function saveLayout(payload, { reload = false, reset = false } = {}) {
                const body = reset ? { reset: true } : { widgets: payload };
                const response = await fetch(layoutUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    data = {};
                }

                if (!response.ok) {
                    const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                    toast(firstError || data.message || 'Unable to save layout', 'error');
                    return false;
                }

                layoutState = (data.layout || []).map((item, index) => ({
                    id: item.id,
                    visible: !!item.visible,
                    order: index,
                }));
                toast(data.message || savedMessage);

                if (reload) {
                    window.location.reload();
                }

                return true;
            }

            const grid = document.getElementById('crm-widget-grid');
            if (grid && window.Sortable) {
                Sortable.create(grid, {
                    animation: 150,
                    handle: '.crm-drag-handle',
                    draggable: '.crm-widget',
                    ghostClass: 'sortable-ghost',
                    onEnd() {
                        saveLayout(collectLayoutFromDom());
                    },
                });
            }

            document.getElementById('crm-layout-save')?.addEventListener('click', () => {
                saveLayout(collectLayoutFromDom(), { reload: true });
            });

            document.getElementById('crm-layout-reset')?.addEventListener('click', () => {
                saveLayout(layoutState, { reload: true, reset: true });
            });

            const chartColors = @json($analytics['chart_colors']);
            const funnelData = @json($analytics['pipeline_funnel']);
            const channelData = @json($analytics['lead_channels']);
            const tooltipTheme = {
                backgroundColor: '#1e1e2d',
                titleColor: '#fff',
                bodyColor: '#a1a5b7',
                padding: 12,
                cornerRadius: 10,
                displayColors: true,
                boxPadding: 4,
            };

            function markChartUnavailable(canvas) {
                const box = canvas?.closest('.crm-chart-box');
                if (!box) return;
                box.innerHTML = '<div class="text-muted fs-7 text-center d-flex align-items-center justify-content-center h-100">{{ __('No data available') }}</div>';
            }

            if (typeof Chart === 'undefined') {
                document.querySelectorAll('#crmPipelineChart, #crmChannelsChart').forEach(markChartUnavailable);
            } else {
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
                                borderRadius: 10,
                                maxBarThickness: 36,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: tooltipTheme,
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.04)' } },
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
                                borderWidth: 4,
                                borderColor: '#fff',
                                hoverOffset: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '72%',
                            plugins: {
                                legend: { display: false },
                                tooltip: tooltipTheme,
                            }
                        }
                    });
                }
            }
        </script>
    @endpush
</x-admin-layout>
