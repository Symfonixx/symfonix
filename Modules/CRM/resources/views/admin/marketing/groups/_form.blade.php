<div class="row mb-8">
    <div class="col-xl-3">
        <label for="title" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::marketing.fields.title') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" id="title" name="title"
               class="form-control form-control-solid @error('title') is-invalid @enderror"
               value="{{ old('title', $group->title) }}"
               maxlength="255"
               placeholder="{{ __('crm::marketing.placeholders.title') }}"
               required autofocus/>
        @error('title')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-0">
    <div class="col-xl-3">
        <label for="goal" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::marketing.fields.goal') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="goal" name="goal" rows="5"
                  class="form-control form-control-solid @error('goal') is-invalid @enderror"
                  placeholder="{{ __('crm::marketing.placeholders.goal') }}"
                  required>{{ old('goal', $group->goal) }}</textarea>
        <div class="form-text">{{ __('crm::marketing.groups.goal_hint') }}</div>
        @error('goal')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
