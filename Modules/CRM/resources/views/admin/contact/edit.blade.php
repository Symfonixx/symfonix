@section('title', __('crm::contact.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::contact.pages.index_title'), 'url' => route('admin.contacts.index')],
            ['label' => __('crm::contact.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::contact.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.contacts.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::contact.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('crm::contact.pages.edit_title')" :formUrl="route('admin.contacts.update', $contact->id)" :cancelUrl="route('admin.contacts.index')" icon="person-badge" color="primary">
        @method('PUT')
        @include('crm::admin.contact._form', ['contact' => $contact])
    </x-admin.create-card>
</x-admin-layout>
