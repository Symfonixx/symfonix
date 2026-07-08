@php($useCaseData = $useCase ?? null)

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.image') }}
            @if(!$useCaseData)<span class="text-danger">*</span>@endif
        </div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="image-input image-input-outline" data-kt-image-input="true"
             style="background-image: url('{{ $useCaseData?->image_link ?? asset('images/default.jpg') }}')">
            <div class="image-input-wrapper w-250px h-250px bgi-position-center"
                 style="background-size: cover; background-image: url({{ $useCaseData?->image_link ?? asset('images/default.jpg') }})"></div>
            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                <i class="bi bi-pencil-fill fs-7"></i>
                <input type="file" name="image" accept=".png,.jpg,.jpeg,.webp" @if(!$useCaseData) required @endif/>
            </label>
        </div>
        <div class="form-text">Recommended 800×600px or larger</div>
        @error('image')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('project::use_case.fields.title') }} <span class="text-danger">*</span></label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" class="form-control form-control-solid @error('title') is-invalid @enderror"
               name="title" value="{{ old('title', $useCaseData?->title) }}" required/>
        @error('title')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.slug') }} <span class="text-danger">*</span></label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" id="gslug" class="form-control form-control-solid @error('slug') is-invalid @enderror"
               value="{{ old('slug', $useCaseData?->slug) }}"/>
        <input type="hidden" name="slug" id="slug" value="{{ old('slug', $useCaseData?->slug) }}">
        <div class="form-text">{{ __('project::use_case.hints.slug') }}</div>
        @error('slug')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('project::use_case.fields.client_name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" class="form-control form-control-solid" name="client_name"
               value="{{ old('client_name', $useCaseData?->client_name) }}"/>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.linked_project') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select name="project_id" class="form-select form-select-solid" data-control="select2"
                data-placeholder="{{ __('project::use_case.fields.select_project') }}">
            <option value="">{{ __('project::use_case.fields.select_project') }}</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected((int) old('project_id', $useCaseData?->project_id) === $project->id)>
                    {{ $project->title }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('project::use_case.hints.linked_project') }}</div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('project::use_case.fields.summary') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea class="form-control form-control-solid" name="summary" rows="3">{{ old('summary', $useCaseData?->summary) }}</textarea>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('project::use_case.fields.challenge') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea class="form-control form-control-solid" name="challenge" rows="4">{{ old('challenge', $useCaseData?->challenge) }}</textarea>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('project::use_case.fields.solution') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea class="form-control form-control-solid" name="solution" rows="4">{{ old('solution', $useCaseData?->solution) }}</textarea>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('project::use_case.fields.results') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea class="form-control form-control-solid" name="results" rows="4">{{ old('results', $useCaseData?->results) }}</textarea>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('project::use_case.fields.content') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea name="content" id="tinymce" class="form-control form-control-solid">{!! old('content', $useCaseData?->content) !!}</textarea>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.technologies') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input class="form-control" name="technologies" id="kt_tagify_tech"
               value="{{ old('technologies', $useCaseData ? implode(',', $useCaseData->technologies ?? []) : '') }}"/>
        <div class="form-text">{{ __('project::use_case.hints.technologies') }}</div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.category_tag') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" class="form-control form-control-solid" name="category_tag"
               value="{{ old('category_tag', $useCaseData?->category_tag) }}"
               placeholder="Web Development"/>
        <div class="form-text">{{ __('project::use_case.hints.category_tag') }}</div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.project_url') }} / {{ __('project::use_case.fields.completed_year') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-8">
                <input type="url" class="form-control form-control-solid" name="project_url"
                       value="{{ old('project_url', $useCaseData?->project_url) }}" placeholder="https://"/>
            </div>
            <div class="col-md-4">
                <input type="number" min="2000" max="2100" class="form-control form-control-solid" name="completed_year"
                       value="{{ old('completed_year', $useCaseData?->completed_year ?? date('Y')) }}"/>
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.sort_order') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="number" min="0" class="form-control form-control-solid" name="sort_order"
               value="{{ old('sort_order', $useCaseData?->sort_order ?? $nextSortOrder ?? 0) }}"/>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.published') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid me-10">
            <input class="form-check-input h-30px w-50px" type="checkbox" name="publish" id="publishSwitch"
                   @checked(old('publish', $useCaseData ? $useCaseData->status === 'Published' : true))/>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('project::use_case.fields.featured') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid me-10">
            <input class="form-check-input h-30px w-50px" type="checkbox" name="featured" id="featuredSwitch"
                   @checked(old('featured', $useCaseData?->featured ?? false))/>
        </div>
    </div>
</div>

<x-admin.auto-translate-checkbox :default="! $useCaseData" class="mb-0"/>
