@section('title', __('Job Positions'))

@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('Job Positions')" :breadcrumbItems="[
        ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
        ['label' => __('Job Positions')],
    ]"/>
    <x-can perform="hr.job_positions.create">
        <a href="{{ route('admin.job-positions.create') }}" class="btn btn-sm fw-bold btn-primary">
            {{ __('Add Job Position') }} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </x-can>
@endsection

<x-admin-layout>
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Title or department') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Department') }}</label>
                    <select name="department" class="form-select">
                        <option value="">{{ __('All Departments') }}</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" @selected(($filters['department'] ?? '') === $department)>{{ $department }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('Active') }}</option>
                        <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>{{ __('Closed') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-light-primary w-100">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <x-admin.table :model="$model" :search="__('Search job positions')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{ __('Position') }}</th>
            <th>{{ __('Department') }}</th>
            <th>{{ __('Location') }}</th>
            <th>{{ __('Employment Type') }}</th>
            <th>{{ __('Posting Date') }}</th>
            <th>{{ __('Applications') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @forelse($model as $position)
            <tr>
                <td>{{ $position->title }}</td>
                <td>{{ $position->department }}</td>
                <td>{{ $position->location ?: __('N/A') }}</td>
                <td>{{ __((string) str($position->employment_type)->replace('_', ' ')->title()) }}</td>
                <td>{{ $position->posted_at->format('Y-m-d') }}</td>
                <td>{{ $position->applications_count }}</td>
                <td><span class="badge badge-light-{{ $position->status === 'active' ? 'success' : 'secondary' }}">{{ __(ucfirst($position->status)) }}</span></td>
                <td class="text-end">
                    <a href="{{ route('admin.job-positions.edit', $position) }}" class="btn btn-sm btn-light-primary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.job-positions.destroy', $position) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete it') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-light-danger" type="submit"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-10">{{ __('No job positions found.') }}</td></tr>
        @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>
