@php
    $marketingGroups = $marketingGroups ?? collect();
    $selectedGroupId = old('marketing_group_id', $selectedGroupId ?? null);
    $groupsPayload = $marketingGroups->map(fn ($group) => [
        'id' => $group->id,
        'title' => $group->title,
        'goal' => $group->goal,
        'missing' => $group->missingKeys(),
    ])->values();
    $missingLabels = [
        'title' => __('crm::marketing.missing.title'),
        'goal' => __('crm::marketing.missing.goal'),
        'sends' => __('crm::marketing.missing.sends'),
        'email' => __('crm::marketing.missing.email'),
        'whatsapp' => __('crm::marketing.missing.whatsapp'),
    ];
@endphp

<div class="mb-10" id="marketing-group-fields">
    <h4 class="fw-bold mb-2">{{ __('crm::marketing.fields.campaign') }}</h4>
    <p class="text-muted mb-6">{{ __('crm::marketing.groups.hint') }}</p>

    <div class="row mb-8">
        <div class="col-xl-3">
            <label for="marketing_group_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::marketing.fields.campaign') }}</label>
        </div>
        <div class="col-xl-9 fv-row">
            <select id="marketing_group_id" name="marketing_group_id"
                    class="form-select form-select-solid @error('marketing_group_id') is-invalid @enderror"
                    data-control="select2"
                    data-placeholder="{{ __('crm::marketing.placeholders.select_campaign') }}">
                <option value="">{{ __('crm::marketing.groups.create_new') }}</option>
                @foreach($marketingGroups as $group)
                    <option value="{{ $group->id }}" @selected((string) $selectedGroupId === (string) $group->id)>
                        {{ $group->title }}
                    </option>
                @endforeach
            </select>
            @error('marketing_group_id')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div id="marketing-group-new-fields">
        <div class="row mb-8">
            <div class="col-xl-3">
                <label for="group_title" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::marketing.fields.title') }}</label>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="text" id="group_title" name="group_title"
                       class="form-control form-control-solid @error('group_title') is-invalid @enderror"
                       value="{{ old('group_title') }}"
                       maxlength="255"
                       placeholder="{{ __('crm::marketing.placeholders.title') }}"/>
                @error('group_title')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="row mb-0">
            <div class="col-xl-3">
                <label for="group_goal" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::marketing.fields.goal') }}</label>
            </div>
            <div class="col-xl-9 fv-row">
                <textarea id="group_goal" name="group_goal" rows="4"
                          class="form-control form-control-solid @error('group_goal') is-invalid @enderror"
                          placeholder="{{ __('crm::marketing.placeholders.goal') }}">{{ old('group_goal') }}</textarea>
                <div class="form-text">{{ __('crm::marketing.groups.goal_hint') }}</div>
                @error('group_goal')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <div id="marketing-group-existing-summary" class="d-none">
        <div class="alert alert-light-primary mb-0">
            <div class="fw-bold mb-1" id="marketing-group-existing-title"></div>
            <div class="text-gray-700 mb-3" id="marketing-group-existing-goal" style="white-space: pre-wrap;"></div>
            <div id="marketing-group-existing-missing" class="d-none">
                <div class="fw-semibold text-warning mb-2">{{ __('crm::marketing.missing.heading') }}</div>
                <ul class="mb-0 ps-4" id="marketing-group-existing-missing-list"></ul>
            </div>
        </div>
    </div>
</div>

<div class="separator my-10"></div>

@once
    @push('scripts')
        <script>
            (function () {
                var groups = @json($groupsPayload);
                var missingLabels = @json($missingLabels);

                function byId(id) {
                    return document.getElementById(id);
                }

                function selectedGroup() {
                    var select = byId('marketing_group_id');
                    if (!select || !select.value) {
                        return null;
                    }

                    return groups.find(function (group) {
                        return String(group.id) === String(select.value);
                    }) || null;
                }

                function syncGroupFields() {
                    var group = selectedGroup();
                    var newFields = byId('marketing-group-new-fields');
                    var summary = byId('marketing-group-existing-summary');
                    var titleInput = byId('group_title');
                    var goalInput = byId('group_goal');

                    if (!newFields || !summary) {
                        return;
                    }

                    if (!group) {
                        newFields.classList.remove('d-none');
                        summary.classList.add('d-none');
                        if (titleInput) titleInput.disabled = false;
                        if (goalInput) goalInput.disabled = false;
                        return;
                    }

                    newFields.classList.add('d-none');
                    summary.classList.remove('d-none');
                    if (titleInput) {
                        titleInput.disabled = true;
                        titleInput.value = group.title || '';
                    }
                    if (goalInput) {
                        goalInput.disabled = true;
                        goalInput.value = group.goal || '';
                    }

                    byId('marketing-group-existing-title').textContent = group.title || '';
                    byId('marketing-group-existing-goal').textContent = group.goal || '';

                    var missing = Array.isArray(group.missing) ? group.missing : [];
                    var missingWrap = byId('marketing-group-existing-missing');
                    var missingList = byId('marketing-group-existing-missing-list');
                    missingList.innerHTML = '';

                    if (missing.length === 0) {
                        missingWrap.classList.add('d-none');
                        return;
                    }

                    missing.forEach(function (key) {
                        var item = document.createElement('li');
                        item.textContent = missingLabels[key] || key;
                        missingList.appendChild(item);
                    });
                    missingWrap.classList.remove('d-none');
                }

                window.SymfonixMarketingGroupFields = {
                    selectedGroup: selectedGroup,
                    title: function () {
                        var group = selectedGroup();
                        if (group) {
                            return group.title || '';
                        }
                        var input = byId('group_title');
                        return input ? String(input.value || '').trim() : '';
                    },
                    goal: function () {
                        var group = selectedGroup();
                        if (group) {
                            return group.goal || '';
                        }
                        var input = byId('group_goal');
                        return input ? String(input.value || '').trim() : '';
                    },
                    groupId: function () {
                        var select = byId('marketing_group_id');
                        return select && select.value ? Number(select.value) : null;
                    }
                };

                document.addEventListener('DOMContentLoaded', function () {
                    var select = byId('marketing_group_id');
                    if (!select) {
                        return;
                    }

                    select.addEventListener('change', syncGroupFields);
                    if (window.jQuery) {
                        window.jQuery(select).on('change.select2', syncGroupFields);
                    }
                    syncGroupFields();
                });
            })();
        </script>
    @endpush
@endonce
