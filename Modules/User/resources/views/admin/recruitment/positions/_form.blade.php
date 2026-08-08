@php($position = $jobPosition ?? null)

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="title" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('Title') }}</label>
    </div>
    <div class="col-xl-9">
        <input id="title" name="title" type="text" required maxlength="255"
               class="form-control form-control-solid @error('title') is-invalid @enderror"
               value="{{ old('title', $position?->title) }}"/>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="department" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('Department') }}</label>
    </div>
    <div class="col-xl-9">
        <input id="department" name="department" type="text" required maxlength="255"
               class="form-control form-control-solid @error('department') is-invalid @enderror"
               value="{{ old('department', $position?->department) }}"/>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="location" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('Location') }}</label>
    </div>
    <div class="col-xl-9">
        <input id="location" name="location" type="text" required maxlength="255"
               class="form-control form-control-solid @error('location') is-invalid @enderror"
               value="{{ old('location', $position?->location) }}"/>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="employment_type" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('Employment Type') }}</label>
    </div>
    <div class="col-xl-9">
        <select id="employment_type" name="employment_type" class="form-select form-select-solid @error('employment_type') is-invalid @enderror" required>
            @foreach(\Modules\User\Models\JobPosition::EMPLOYMENT_TYPES as $employmentType)
                <option value="{{ $employmentType }}" @selected(old('employment_type', $position?->employment_type ?? 'full_time') === $employmentType)>
                    {{ __((string) str($employmentType)->replace('_', ' ')->title()) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="posted_at" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('Posting Date') }}</label>
    </div>
    <div class="col-xl-9">
        <input id="posted_at" name="posted_at" type="date" required
               class="form-control form-control-solid @error('posted_at') is-invalid @enderror"
               value="{{ old('posted_at', $position?->posted_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"/>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('Status') }}</label>
    </div>
    <div class="col-xl-9">
        <select id="status" name="status" class="form-select form-select-solid" required>
            <option value="active" @selected(old('status', $position?->status ?? 'active') === 'active')>{{ __('Active') }}</option>
            <option value="closed" @selected(old('status', $position?->status) === 'closed')>{{ __('Closed') }}</option>
        </select>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="job-description-editor" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('Description') }}</label>
    </div>
    <div class="col-xl-9">
        <textarea id="job-description-editor" name="description" rows="8" required
                  class="form-control form-control-solid cms-tinymce-editor @error('description') is-invalid @enderror">{!! old('description', $position?->description) !!}</textarea>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="job-requirements-editor" class="fs-6 fw-bold mt-2 mb-3">{{ __('Requirements') }}</label>
    </div>
    <div class="col-xl-9">
        <textarea id="job-requirements-editor" name="requirements" rows="6"
                  class="form-control form-control-solid cms-tinymce-editor @error('requirements') is-invalid @enderror">{!! old('requirements', $position?->requirements) !!}</textarea>
    </div>
</div>
