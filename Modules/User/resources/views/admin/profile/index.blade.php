@section('title' , __('My Profile'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'My Profile'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('My Profile')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            const qrBtn = $('#show_qr');
            const codesBtn = $('#show_codes');
            const qrBox = $('#qr_box');
            const codesBox = $('#codes_box');

            qrBtn.on('click', function (e) {
                e.preventDefault();
                $.get('{{ route('two-factor.qr-code') }}', function (res) {
                    if (res.svg) {
                        qrBox.html(res.svg).removeClass('d-none');
                    }
                }).fail(function () {
                    toastr.error('{{ __('An Error Occurred!') }}');
                });
            });

            codesBtn.on('click', function (e) {
                e.preventDefault();
                $.get('{{ route('two-factor.recovery-codes') }}', function (res) {
                    if (res.recoveryCodes) {
                        let list = '<ul class="mb-0">';
                        res.recoveryCodes.forEach(function (code) {
                            list += '<li><code>' + code + '</code></li>';
                        });
                        list += '</ul>';
                        codesBox.html(list).removeClass('d-none');
                    }
                }).fail(function () {
                    toastr.error('{{ __('An Error Occurred!') }}');
                });
            });
        });
    </script>
@endsection

<x-admin-layout>
    <div class="row g-5 g-xl-8">
        <div class="col-12 col-sm-6">
            <x-admin.card :title="__('Profile Information')">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold">{{ __('Avatar') }}</label>
                        <div class="col-lg-8">
                            <div class="image-input image-input-outline" data-kt-image-input="true">
                                <div class="image-input-wrapper w-250px h-250px bgi-position-center"
                                     style="background-size: 90%; background-image: url('{{ $user->avatar }}')"></div>

                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                    title="{{__('Change avatar')}}">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg, .webp"/>
                                    <input type="hidden" name="avatar_remove"/>
                                </label>

                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                      data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                      title="{{__('Cancel avatar')}}">
                                    <i class="bi bi-x fs-2"></i>
                                </span>

                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                      data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                      title="{{__('Remove avatar')}}">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold">{{ __('Name') }}</label>
                        <div class="col-lg-8">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold">{{ __('Email') }}</label>
                        <div class="col-lg-8">
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold">{{ __('Mobile') }}</label>
                        <div class="col-lg-8">
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
                    </div>
                </form>
            </x-admin.card>
        </div>

        <div class="col-12 col-sm-6">
            <x-admin.card :title="__('Security')">
                <div class="mb-10">
                    <div class="fs-6 fw-bold mb-3">{{ __('Change Password') }}</div>
                    <form method="POST" action="{{ route('user-password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row mb-6">
                            <label class="col-lg-5 col-form-label required fw-semibold">{{ __('Current Password') }}</label>
                            <div class="col-lg-7">
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-5 col-form-label required fw-semibold">{{ __('New Password') }}</label>
                            <div class="col-lg-7">
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-5 col-form-label required fw-semibold">{{ __('Confirm Password') }}</label>
                            <div class="col-lg-7">
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>
                        </div>
                    </form>
                </div>

                <div>
                    <div class="fs-6 fw-bold mb-3">{{ __('Two-Factor Authentication') }}</div>
                    @if(! empty($user->two_factor_secret))
                        <div class="mb-5">
                            <span class="badge badge-light-success">{{ __('Enabled') }}</span>
                        </div>
                        <div class="d-flex gap-2 mb-5">
                            <a href="#" id="show_qr" class="btn btn-light-primary btn-sm">{{ __('Show QR Code') }}</a>
                            <a href="#" id="show_codes" class="btn btn-light-info btn-sm">{{ __('Show Recovery Codes') }}</a>
                        </div>
                        <div id="qr_box" class="border rounded p-3 d-none"></div>
                        <div id="codes_box" class="border rounded p-3 d-none mt-3"></div>
                        <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-5">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{ __('Disable Two-Factor Authentication') }}</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">{{ __('Enable Two-Factor Authentication') }}</button>
                        </form>
                    @endif
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>


