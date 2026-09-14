@section('title', __('Job Applications'))

@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('Job Applications')" :breadcrumbItems="[
        ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
        ['label' => __('Job Applications')],
    ]"/>
@endsection

<x-admin-layout>
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Candidate name or email') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Position') }}</label>
                    <select name="position_id" class="form-select">
                        <option value="">{{ __('All Positions') }}</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" @selected((int) ($filters['position_id'] ?? 0) === $position->id)>{{ $position->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ __(ucfirst($status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-light-primary w-100">{{ __('Filter') }}</button></div>
            </form>
        </div>
    </div>

    <x-admin.table :model="$model" :search="__('Search applications')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{ __('Candidate') }}</th>
            <th>{{ __('Position') }}</th>
            <th>{{ __('Expected Salary') }}</th>
            <th>{{ __('Submitted') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @forelse($model as $application)
            <tr>
                <td>
                    <div class="d-flex flex-column">
                        <span>{{ $application->candidate->full_name }}</span>
                        <a href="mailto:{{ $application->candidate->email }}" class="text-muted fs-7">{{ $application->candidate->email }}</a>
                        <a href="tel:{{ $application->candidate->phone }}" class="text-muted fs-7">{{ $application->candidate->phone }}</a>
                    </div>
                </td>
                <td>{{ $application->position->title }}</td>
                <td>{{ $application->candidate->expected_salary ? number_format((float) $application->candidate->expected_salary, 2) : __('N/A') }}</td>
                <td>{{ $application->submitted_at->format('Y-m-d H:i') }}</td>
                <td><span class="badge badge-light-primary">{{ __(ucfirst($application->status)) }}</span></td>
                <td class="text-end">
                    <a href="{{ route('admin.job-applications.show', $application) }}" class="btn btn-sm btn-light-primary" title="{{ __('View') }}">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if($application->isHired() && $application->employee_id)
                        <a href="{{ route('admin.employees.show', $application->employee_id) }}" class="btn btn-sm btn-light-success" title="{{ __('View Employee') }}">
                            <i class="bi bi-person-check"></i>
                        </a>
                    @else
                        <x-can perform="hr.employees.create">
                            <form method="POST"
                                  action="{{ route('admin.job-applications.hire', $application) }}"
                                  class="d-inline"
                                  data-confirm="{{ __('user::emails.hire.confirm', ['name' => $application->candidate->full_name, 'email' => $application->candidate->email]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="{{ __('Hire Employee') }}">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            </form>
                        </x-can>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-10">{{ __('No job applications found.') }}</td></tr>
        @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>
