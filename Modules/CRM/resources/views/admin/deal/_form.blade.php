@php($dealData = $deal ?? null)

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('crm::deal.validation.fix_errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::deal.sections.basic_information') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::deal.sections.basic_information_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="title" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::deal.fields.title') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="title" type="text" class="form-control form-control-solid @error('title') is-invalid @enderror"
               name="title" value="{{ old('title', $dealData?->title) }}"
               placeholder="{{ __('crm::deal.placeholders.title') }}" maxlength="255" required autofocus/>
        @error('title')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="company_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::deal.fields.company') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="company_id" class="form-select form-select-solid @error('company_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::deal.fields.select_company') }}" name="company_id">
            <option value="">{{ __('crm::deal.fields.select_company') }}</option>
            @foreach(($companies ?? collect()) as $company)
                <option value="{{ $company->id }}" @selected((int) old('company_id', $dealData?->company_id ?? request('company_id')) === $company->id)>
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
        <label for="pipeline_stage_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::deal.fields.stage') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="pipeline_stage_id" class="form-select form-select-solid @error('pipeline_stage_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::deal.fields.select_stage') }}"
                name="pipeline_stage_id" required>
            @foreach(($stages ?? collect()) as $stage)
                <option value="{{ $stage->id }}" @selected((int) old('pipeline_stage_id', $dealData?->pipeline_stage_id ?? $defaultStageId ?? null) === $stage->id)>
                    {{ $stage->display_name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::deal.hints.stage') }}</div>
        @error('pipeline_stage_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="assigned_to" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::deal.fields.assignee') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="assigned_to" class="form-select form-select-solid @error('assigned_to') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::deal.fields.select_assignee') }}" name="assigned_to">
            <option value="">{{ __('crm::deal.fields.select_assignee') }}</option>
            @foreach(($assignees ?? collect()) as $assignee)
                <option value="{{ $assignee->id }}" @selected((int) old('assigned_to', $dealData?->assigned_to) === $assignee->id)>
                    {{ $assignee->name }} ({{ $assignee->email }})
                </option>
            @endforeach
        </select>
        @error('assigned_to')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::deal.sections.financial') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::deal.sections.financial_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::deal.fields.value') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-4">
                <input id="value" type="number" step="0.01" min="0"
                       class="form-control form-control-solid @error('value') is-invalid @enderror" name="value"
                       value="{{ old('value', $dealData?->value) }}"
                       placeholder="{{ __('crm::deal.placeholders.value') }}"/>
                @error('value')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-4">
                <input id="currency" type="text" maxlength="3"
                       class="form-control form-control-solid @error('currency') is-invalid @enderror" name="currency"
                       value="{{ old('currency', $dealData?->currency ?? 'USD') }}"/>
                @error('currency')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-4">
                <input id="probability" type="number" min="0" max="100"
                       class="form-control form-control-solid @error('probability') is-invalid @enderror" name="probability"
                       value="{{ old('probability', $dealData?->probability) }}"
                       placeholder="{{ __('crm::deal.fields.probability') }}"/>
                <div class="form-text">{{ __('crm::deal.hints.probability') }}</div>
                @error('probability')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="expected_close_date" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::deal.fields.expected_close_date') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="expected_close_date" type="date"
               class="form-control form-control-solid @error('expected_close_date') is-invalid @enderror"
               name="expected_close_date"
               value="{{ old('expected_close_date', $dealData?->expected_close_date?->format('Y-m-d')) }}"/>
        @error('expected_close_date')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::deal.sections.services') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::deal.sections.services_hint') }}</p>
</div>

