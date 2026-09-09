@section('title', __('crm::contact.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::contact.pages.index_title'), 'url' => route('admin.contacts.index')],
            ['label' => __('crm::contact.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::contact.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.contacts.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::contact.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('crm::contact.pages.create_title')" :formUrl="route('admin.contacts.store')" :cancelUrl="route('admin.contacts.index')" icon="person-badge" color="primary">
        @include('crm::admin.contact._form')
    </x-admin.create-card>
</x-admin-layout>
