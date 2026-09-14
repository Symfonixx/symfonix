@php
    $convertEmployee = $convertEmployee ?? null;
    $convertEmployeeId = $convertEmployee?->id;
    $convertAction = $convertEmployeeId
        ? route('admin.employees.convert-to-admin', $convertEmployeeId)
        : '#';
    $selectAllId = 'convert_admin_select_all'.($convertEmployeeId ?? '_shared');
@endphp
<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">{{ __('Convert to Admin') }}</h3>
            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                <i class="ki-duotone ki-cross fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        <form method="POST" action="{{ $convertAction }}" class="js-convert-admin-form">
            @csrf
            <input type="hidden" name="form_context" value="convert_to_admin">
            <div class="modal-body">
                <p class="text-muted mb-6">
                    {{ __('Set a password and permissions so this employee can log in to the admin panel.') }}
                    <span class="fw-bold text-gray-800 js-convert-employee-name">
                        {{ $convertEmployee?->name }}
                    </span>
                </p>
                <p class="text-muted mb-6">
                    {{ __('Login credentials will be emailed to this employee.') }}
                </p>
                <div class="mb-8">
                    <label for="{{ $selectAllId }}_password" class="required form-label">{{ __('Password') }}</label>
                    <input type="password"
                           id="{{ $selectAllId }}_password"
                           class="form-control form-control-solid @error('password') is-invalid @enderror"
                           name="password"
                           required
                           minlength="6"
                           autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="fv-row">
                    <label class="fs-5 fw-bolder form-label mb-2">{{ __('Permissions') }}</label>
                    @include('user::admin.role._permissions_matrix', [
                        'groups' => $groups,
                        'assigned' => old('permissions', []),
                        'selectAllId' => $selectAllId,
                    ])
                    @error('permissions')
                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                    @enderror
                    @error('permissions.*')
                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                    @enderror
                    @error('email')
                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                    @enderror
                    @error('mobile')
                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Discard') }}</button>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create Admin Login') }} <i class="bi bi-shield-lock ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>
