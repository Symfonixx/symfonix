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
        <input id="phone" type="tel" inputmode="tel" pattern="[0-9+\-\s()]+"
               class="form-control form-control-solid @error('phone') is-invalid @enderror"
               name="phone" value="{{ old('phone', $leadData?->phone) }}"
               placeholder="{{ __('crm::lead.placeholders.phone') }}" maxlength="50"/>
        @error('phone')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="job_title" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.job_title') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="job_title" type="text" class="form-control form-control-solid @error('job_title') is-invalid @enderror"
               name="job_title" value="{{ old('job_title', $leadData?->job_title) }}"
               placeholder="{{ __('crm::lead.placeholders.job_title') }}" maxlength="255"/>
        @error('job_title')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.city') }} / {{ __('crm::lead.fields.country') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-6">
                <input id="city" type="text" class="form-control form-control-solid @error('city') is-invalid @enderror"
                       name="city" value="{{ old('city', $leadData?->city) }}"
                       placeholder="{{ __('crm::lead.placeholders.city') }}" maxlength="100"/>
                @error('city')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-6">
                <input id="country" type="text" class="form-control form-control-solid @error('country') is-invalid @enderror"
                       name="country" value="{{ old('country', $leadData?->country) }}"
                       placeholder="{{ __('crm::lead.placeholders.country') }}" maxlength="100"/>
                @error('country')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="website" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.website') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="website" type="url" class="form-control form-control-solid @error('website') is-invalid @enderror"
               name="website" value="{{ old('website', $leadData?->website) }}"
               placeholder="{{ __('crm::lead.placeholders.website') }}" maxlength="255"/>
        @error('website')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="industry" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.industry') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="industry" type="text" class="form-control form-control-solid @error('industry') is-invalid @enderror"
               name="industry" value="{{ old('industry', $leadData?->industry) }}"
               placeholder="{{ __('crm::lead.placeholders.industry') }}" maxlength="150"/>
        @error('industry')
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
                <option value="{{ $company->id }}" @selected((int) old('company_id', $leadData?->company_id ?? request('company_id')) === $company->id)>
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
        <label for="service_ids" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.services') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <?php
            $selectedServiceIds = collect(old('service_ids', $leadData?->serviceIds() ?? []))
                ->filter(fn ($id) => filled($id))
                ->map(fn ($id) => (int) $id)
                ->all();
        ?>
        <select id="service_ids" class="form-select form-select-solid @error('service_ids') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::lead.fields.select_services') }}"
                name="service_ids[]" multiple>
            @foreach(($services ?? collect()) as $service)
                <option value="{{ $service->id }}" @selected(in_array((int) $service->id, $selectedServiceIds, true))>
                    {{ $service->getTranslation('title', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::lead.hints.services') }}</div>
        @error('service_ids')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.tags') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        @php($selectedTagIds = collect(old('tag_ids', $leadData?->tagIds() ?? []))->filter(fn ($id) => filled($id))->map(fn ($id) => (int) $id)->all())
        @php($availableTags = $tags ?? collect())

        @if($availableTags->isEmpty())
            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                <i class="bi bi-tags fs-2 text-warning me-3"></i>
                <div class="d-flex flex-stack flex-grow-1 flex-wrap gap-2">
                    <div class="fw-semibold">
                        <div class="fs-6 text-gray-700">{{ __('crm::lead.hints.no_tags') }}</div>
                    </div>
                    @can('crm.lead_tags.create')
                        <a href="{{ route('admin.crm.lead-tags.create') }}" class="btn btn-sm btn-warning">
                            {{ __('crm::lead.actions.manage_tags') }}
                        </a>
                    @endcan
                </div>
            </div>
        @else
            <div class="d-flex flex-wrap gap-2" id="lead-tag-picker">
                @foreach($availableTags as $tag)
                    @php($isSelected = in_array((int) $tag->id, $selectedTagIds, true))
                    <input type="checkbox"
                           class="btn-check lead-tag-input"
                           name="tag_ids[]"
                           id="lead_tag_{{ $tag->id }}"
                           value="{{ $tag->id }}"
                           autocomplete="off"
                           @checked($isSelected)>
                    <label for="lead_tag_{{ $tag->id }}"
                           class="btn btn-sm lead-tag-chip border {{ $isSelected ? 'btn-' . $tag->color : 'btn-light-' . $tag->color }} @error('tag_ids') border-danger @enderror"
                           data-color="{{ $tag->color }}">
                        <i class="bi bi-tag-fill me-1"></i>{{ $tag->display_name }}
                    </label>
                @endforeach
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                <div class="form-text mb-0">{{ __('crm::lead.hints.tags') }}</div>
                @can('crm.lead_tags.view')
                    <a href="{{ route('admin.crm.lead-tags.index') }}" class="fs-7 text-primary text-hover-primary">
                        <i class="bi bi-gear me-1"></i>{{ __('crm::lead.actions.manage_tags') }}
                    </a>
                @endcan
            </div>
        @endif

        @error('tag_ids')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
        @error('tag_ids.*')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

@php($availableCustomFields = $customFields ?? collect())
@if($availableCustomFields->isNotEmpty())
    <div class="separator my-10"></div>

    <div class="mb-10">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="fw-bold mb-2">{{ __('crm::lead.sections.custom_fields') }}</h4>
                <p class="text-muted mb-0">{{ __('crm::lead.sections.custom_fields_hint') }}</p>
            </div>
            @can('crm.custom_fields.view')
                <a href="{{ route('admin.crm.custom-fields.index') }}" class="fs-7 text-primary text-hover-primary">
                    <i class="bi bi-gear me-1"></i>{{ __('crm::lead.actions.manage_custom_fields') }}
                </a>
            @endcan
        </div>
    </div>

    @foreach($availableCustomFields as $customField)
        @php($fieldName = 'custom_fields[' . $customField->key . ']')
        @php($fieldId = 'custom_field_' . $customField->key)
        @php($oldValue = old('custom_fields.' . $customField->key, $leadData?->customFieldValue($customField->key)))

        <div class="row mb-8">
            <div class="col-xl-3">
                <label for="{{ $fieldId }}" class="fs-6 fw-bold mt-2 mb-3 {{ $customField->is_required ? 'required' : '' }}">
                    {{ $customField->display_label }}
                </label>
            </div>
            <div class="col-xl-9 fv-row">
                @if($customField->type === \Modules\CRM\Models\LeadCustomField::TYPE_TEXTAREA)
                    <textarea id="{{ $fieldId }}"
                              name="{{ $fieldName }}"
                              rows="3"
                              class="form-control form-control-solid @error('custom_fields.' . $customField->key) is-invalid @enderror"
                              {{ $customField->is_required ? 'required' : '' }}>{{ $oldValue }}</textarea>
                @elseif($customField->type === \Modules\CRM\Models\LeadCustomField::TYPE_NUMBER)
                    <input id="{{ $fieldId }}"
                           type="number"
                           name="{{ $fieldName }}"
                           value="{{ $oldValue }}"
                           class="form-control form-control-solid @error('custom_fields.' . $customField->key) is-invalid @enderror"
                           {{ $customField->is_required ? 'required' : '' }}/>
                @elseif($customField->type === \Modules\CRM\Models\LeadCustomField::TYPE_DATE)
                    <input id="{{ $fieldId }}"
                           type="date"
                           name="{{ $fieldName }}"
                           value="{{ $oldValue }}"
                           class="form-control form-control-solid @error('custom_fields.' . $customField->key) is-invalid @enderror"
                           {{ $customField->is_required ? 'required' : '' }}/>
                @elseif($customField->type === \Modules\CRM\Models\LeadCustomField::TYPE_SELECT)
                    <select id="{{ $fieldId }}"
                            name="{{ $fieldName }}"
                            class="form-select form-select-solid @error('custom_fields.' . $customField->key) is-invalid @enderror"
                            data-control="select2"
                            {{ $customField->is_required ? 'required' : '' }}>
                        <option value="">{{ __('crm::lead.fields.select_option') }}</option>
                        @foreach(($customField->options ?? []) as $option)
                            <option value="{{ $option }}" @selected((string) $oldValue === (string) $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                @elseif($customField->type === \Modules\CRM\Models\LeadCustomField::TYPE_CHECKBOX)
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input @error('custom_fields.' . $customField->key) is-invalid @enderror"
                               type="checkbox"
                               name="{{ $fieldName }}"
                               value="1"
                               id="{{ $fieldId }}"
                               @checked(old('custom_fields.' . $customField->key, $leadData?->customFieldValue($customField->key)))>
                        <label class="form-check-label" for="{{ $fieldId }}">{{ __('Yes') }}</label>
                    </div>
                @else
                    <input id="{{ $fieldId }}"
                           type="text"
                           name="{{ $fieldName }}"
                           value="{{ $oldValue }}"
                           class="form-control form-control-solid @error('custom_fields.' . $customField->key) is-invalid @enderror"
                           maxlength="255"
                           {{ $customField->is_required ? 'required' : '' }}/>
                @endif

                @error('custom_fields.' . $customField->key)
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    @endforeach
@endif

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

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="status" class="form-select form-select-solid @error('status') is-invalid @enderror"
                data-control="select2" name="status">
            @foreach(\Modules\CRM\Models\Lead::STATUSES as $leadStatus)
                <option value="{{ $leadStatus }}" @selected(old('status', $leadData?->status ?? \Modules\CRM\Models\Lead::STATUS_NEW) === $leadStatus)>
                    {{ __('crm::lead.status.' . $leadStatus) }}
                </option>
            @endforeach
        </select>
        @error('status')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="attachments" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead.fields.attachments') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="attachments" type="file" class="form-control form-control-solid @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
               name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.webp"/>
        @if(!empty($leadData?->attachments))
            <div class="mt-4">
                @foreach($leadData->attachments as $attachment)
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

        document.querySelectorAll('.lead-tag-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const label = document.querySelector('label[for="' + input.id + '"]');
                if (!label) return;

                const color = label.getAttribute('data-color') || 'primary';
                label.classList.remove('btn-' + color, 'btn-light-' + color);
                label.classList.add(input.checked ? 'btn-' + color : 'btn-light-' + color);
            });
        });
    })();
</script>
@endpush
