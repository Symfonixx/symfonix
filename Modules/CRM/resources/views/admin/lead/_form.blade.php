@php($leadData = $lead ?? null)

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('crm::lead.validation.fix_errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::lead.sections.contact_information') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::lead.sections.contact_information_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $leadData?->name) }}"
               placeholder="{{ __('crm::lead.placeholders.name') }}" maxlength="255" autofocus/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="email" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.email') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="email" type="email" class="form-control form-control-solid @error('email') is-invalid @enderror"
               name="email" value="{{ old('email', $leadData?->email) }}"
               placeholder="{{ __('crm::lead.placeholders.email') }}" maxlength="255"/>
        @error('email')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="phone" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.phone') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="phone" type="text" class="form-control form-control-solid @error('phone') is-invalid @enderror"
               name="phone" value="{{ old('phone', $leadData?->phone) }}"
               placeholder="{{ __('crm::lead.placeholders.phone') }}" maxlength="50"/>
        @error('phone')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::lead.sections.company') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::lead.sections.company_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="company_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.company') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="company_id" class="form-select form-select-solid @error('company_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::lead.fields.select_company') }}" name="company_id">
            <option value="">{{ __('crm::lead.fields.select_company') }}</option>
            @foreach(($companies ?? collect()) as $company)
                <option value="{{ $company->id }}" @selected((int) old('company_id', $leadData?->company_id) === $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::lead.hints.company') }}</div>
        @error('company_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8" id="company-name-row">
    <div class="col-xl-3">
        <label for="company_name" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.company_name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="company_name" type="text" class="form-control form-control-solid @error('company_name') is-invalid @enderror"
               name="company_name" value="{{ old('company_name', $leadData?->company_name) }}"
               placeholder="{{ __('crm::lead.placeholders.company_name') }}" maxlength="255"/>
        <div class="form-text">{{ __('crm::lead.hints.company_name') }}</div>
        @error('company_name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="assigned_to" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.assignee') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="assigned_to" class="form-select form-select-solid @error('assigned_to') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::lead.fields.select_assignee') }}" name="assigned_to">
            <option value="">{{ __('crm::lead.fields.select_assignee') }}</option>
            @foreach(($assignees ?? collect()) as $assignee)
                <option value="{{ $assignee->id }}" @selected((int) old('assigned_to', $leadData?->assigned_to) === $assignee->id)>
                    {{ $assignee->name }} ({{ $assignee->email }})
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::lead.hints.assignee') }}</div>
        @error('assigned_to')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::lead.sections.lead_details') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::lead.sections.lead_details_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="source" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::lead.fields.source') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="source" class="form-select form-select-solid @error('source') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::lead.fields.select_source') }}"
                name="source" required>
            @foreach(\Modules\CRM\Models\Lead::SOURCES as $source)
                <option value="{{ $source }}" @selected(old('source', $leadData?->source ?? \Modules\CRM\Models\Lead::SOURCE_MANUAL) === $source)>
                    {{ __('crm::lead.sources.' . $source) }}
                </option>
            @endforeach
        </select>
        @error('source')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="service_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.service') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="service_id" class="form-select form-select-solid @error('service_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::lead.fields.select_service') }}" name="service_id">
            <option value="">{{ __('crm::lead.fields.select_service') }}</option>
            @foreach(($services ?? collect()) as $service)
                <option value="{{ $service->id }}" @selected((int) old('service_id', $leadData?->service_id) === $service->id)>
                    {{ $service->getTranslation('title', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
        @error('service_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="service_interest" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.service_interest') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="service_interest" type="text" class="form-control form-control-solid @error('service_interest') is-invalid @enderror"
               name="service_interest" value="{{ old('service_interest', $leadData?->service_interest) }}"
               placeholder="{{ __('crm::lead.placeholders.service_interest') }}" maxlength="255"/>
        <div class="form-text">{{ __('crm::lead.hints.service_interest') }}</div>
        @error('service_interest')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="project_budget" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.project_budget') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="project_budget" type="text" class="form-control form-control-solid @error('project_budget') is-invalid @enderror"
               name="project_budget" value="{{ old('project_budget', $leadData?->project_budget) }}"
               placeholder="{{ __('crm::lead.placeholders.project_budget') }}" maxlength="255"/>
        @error('project_budget')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="problem_statement" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.problem_statement') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="problem_statement" class="form-control form-control-solid @error('problem_statement') is-invalid @enderror"
                  name="problem_statement" rows="4"
                  placeholder="{{ __('crm::lead.placeholders.problem_statement') }}">{{ old('problem_statement', $leadData?->problem_statement) }}</textarea>
        @error('problem_statement')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

@if($leadData)
    <div class="separator my-10"></div>

    <div class="mb-10">
        <h4 class="fw-bold mb-2">{{ __('crm::lead.sections.status') }}</h4>
    </div>

    <div class="row mb-8">
        <div class="col-xl-3">
            <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.blocked') }}</div>
        </div>
        <div class="col-xl-9 fv-row">
            <div class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="blocked" value="1" id="blocked"
                       @checked(old('blocked', $leadData?->blocked))/>
                <label class="form-check-label text-muted" for="blocked">
                    {{ __('crm::lead.hints.blocked') }}
                </label>
            </div>
            @error('blocked')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
@endif

@push('scripts')
<script>
    (function () {
        const companySelect = document.getElementById('company_id');
        const companyNameRow = document.getElementById('company-name-row');
        const companyNameInput = document.getElementById('company_name');

        function toggleCompanyName() {
            if (!companySelect || !companyNameRow) return;

            const hasCompany = companySelect.value !== '';
            companyNameRow.style.display = hasCompany ? 'none' : '';
            if (hasCompany && companyNameInput) {
                companyNameInput.value = '';
            }
        }

        companySelect?.addEventListener('change', toggleCompanyName);
        toggleCompanyName();
    })();
</script>
@endpush
