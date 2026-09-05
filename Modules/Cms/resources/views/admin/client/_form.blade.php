@php($clientData = $client ?? null)
@php($logoUrl = $clientData?->logo_link ?? asset('images/default.jpg'))

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('Please fix the following errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row g-6">
    <div class="col-xl-7">
        <x-admin.settings-section
            icon="bi-buildings"
            title="Client Details"
            description="Company name and optional website URL."
        >
            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="name">
                        <i class="bi bi-building text-primary me-1"></i>{{ __('Company Name') }}
                        <span class="text-danger">*</span>
                    </label>
                </div>
                <div class="col-lg-8">
                    <input id="name" type="text" class="form-control form-control-solid" name="name"
                           value="{{ old('name', $clientData?->name) }}"
                           placeholder="{{ __('Company Name') }}" required autofocus/>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="url">
                        <i class="bi bi-link-45deg text-primary me-1"></i>{{ __('URL') }}
                    </label>
                </div>
                <div class="col-lg-8">
                    <input id="url" type="url" class="form-control form-control-solid" name="url"
                           value="{{ old('url', $clientData?->url) }}"
                           placeholder="https://example.com"/>
                    <div class="form-text">{{ __('Optional. Leave empty if the client has no public website.') }}</div>
                </div>
            </div>
        </x-admin.settings-section>
    </div>

    <div class="col-xl-5">
        <div class="seo-preview-panel">
            <div class="card mb-6 cms-aside-image-card">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-5">
                        <i class="bi bi-image text-primary me-2"></i>{{ __('Logo') }}
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="image-input image-input-outline w-100" data-kt-image-input="true"
                         style="background-image: url('{{ asset('images/default.jpg') }}')">
                        <div class="image-input-wrapper bgi-position-center"
                             style="background-image: url('{{ $logoUrl }}'); background-size: contain;"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                               data-kt-image-input-action="change" data-bs-toggle="tooltip"
                               title="{{ __('Change logo') }}">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="logo" accept=".png, .jpg, .jpeg, .webp, .gif"
                                   @if(! $clientData) required @endif/>
                            <input type="hidden" name="logo_remove"/>
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                              data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="{{ __('Cancel') }}">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                              data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="{{ __('Remove') }}">
                            <i class="bi bi-trash fs-7"></i>
                        </span>
                    </div>
                    <div class="form-text mt-3">
                        <span class="badge badge-light-primary fs-8">400 × 400 px</span>
                    </div>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-5">
                        <i class="bi bi-sort-numeric-down text-primary me-2"></i>{{ __('Display Order') }}
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <label class="form-label fw-semibold" for="rank">{{ __('Rank') }}</label>
                    <input id="rank" type="number"
                           class="form-control form-control-solid @error('rank') is-invalid @enderror"
                           name="rank" value="{{ old('rank', $clientData?->rank ?? $minRank) }}"
                           placeholder="{{ __('Rank') }}"
                           min="{{ $clientData ? min($clientData->rank, $minRank) : $minRank }}" required/>
                    <div class="form-text">
                        {{ __('Lower numbers appear first. Minimum rank is :min.', ['min' => $clientData ? min($clientData->rank, $minRank) : $minRank]) }}
                    </div>
                    @error('rank')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @include('cms::admin.shared._publish-aside', [
                'item' => $clientData,
                'showFeatured' => false,
            ])

            <div class="card mb-6">
                <div class="card-body">
                    <x-admin.auto-translate-checkbox :default="! $clientData" class="mb-0"/>
                </div>
            </div>
        </div>
    </div>
</div>
