@php
    $subscriptionData = $subscription ?? null;
@endphp

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('crm::subscription.validation.fix_errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::subscription.sections.basic_information') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::subscription.sections.basic_information_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="company_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::subscription.fields.company') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="company_id" class="form-select form-select-solid @error('company_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::subscription.fields.select_company') }}"
                name="company_id" required>
            <option value="">{{ __('crm::subscription.fields.select_company') }}</option>
            @foreach(($companies ?? collect()) as $company)
                <option value="{{ $company->id }}" @selected((int) old('company_id', $subscriptionData?->company_id ?? $selectedCompanyId ?? null) === $company->id)>
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
        <label for="service_ids" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::subscription.fields.services') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <?php
            $selectedServiceIds = collect(old(
                'service_ids',
                $subscriptionData?->relationLoaded('services')
                    ? $subscriptionData->services->pluck('id')->all()
                    : array_filter([$subscriptionData?->service_id])
            ))
                ->flatten()
                ->filter(fn ($id) => filled($id))
                ->map(fn ($id) => (int) $id)
                ->all();
        ?>
        <select id="service_ids" class="form-select form-select-solid @error('service_ids') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::subscription.fields.select_service') }}"
                name="service_ids[]" multiple>
            @foreach(($services ?? collect()) as $service)
                <option value="{{ $service->id }}" @selected(in_array((int) $service->id, $selectedServiceIds, true))>
                    {{ $service->getTranslation('title', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::subscription.hints.services') }}</div>
        @error('service_ids')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::subscription.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $subscriptionData?->name) }}"
               placeholder="{{ __('crm::subscription.placeholders.name') }}" maxlength="255" required autofocus/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::subscription.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="status" class="form-select form-select-solid @error('status') is-invalid @enderror" name="status" required>
            @foreach(\Modules\CRM\Models\Subscription::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $subscriptionData?->status ?? 'active') === $status)>
                    {{ __('crm::subscription.status.'.$status) }}
                </option>
            @endforeach
        </select>
        @error('status')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::subscription.sections.billing') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::subscription.sections.billing_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::subscription.fields.amount') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-4">
                <input id="amount" type="number" step="0.01" min="0"
                       class="form-control form-control-solid @error('amount') is-invalid @enderror" name="amount"
                       value="{{ old('amount', $subscriptionData?->amount ?? 0) }}" required/>
                @error('amount')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-4">
                <input id="currency" type="text" maxlength="3"
                       class="form-control form-control-solid @error('currency') is-invalid @enderror" name="currency"
                       value="{{ old('currency', $subscriptionData?->currency ?? 'USD') }}" required/>
                @error('currency')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-4">
                <select id="billing_cycle" class="form-select form-select-solid @error('billing_cycle') is-invalid @enderror"
                        name="billing_cycle" required>
                    @foreach(\Modules\CRM\Models\Subscription::BILLING_CYCLES as $cycle)
                        <option value="{{ $cycle }}" @selected(old('billing_cycle', $subscriptionData?->billing_cycle ?? 'monthly') === $cycle)>
                            {{ __('crm::subscription.billing_cycle.'.$cycle) }}
                        </option>
                    @endforeach
                </select>
                @error('billing_cycle')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::subscription.sections.dates') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::subscription.sections.dates_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::subscription.fields.starts_at') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-4">
                <label for="starts_at" class="form-label required">{{ __('crm::subscription.fields.starts_at') }}</label>
                <input id="starts_at" type="date"
                       class="form-control form-control-solid @error('starts_at') is-invalid @enderror"
                       name="starts_at"
                       value="{{ old('starts_at', $subscriptionData?->starts_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required/>
                @error('starts_at')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="ends_at" class="form-label">{{ __('crm::subscription.fields.ends_at') }}</label>
                <input id="ends_at" type="date"
                       class="form-control form-control-solid @error('ends_at') is-invalid @enderror"
                       name="ends_at"
                       value="{{ old('ends_at', $subscriptionData?->ends_at?->format('Y-m-d')) }}"/>
                <div class="form-text">{{ __('crm::subscription.hints.ends_at') }}</div>
                @error('ends_at')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="renewal_at" class="form-label">{{ __('crm::subscription.fields.renewal_at') }}</label>
                <input id="renewal_at" type="date"
                       class="form-control form-control-solid @error('renewal_at') is-invalid @enderror"
                       name="renewal_at"
                       value="{{ old('renewal_at', $subscriptionData?->renewal_at?->format('Y-m-d')) }}"/>
                <div class="form-text">{{ __('crm::subscription.hints.renewal_at') }}</div>
                @error('renewal_at')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::subscription.fields.auto_renew') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="auto_renew" value="1" id="auto_renew"
                   @checked(old('auto_renew', $subscriptionData?->auto_renew ?? true))/>
            <label class="form-check-label text-muted" for="auto_renew">
                {{ __('crm::subscription.hints.auto_renew') }}
            </label>
        </div>
    </div>
</div>

<div class="row mb-0">
    <div class="col-xl-3">
        <label for="notes" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::subscription.fields.notes') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="notes" class="form-control form-control-solid @error('notes') is-invalid @enderror"
                  name="notes" rows="4"
                  placeholder="{{ __('crm::subscription.placeholders.notes') }}">{{ old('notes', $subscriptionData?->notes) }}</textarea>
        @error('notes')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

@if(!empty($selectedCompanyId) && empty($subscriptionData))
    <input type="hidden" name="redirect_to_company" value="1"/>
@endif