<div id="deal-services-wrapper" class="mb-8">
    @foreach(($dealServices ?? [['service_id' => '', 'quantity' => 1, 'unit_price' => '']]) as $index => $line)
        <div class="row g-4 mb-4 deal-service-row">
            <div class="col-md-5">
                <select name="services[{{ $index }}][service_id]" class="form-select form-select-solid deal-service-select" data-control="select2">
                    <option value="">{{ __('crm::deal.placeholders.select_service') }}</option>
                    @foreach(($services ?? collect()) as $service)
                        <option value="{{ $service->id }}"
                                @selected((int) ($line['service_id'] ?? 0) === $service->id)>
                            {{ $service->getTranslation('title', app()->getLocale()) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" min="1" name="services[{{ $index }}][quantity]"
                       class="form-control form-control-solid" value="{{ $line['quantity'] ?? 1 }}"
                       placeholder="{{ __('crm::deal.fields.quantity') }}"/>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" min="0" name="services[{{ $index }}][unit_price]"
                       class="form-control form-control-solid deal-service-price" value="{{ $line['unit_price'] ?? '' }}"
                       placeholder="{{ __('crm::deal.fields.unit_price') }}"/>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-light-danger remove-deal-service">{{ __('crm::deal.actions.remove_service') }}</button>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="add-deal-service" class="btn btn-sm btn-light-primary mb-8">
    <i class="bi bi-plus-lg me-1"></i>{{ __('crm::deal.actions.add_service') }}
</button>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::deal.sections.additional_details') }}</h4>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="source" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::deal.fields.source') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="source" type="text" class="form-control form-control-solid @error('source') is-invalid @enderror"
               name="source" value="{{ old('source', $dealData?->source) }}"
               placeholder="{{ __('crm::deal.placeholders.source') }}" maxlength="100"/>
        @error('source')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="description" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::deal.fields.description') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="description" class="form-control form-control-solid @error('description') is-invalid @enderror"
                  name="description" rows="4"
                  placeholder="{{ __('crm::deal.placeholders.description') }}">{{ old('description', $dealData?->description) }}</textarea>
        @error('description')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8" id="lost-reason-row" style="{{ old('status', $dealData?->status ?? 'open') === 'lost' ? '' : 'display:none' }}">
    <div class="col-xl-3">
        <label for="lost_reason" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::deal.fields.lost_reason') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="lost_reason" class="form-control form-control-solid @error('lost_reason') is-invalid @enderror"
                  name="lost_reason" rows="3"
                  placeholder="{{ __('crm::deal.placeholders.lost_reason') }}">{{ old('lost_reason', $dealData?->lost_reason) }}</textarea>
        @error('lost_reason')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<input type="hidden" name="status" id="status" value="{{ old('status', $dealData?->status ?? 'open') }}"/>

@push('scripts')
<script>
    document.getElementById('pipeline_stage_id')?.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const text = selected.text.toLowerCase();
        const statusInput = document.getElementById('status');
        const lostRow = document.getElementById('lost-reason-row');

        if (text.includes('lost') || text.includes('مخسورة') || text.includes('kaybedildi')) {
            statusInput.value = 'lost';
            lostRow.style.display = '';
        } else if (text.includes('won') || text.includes('مكسوبة') || text.includes('kazanıldı')) {
            statusInput.value = 'won';
            lostRow.style.display = 'none';
        } else {
            statusInput.value = 'open';
            lostRow.style.display = 'none';
        }
    });

    const servicesWrapper = document.getElementById('deal-services-wrapper');
    const addServiceBtn = document.getElementById('add-deal-service');

    function bindServiceRow(row) {
        const select = row.querySelector('.deal-service-select');
        const priceInput = row.querySelector('.deal-service-price');

        row.querySelector('.remove-deal-service')?.addEventListener('click', function () {
            if (servicesWrapper.querySelectorAll('.deal-service-row').length > 1) {
                row.remove();
            } else {
                select.value = '';
                priceInput.value = '';
                row.querySelector('[name*="[quantity]"]').value = 1;
            }
        });
    }

    servicesWrapper?.querySelectorAll('.deal-service-row').forEach(bindServiceRow);

    addServiceBtn?.addEventListener('click', function () {
        const index = servicesWrapper.querySelectorAll('.deal-service-row').length;
        const template = servicesWrapper.querySelector('.deal-service-row').cloneNode(true);

        // Drop any Select2 markup copied from the cloned row so it can be re-initialised.
        template.querySelectorAll('.select2-container').forEach((el) => el.remove());

        template.querySelectorAll('[name]').forEach((input) => {
            input.name = input.name.replace(/services\[\d+\]/, `services[${index}]`);
            if (input.tagName === 'SELECT') {
                input.classList.remove('select2-hidden-accessible');
                input.removeAttribute('data-select2-id');
                input.removeAttribute('aria-hidden');
                input.removeAttribute('tabindex');
                input.querySelectorAll('option').forEach((opt) => opt.removeAttribute('data-select2-id'));
                input.value = '';
            } else if (input.name.includes('[quantity]')) {
                input.value = 1;
            } else {
                input.value = '';
            }
        });

        servicesWrapper.appendChild(template);

        if (window.jQuery && jQuery.fn.select2) {
            jQuery(template).find('.deal-service-select').select2();
        }

        bindServiceRow(template);
    });
</script>
@endpush
