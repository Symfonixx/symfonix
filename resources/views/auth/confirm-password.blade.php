@section('title' , __('Confirm Password'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Confirm Password'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Confirm Password')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <x-admin.card :title="__('Confirm Password')">
                <p class="mb-6 text-muted">{{ __('For your security, please confirm your password to continue.') }}</p>
                <form method="POST" action="{{ route('password.confirm.store') }}">
                    @csrf
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold">{{ __('Password') }}</label>
                        <div class="col-lg-8">
                            <input type="password" name="password" class="form-control" required autofocus>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Confirm Password') }}</button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>










