@props(['filters' => [], 'showAssignee' => false, 'assignees' => collect(), 'showEmployee' => false, 'employees' => collect()])

<div class="card mb-5">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">{{ __('reporting::report.filters.period') }}</label>
                <select name="period" id="report-period" class="form-select form-select-solid" data-control="select2">
                    @foreach(__('reporting::report.filters.periods') as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['period'] ?? 'this_month') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 custom-range {{ ($filters['period'] ?? '') === 'custom' ? '' : 'd-none' }}">
                <label class="form-label fw-semibold">{{ __('reporting::report.filters.date_from') }}</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control form-control-solid"/>
            </div>
            <div class="col-md-2 custom-range {{ ($filters['period'] ?? '') === 'custom' ? '' : 'd-none' }}">
                <label class="form-label fw-semibold">{{ __('reporting::report.filters.date_to') }}</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control form-control-solid"/>
            </div>
            @if($showAssignee)
                <div class="col-md-3">
                    <label class="form-label fw-semibold">{{ __('reporting::report.filters.assignee') }}</label>
                    <select name="assigned_to" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('reporting::report.filters.all_reps') }}</option>
                        @foreach($assignees as $assignee)
                            <option value="{{ $assignee->id }}" @selected((int) ($filters['assigned_to'] ?? 0) === $assignee->id)>
                                {{ $assignee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if($showEmployee)
                <div class="col-md-3">
                    <label class="form-label fw-semibold">{{ __('reporting::report.filters.employee') }}</label>
                    <select name="employee_id" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('reporting::report.filters.all_employees') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected((int) ($filters['employee_id'] ?? 0) === $employee->id)>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>{{ __('reporting::report.filters.apply') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var periodInput = document.getElementById('report-period');
        if (!periodInput) {
            return;
        }

        periodInput.addEventListener('change', function () {
            var nodes = document.querySelectorAll('.custom-range');
            for (var i = 0; i < nodes.length; i++) {
                if (this.value !== 'custom') {
                    nodes[i].classList.add('d-none');
                } else {
                    nodes[i].classList.remove('d-none');
                }
            }
        });
    })();
</script>
@endpush
