@props(['model' => null, 'dataTable' => true, 'class' => null, 'formUrl' => null, 'search' => null, 'title' => null, 'emptyMessage' => null, 'emptyIcon' => 'bi-inbox'])
@php
    $rowCount = null;
    if (isset($model)) {
        // Use total() for paginators so an empty page (e.g. out-of-range) still
        // shows pagination instead of the "no records" empty state.
        $rowCount = $model instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $model->total()
            : (is_countable($model) ? count($model) : 0);
    }
    $isEmpty = $rowCount !== null && $rowCount === 0;
@endphp
<div class="card">

    <div class="card-header border-0 pt-6">
        @if($search || $title)
            <div class="card-title">
                @if($search)
                    <div class="search-wrapper sx-table-search d-flex align-items-center position-relative my-1">
                        <i class="bi bi-search fs-4 position-absolute ms-5 text-primary"></i>
                        <input type="text" data-kt-data-table-filter="search"
                               class="form-control form-control-solid w-250px ps-12 border-0 bg-transparent"
                               placeholder="{{ __($search) }}"/>
                        <span class="search-clear" data-search-clear title="{{ __('Clear') }}">
                            <i class="bi bi-x-lg"></i>
                        </span>
                    </div>
                @endif
                @if($title)
                    <h2 class="mb-0">{{ $title }}</h2>
                @endif
            </div>
        @elseif(!$search && !$title && $formUrl)
            <div></div>
        @endif
        @if($formUrl)
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-comp-table-toolbar="base"></div>
                <div class="d-flex justify-content-end align-items-center d-none"
                     data-kt-comp-table-toolbar="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-comp-table-toolbar="selected_count"></span>{{ __('Selected') }}
                    </div>
                    <button type="button" class="btn btn-danger"
                            data-kt-comp-table-toolbar="delete_selected">
                        <i class="bi bi-trash me-1"></i>{{ __('Delete Selected') }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if($isEmpty)
        <div class="card-body">
            <div class="table-empty-state">
                <div class="empty-icon"><i class="bi {{ $emptyIcon }}"></i></div>
                <div class="fw-bold fs-5 text-gray-700 mb-1">{{ $emptyMessage ?? __('No records found') }}</div>
                <div class="text-muted fs-7">{{ __('Try adjusting your search or add a new record.') }}</div>
            </div>
        </div>
    @else
        @if($formUrl)
            {{-- Standalone bulk-delete form. It intentionally does NOT wrap the table:
                 nesting the table (and its per-row action forms) inside a form produces
                 invalid nested forms, which browsers ignore, causing row buttons to submit
                 this bulk form instead. Selected IDs are injected via JS on submit. --}}
            <form method="post" id="delete_all" action="{{ $formUrl }}" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endif

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed table-hover fs-6 gy-5 {{ $class ?? '' }}"
                       @if(isset($dataTable)) id="dataTable" @endif>
                    {{ $slot }}
                </table>
            </div>
        </div>

        @if(isset($model) && $model instanceof \Illuminate\Pagination\LengthAwarePaginator && $model->total() > 0)
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-3">
                <span class="text-muted fs-7">
                    {{ __('Showing :from–:to of :total', [
                        'from' => $model->firstItem() ?? 0,
                        'to' => $model->lastItem() ?? 0,
                        'total' => $model->total(),
                    ]) }}
                </span>
                @if($model->hasPages())
                    {!! $model->withQueryString()->links() !!}
                @endif
            </div>
        @endif
    @endif
</div>

@if($dataTable && !$isEmpty)
    @push('scripts')
        <script>
            $(document).ready(function () {
                const tableEl = document.getElementById("dataTable");
                const dataTable = $('#dataTable').DataTable({
                    responsive: true,
                    paging: false,
                    searching: true,
                    info: false,
                    order: [],
                    ordering: true,
                    language: {
                        search: '',
                        zeroRecords: @json(__('No matching records found')),
                    },
                });

                const initBulkActions = () => {
                    const checkboxes = tableEl.querySelectorAll('[type="checkbox"]');
                    const baseToolbar = document.querySelector('[data-kt-comp-table-toolbar="base"]');
                    const selectedToolbar = document.querySelector('[data-kt-comp-table-toolbar="selected"]');
                    const selectedCount = document.querySelector('[data-kt-comp-table-toolbar="selected_count"]');
                    const deleteBtn = document.querySelector('[data-kt-comp-table-toolbar="delete_selected"]');

                    if (!baseToolbar || !selectedToolbar || !selectedCount || !deleteBtn) return;

                    checkboxes.forEach((checkbox) => {
                        checkbox.addEventListener("click", () => {
                            setTimeout(() => {
                                const bodyCheckboxes = tableEl.querySelectorAll('tbody [type="checkbox"]');
                                let checkedCount = 0;
                                bodyCheckboxes.forEach((cb) => { if (cb.checked) checkedCount++; });

                                if (checkedCount > 0) {
                                    selectedCount.innerHTML = checkedCount;
                                    baseToolbar.classList.add("d-none");
                                    selectedToolbar.classList.remove("d-none");
                                } else {
                                    baseToolbar.classList.remove("d-none");
                                    selectedToolbar.classList.add("d-none");
                                }
                            }, 50);
                        });
                    });

                    deleteBtn.addEventListener("click", () => {
                        Swal.fire({
                            text: @json(__('This action cannot be undone.')),
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: @json(__('Yes Delete!')),
                            cancelButtonText: @json(__('No Cancel')),
                            customClass: {
                                confirmButton: "btn fw-bold btn-danger",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.getElementById('delete_all');
                                // Remove any IDs injected by a previous attempt.
                                form.querySelectorAll('input[name="ids[]"]').forEach((el) => el.remove());
                                // Collect the checked row checkboxes (they live in the table, not the form).
                                tableEl.querySelectorAll('tbody input[type="checkbox"]:checked').forEach((cb) => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'ids[]';
                                    input.value = cb.value;
                                    form.appendChild(input);
                                });
                                form.submit();
                            }
                        });
                    });
                };

                initBulkActions();

                // Form confirmations ([data-confirm], DELETE) are handled globally in admin-layout.

                const searchInput = document.querySelector('[data-kt-data-table-filter="search"]');
                const clearBtn = document.querySelector('[data-search-clear]');

                if (searchInput && dataTable) {
                    searchInput.addEventListener("keyup", (event) => {
                        dataTable.search(event.target.value).draw();
                        clearBtn?.classList.toggle('visible', event.target.value.length > 0);
                    });
                }

                if (clearBtn && searchInput) {
                    clearBtn.addEventListener('click', () => {
                        searchInput.value = '';
                        dataTable.search('').draw();
                        clearBtn.classList.remove('visible');
                        searchInput.focus();
                    });
                }
            });
        </script>
    @endpush
@endif
