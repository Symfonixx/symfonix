@section('title', __('Job Application'))

@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('Job Application')" :breadcrumbItems="[
        ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
        ['label' => __('Job Applications'), 'url' => route('admin.job-applications.index')],
        ['label' => $jobApplication->candidate->full_name],
    ]"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ route('admin.job-applications.index') }}" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to List') }}
        </a>
        @if($jobApplication->isHired() && $jobApplication->employee_id)
            <a href="{{ route('admin.employees.show', $jobApplication->employee_id) }}" class="btn btn-sm btn-light-primary">
                <i class="bi bi-person-check me-1"></i>{{ __('View Employee') }}
            </a>
        @else
            <x-can perform="hr.employees.create">
                <form method="POST"
                      action="{{ route('admin.job-applications.hire', $jobApplication) }}"
                      class="d-inline"
                      data-confirm="{{ __('user::emails.hire.confirm', ['name' => $jobApplication->candidate->full_name, 'email' => $jobApplication->candidate->email]) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-person-plus me-1"></i>{{ __('Hire Employee') }}
                    </button>
                </form>
            </x-can>
        @endif
    </div>
@endsection

<x-admin-layout>
    <div class="row g-6">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Candidate Details') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="text-muted fs-7">{{ __('Full Name') }}</div>
                            <div class="fw-bold fs-5">{{ $jobApplication->candidate->full_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted fs-7">{{ __('Position') }}</div>
                            <div class="fw-bold fs-5">{{ $jobApplication->position->title }}</div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="text-muted fs-7">{{ __('Email') }}</div>
                            <a href="mailto:{{ $jobApplication->candidate->email }}">{{ $jobApplication->candidate->email }}</a>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted fs-7">{{ __('Phone') }}</div>
                            <a href="tel:{{ $jobApplication->candidate->phone }}">{{ $jobApplication->candidate->phone }}</a>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="text-muted fs-7">{{ __('Expected Salary') }}</div>
                            <div>{{ $jobApplication->candidate->expected_salary ? number_format((float) $jobApplication->candidate->expected_salary, 2).' USD' : __('N/A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted fs-7">{{ __('Submitted') }}</div>
                            <div>{{ $jobApplication->submitted_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <div class="text-muted fs-7 mb-2">{{ __('Why they want to work with us') }}</div>
                        <div class="bg-light rounded p-4">{!! nl2br(e($jobApplication->candidate->motivation)) !!}</div>
                    </div>
                    @if($jobApplication->candidate->cover_letter)
                        <div class="mb-6">
                            <div class="text-muted fs-7 mb-2">{{ __('Cover Letter') }}</div>
                            <div class="bg-light rounded p-4">{!! nl2br(e($jobApplication->candidate->cover_letter)) !!}</div>
                        </div>
                    @endif
                    <a href="{{ asset('storage/'.$jobApplication->candidate->resume_path) }}" target="_blank" class="btn btn-light-success">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ __('Download Resume') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <form method="POST" action="{{ route('admin.job-applications.update', $jobApplication) }}" class="card">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h3 class="card-title">{{ __('Hiring Decision') }}</h3>
                </div>
                <div class="card-body">
                    @error('mobile')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @error('email')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div class="mb-6">
                        <label for="status" class="form-label">{{ __('Hiring Status') }}</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $jobApplication->status) === $status)>{{ __(ucfirst($status)) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label for="profile_notes" class="form-label">{{ __('Profile Notes') }}</label>
                        <textarea id="profile_notes" name="profile_notes" rows="8" class="form-control @error('profile_notes') is-invalid @enderror">{{ old('profile_notes', $jobApplication->candidate->profile_notes) }}</textarea>
                        @error('profile_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
