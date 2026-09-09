@section('title', __('crm::calendar.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('CRM')],
            ['label' => __('crm::calendar.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::calendar.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

@section('css')
    @if(app()->getLocale() === 'ar')
        <link href="{{ asset('admin/plugins/custom/fullcalendar/fullcalendar.bundle.rtl.css') }}" rel="stylesheet" type="text/css"/>
    @else
        <link href="{{ asset('admin/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css"/>
    @endif
    <style>
        #crm_calendar {
            min-height: 680px;
        }
        .crm-cal-filter .form-check-input:checked {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        .crm-cal-legend-dot {
            width: .85rem;
            height: .85rem;
            border-radius: .25rem;
            display: inline-block;
        }
        .fc-event-completed {
            opacity: .55;
            text-decoration: line-through;
        }
        .fc-event {
            cursor: pointer;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('admin/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script>
        (function () {
            function initCrmCalendar() {
                if (typeof FullCalendar === 'undefined') {
                    console.error('FullCalendar failed to load');
                    return;
                }

                const eventsUrl = @json(route('admin.crm.calendar.events'));
                const locale = @json(app()->getLocale());
                const calendarEl = document.getElementById('crm_calendar');
                if (!calendarEl || calendarEl.dataset.fcInitialized === '1') {
                    return;
                }
                calendarEl.dataset.fcInitialized = '1';

                const modalEl = document.getElementById('crm_calendar_event_modal');
                const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

                const selectedTypes = () => Array.from(document.querySelectorAll('[data-crm-cal-type]:checked'))
                    .map((el) => el.value);

                const formatWhen = (event) => {
                    if (!event.start) {
                        return '—';
                    }

                    if (event.allDay) {
                        return event.start.toLocaleDateString(undefined, {
                            year: 'numeric', month: 'short', day: 'numeric'
                        });
                    }

                    return event.start.toLocaleString(undefined, {
                        year: 'numeric', month: 'short', day: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                };

                const showEvent = (event) => {
                    if (!modal || !modalEl) {
                        if (event.url) {
                            window.location.href = event.url;
                        }
                        return;
                    }

                    const props = event.extendedProps || {};
                    modalEl.querySelector('[data-crm-cal="modal-title"]').textContent = event.title || '';
                    modalEl.querySelector('[data-crm-cal="category"]').textContent = props.category_label || '—';
                    modalEl.querySelector('[data-crm-cal="type"]').textContent = props.type_label || props.type || '—';
                    modalEl.querySelector('[data-crm-cal="when"]').textContent = formatWhen(event);

                    const statusWrap = modalEl.querySelector('[data-crm-cal="status-wrap"]');
                    const statusEl = modalEl.querySelector('[data-crm-cal="status"]');
                    if (props.status) {
                        statusWrap.classList.remove('d-none');
                        statusEl.textContent = props.status;
                    } else {
                        statusWrap.classList.add('d-none');
                    }

                    const ownerWrap = modalEl.querySelector('[data-crm-cal="owner-wrap"]');
                    const ownerEl = modalEl.querySelector('[data-crm-cal="owner"]');
                    if (props.user) {
                        ownerWrap.classList.remove('d-none');
                        ownerEl.textContent = props.user;
                    } else {
                        ownerWrap.classList.add('d-none');
                    }

                    const descWrap = modalEl.querySelector('[data-crm-cal="description-wrap"]');
                    const descEl = modalEl.querySelector('[data-crm-cal="description"]');
                    if (props.description) {
                        descWrap.classList.remove('d-none');
                        descEl.textContent = props.description;
                    } else {
                        descWrap.classList.add('d-none');
                    }

                    const openBtn = modalEl.querySelector('[data-crm-cal="open"]');
                    if (event.url) {
                        openBtn.classList.remove('d-none');
                        openBtn.setAttribute('href', event.url);
                    } else {
                        openBtn.classList.add('d-none');
                        openBtn.removeAttribute('href');
                    }

                    modal.show();
                };

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    initialView: 'dayGridMonth',
                    locale: locale,
                    direction: document.documentElement.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr',
                    navLinks: true,
                    dayMaxEvents: true,
                    height: 'auto',
                    events: function (info, successCallback, failureCallback) {
                        const types = selectedTypes();
                        if (types.length === 0) {
                            successCallback([]);
                            return;
                        }

                        const params = new URLSearchParams({
                            start: info.startStr,
                            end: info.endStr,
                        });

                        types.forEach((type) => params.append('types[]', type));

                        fetch(eventsUrl + '?' + params.toString(), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                            .then((response) => {
                                if (!response.ok) {
                                    throw new Error('Failed to load calendar events');
                                }
                                return response.json();
                            })
                            .then((data) => successCallback(data))
                            .catch((error) => failureCallback(error));
                    },
                    eventClick: function (info) {
                        info.jsEvent.preventDefault();
                        showEvent(info.event);
                    },
                });

                calendar.render();

                document.querySelectorAll('[data-crm-cal-type]').forEach((checkbox) => {
                    checkbox.addEventListener('change', () => calendar.refetchEvents());
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCrmCalendar);
            } else {
                initCrmCalendar();
            }
        })();
    </script>
@endsection

<x-admin-layout>
    <div class="row g-5 g-xl-8">
        <div class="col-xl-3">
            <div class="card card-flush mb-5">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">{{ __('crm::calendar.filters.title') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-7">{{ __('crm::calendar.subtitle') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-4 crm-cal-filter">
                    @foreach([
                        'activities' => 'primary',
                        'leads' => 'info',
                        'deals' => 'primary',
                        'quotes' => 'warning',
                        'subscriptions' => 'success',
                        'projects' => 'info',
                    ] as $type => $color)
                        <div class="form-check form-check-custom form-check-solid mb-4">
                            <input class="form-check-input" type="checkbox" value="{{ $type }}" id="crm_cal_filter_{{ $type }}" checked data-crm-cal-type="{{ $type }}"/>
                            <label class="form-check-label text-gray-700 fw-semibold" for="crm_cal_filter_{{ $type }}">
                                {{ __('crm::calendar.categories.' . $type) }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card card-flush">
                <div class="card-header pt-5">
                    <h3 class="card-title fw-bold text-gray-800">{{ __('crm::calendar.legend.title') }}</h3>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex align-items-center mb-3">
                        <span class="crm-cal-legend-dot bg-primary me-3"></span>
                        <span class="text-gray-700 fs-7">{{ __('crm::calendar.categories.activities') }} / {{ __('crm::calendar.categories.deals') }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="crm-cal-legend-dot bg-info me-3"></span>
                        <span class="text-gray-700 fs-7">{{ __('crm::calendar.categories.leads') }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="crm-cal-legend-dot bg-warning me-3"></span>
                        <span class="text-gray-700 fs-7">{{ __('crm::calendar.categories.quotes') }} / {{ __('crm::timeline.activity_types.task') }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="crm-cal-legend-dot bg-success me-3"></span>
                        <span class="text-gray-700 fs-7">{{ __('crm::calendar.categories.subscriptions') }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="crm-cal-legend-dot bg-info me-3"></span>
                        <span class="text-gray-700 fs-7">{{ __('crm::calendar.event_types.project_start') }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="crm-cal-legend-dot bg-danger me-3"></span>
                        <span class="text-gray-700 fs-7">{{ __('crm::calendar.event_types.project_due') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            <div class="card card-flush">
                <div class="card-body">
                    <div id="crm_calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="crm_calendar_event_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" data-crm-cal="modal-title">{{ __('crm::calendar.modal.title') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg fs-2"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">{{ __('crm::calendar.modal.category') }}</div>
                        <div class="fw-semibold text-gray-800" data-crm-cal="category">—</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">{{ __('crm::calendar.modal.type') }}</div>
                        <div class="fw-semibold text-gray-800" data-crm-cal="type">—</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">{{ __('crm::calendar.modal.when') }}</div>
                        <div class="fw-semibold text-gray-800" data-crm-cal="when">—</div>
                    </div>
                    <div class="mb-4" data-crm-cal="status-wrap">
                        <div class="text-muted fs-7 mb-1">{{ __('crm::calendar.modal.status') }}</div>
                        <div class="fw-semibold text-gray-800" data-crm-cal="status">—</div>
                    </div>
                    <div class="mb-4" data-crm-cal="owner-wrap">
                        <div class="text-muted fs-7 mb-1">{{ __('crm::calendar.modal.owner') }}</div>
                        <div class="fw-semibold text-gray-800" data-crm-cal="owner">—</div>
                    </div>
                    <div class="mb-0" data-crm-cal="description-wrap">
                        <div class="text-muted fs-7 mb-1">{{ __('crm::calendar.modal.description') }}</div>
                        <div class="fw-semibold text-gray-800" data-crm-cal="description">—</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('crm::calendar.modal.close') }}</button>
                    <a href="#" class="btn btn-primary" data-crm-cal="open" target="_self">{{ __('crm::calendar.modal.open') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
