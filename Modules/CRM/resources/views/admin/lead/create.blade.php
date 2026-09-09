@section('title', __('crm::lead.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::lead.menu.leads'), 'url' => route('admin.leads.index')],
            ['label' => __('crm::lead.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::lead.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.leads.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::lead.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('crm::lead.pages.create_title')" :formUrl="route('admin.leads.store')" :cancelUrl="route('admin.leads.index')" icon="person-plus" color="info">
        @include('crm::admin.lead._form')
    </x-admin.create-card>
</x-admin-layout>
