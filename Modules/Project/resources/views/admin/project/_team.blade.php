<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-6">
    <div>
        <h4 class="fw-bold text-gray-900 mb-1">{{ __('project::project.sections.team') }}</h4>
        <p class="text-muted fs-7 mb-0">
            {{ __('project::project.hints.labor_cost', ['days' => $profitAndLoss['working_days_per_month']]) }}
        </p>
    </div>
    @can('update', $project)
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignEmployeeModal">
            <i class="bi bi-person-plus me-1"></i>{{ __('project::project.actions.assign_employee') }}
        </button>
    @endcan
</div>

<div class="table-responsive">
    <table class="table table-row-dashed align-middle gs-0 gy-4">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>{{ __('project::project.fields.employee') }}</th>
            <th>{{ __('project::project.fields.role') }}</th>
            <th>{{ __('project::project.fields.started_at') }}</th>
            <th>{{ __('project::project.fields.ended_at') }}</th>
            <th>{{ __('project::project.fields.days_worked') }}</th>
            <th>{{ __('project::project.fields.daily_rate') }}</th>
            <th>{{ __('project::project.fields.labor_cost') }}</th>
            <th>{{ __('project::project.fields.assignment_status') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @forelse($profitAndLoss['assignments'] as $row)
            @php $assignment = $row['assignment']; @endphp
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <span class="symbol symbol-35px symbol-circle">
                            <span class="symbol-label bg-light-primary text-primary fw-bold">
                                {{ mb_strtoupper(mb_substr($assignment->employee?->name ?? '?', 0, 1)) }}
                            </span>
                        </span>
                        <span class="fw-bold text-gray-800">{{ $assignment->employee?->name ?? __('N/A') }}</span>
                    </div>
                </td>
                <td>{{ $assignment->role ?: '—' }}</td>
                <td>{{ $assignment->started_at?->format('Y-m-d') }}</td>
                <td>{{ $assignment->ended_at?->format('Y-m-d') ?: '—' }}</td>
                <td>{{ $row['days_worked'] }}</td>
                <td>{{ number_format($row['daily_rate'], 2) }} {{ $profitAndLoss['currency'] }}</td>
                <td class="fw-bold">{{ number_format($row['labor_cost'], 2) }} {{ $profitAndLoss['currency'] }}</td>
                <td>
                    @if($row['is_active'])
                        <span class="badge badge-light-success">{{ __('project::project.assignment_status.active') }}</span>
                    @else
                        <span class="badge badge-light-secondary">{{ __('project::project.assignment_status.finished') }}</span>
                    @endif
                </td>
                <td class="text-end">
                    @can('update', $project)
                        <div class="d-inline-flex align-items-center gap-1">
                            @if($row['is_active'])
                                <button type="button"
                                        class="btn btn-sm btn-light-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#finishAssignmentModal{{ $assignment->id }}">
                                    {{ __('project::project.actions.finish_work') }}
                                </button>
                            @endif
                            <form method="POST" action="{{ route('admin.projects.employees.destroy', [$project, $assignment]) }}"
                                  data-confirm="{{ __('project::project.messages.confirm_remove_employee') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-light-danger" title="{{ __('Delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-muted text-center py-10">
                    <div class="mb-3">
                        <i class="bi bi-people fs-2x text-gray-400"></i>
                    </div>
                    {{ __('project::project.messages.no_employees') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
