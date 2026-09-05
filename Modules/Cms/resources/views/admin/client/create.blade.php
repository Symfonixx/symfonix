@section('title', __('Add New Client'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Our Clients', 'url' => route('admin.clients.index')],
            ['label' => 'Add New Client'],
        ];
    @endphp
    <x-admin.breadcrumb
        pageTitle="Add New Client"
        :breadcrumbItems="$breadcrumbItems"
        pageDescription="Add a client logo and company name for the website."
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.clients.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Add New Client"
        :formUrl="route('admin.clients.store')"
        description="Upload the logo, set the company name, optional URL, and publish status."
        :cancelUrl="route('admin.clients.index')"
        id="client-form"
    >
        @include('cms::admin.client._form')
    </x-admin.create-card>
</x-admin-layout>
