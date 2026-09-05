@section('title', __('Edit Client'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Our Clients', 'url' => route('admin.clients.index')],
            ['label' => 'Edit Client'],
        ];
    @endphp
    <x-admin.breadcrumb
        pageTitle="Edit Client"
        :breadcrumbItems="$breadcrumbItems"
        pageDescription="Update the logo, company name, URL, and publish status."
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.clients.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Edit Client"
        :formUrl="route('admin.clients.update', $client->id)"
        description="Update client details and publishing options."
        :cancelUrl="route('admin.clients.index')"
        id="client-form"
    >
        @method('PUT')
        @include('cms::admin.client._form', ['client' => $client])
    </x-admin.create-card>
</x-admin-layout>
