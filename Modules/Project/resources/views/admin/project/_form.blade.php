@php($projectData = $project ?? null)

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

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="title" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('project::project.fields.title') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="title" type="text" class="form-control form-control-solid @error('title') is-invalid @enderror"
               name="title" value="{{ old('title', $projectData?->title) }}"
               placeholder="{{ __('project::project.placeholders.title') }}" maxlength="255" required autofocus/>
        @error('title')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="description" class="fs-6 fw-bold mt-2 mb-3">{{ __('project::project.fields.description') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="description" class="form-control form-control-solid @error('description') is-invalid @enderror"
                  name="description" rows="4"
                  placeholder="{{ __('project::project.placeholders.description') }}">{{ old('description', $projectData?->description) }}</textarea>
        @error('description')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="company_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('project::project.fields.company') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="company_id" class="form-select form-select-solid @error('company_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('project::project.fields.select_company') }}"
                name="company_id" required>
            <option value="">{{ __('project::project.fields.select_company') }}</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" @selected((int) old('company_id', $projectData?->company_id) === $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
        @error('company_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="project_status_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('project::project.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="project_status_id" class="form-select form-select-solid @error('project_status_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('project::project.fields.select_status') }}"
                name="project_status_id" required>
            <option value="">{{ __('project::project.fields.select_status') }}</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}"
                        @selected((int) old('project_status_id', $projectData?->project_status_id ?? $defaultStatusId ?? null) === $status->id)>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
        @error('project_status_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="deal_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('project::project.fields.deal') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="deal_id" class="form-select form-select-solid @error('deal_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('project::project.fields.select_deal') }}"
                name="deal_id">
            <option value="">{{ __('project::project.fields.select_deal') }}</option>
            @foreach($deals as $deal)
                <option value="{{ $deal->id }}" data-company-id="{{ $deal->company_id }}"
                        @selected((int) old('deal_id', $projectData?->deal_id) === $deal->id)>
                    {{ $deal->title }}
                    @if($deal->company)
                        — {{ $deal->company->name }}
                    @endif
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('project::project.hints.deal') }}</div>
        @error('deal_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="service_ids" class="fs-6 fw-bold mt-2 mb-3">{{ __('project::project.fields.services') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <?php
            $selectedServiceIds = collect(old('service_ids', $projectData?->services?->pluck('id')->all() ?? []))
                ->filter(fn ($id) => filled($id))
                ->map(fn ($id) => (int) $id)
                ->all();
        ?>
        <select id="service_ids" class="form-select form-select-solid @error('service_ids') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('project::project.fields.select_services') }}"
                name="service_ids[]" multiple>
            @foreach(($services ?? collect()) as $service)
                <option value="{{ $service->id }}" @selected(in_array((int) $service->id, $selectedServiceIds, true))>
                    {{ $service->getTranslation('title', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('project::project.hints.services') }}</div>
        @error('service_ids')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('project::project.fields.budget') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-8">
                <input id="budget" type="number" step="0.01" min="0"
                       class="form-control form-control-solid @error('budget') is-invalid @enderror"
                       name="budget" value="{{ old('budget', $projectData?->budget) }}"
                       placeholder="{{ __('project::project.placeholders.budget') }}"/>
                @error('budget')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-4">
                @php($currencyOptions = app(\Modules\Finance\Services\CurrencyService::class)->supportedCurrencies())
                @php($budgetCurrency = old('currency', $projectData?->currency ?? app(\Modules\Finance\Services\CurrencyService::class)->defaultCurrency()))
                <select id="currency" name="currency"
                        class="form-select form-select-solid @error('currency') is-invalid @enderror">
                    @foreach($currencyOptions as $code)
                        <option value="{{ $code }}" @selected($budgetCurrency === $code)>{{ $code }}</option>
                    @endforeach
                </select>
                @error('currency')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="row g-6 mt-2">
            <div class="col-md-12">
                <label for="tax_rate_id" class="form-label">{{ __('tax::tax_rate.fields.name') }}</label>
                <x-tax::tax-rate-select
                    name="tax_rate_id"
                    id="tax_rate_id"
                    :selected="$projectData?->tax_rate_id"
                    :tax-rates="$taxRates ?? []"
                />
                <div class="form-text">{{ __('tax::tax_rate.hints.is_default') }}</div>
                @error('tax_rate_id')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('project::project.fields.start_date') }} / {{ __('project::project.fields.due_date') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-6">
                <label for="start_date" class="form-label">{{ __('project::project.fields.start_date') }}</label>
                <input id="start_date" type="date"
                       class="form-control form-control-solid @error('start_date') is-invalid @enderror"
                       name="start_date"
                       value="{{ old('start_date', $projectData?->start_date?->format('Y-m-d')) }}"/>
                @error('start_date')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="due_date" class="form-label">{{ __('project::project.fields.due_date') }}</label>
                <input id="due_date" type="date"
                       class="form-control form-control-solid @error('due_date') is-invalid @enderror"
                       name="due_date"
                       value="{{ old('due_date', $projectData?->due_date?->format('Y-m-d')) }}"/>
                @error('due_date')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="attachments" class="fs-6 fw-bold mt-2 mb-3">{{ __('project::project.fields.attachments') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="attachments" type="file" class="form-control form-control-solid @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
               name="attachments[]" multiple
               accept=".doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z,.odt,.ods,.rtf"/>
        <div class="form-text">{{ __('project::project.hints.attachments') }}</div>
        @if(!empty($projectData?->attachments))
            <div class="mt-4">
                @foreach($projectData->attachments as $attachment)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-paperclip"></i>
                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="text-hover-primary">
                            {{ $attachment['name'] ?? basename($attachment['path']) }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
        @error('attachments')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
        @error('attachments.*')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const companySelect = document.getElementById('company_id');
            const dealSelect = document.getElementById('deal_id');

            if (!companySelect || !dealSelect) {
                return;
            }

            const filterDealsByCompany = () => {
                const companyId = companySelect.value;
                Array.from(dealSelect.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }
                    const dealCompanyId = option.getAttribute('data-company-id');
                    option.hidden = companyId && dealCompanyId !== companyId;
                });
            };

            companySelect.addEventListener('change', filterDealsByCompany);
            filterDealsByCompany();
        });
    </script>
@endpush
