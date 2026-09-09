@section('title', __('crm::company.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::company.menu.companies'), 'url' => route('admin.companies.index')],
            ['label' => __('crm::company.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::company.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.companies.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::company.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('crm::company.pages.edit_title')" :formUrl="route('admin.companies.update', $company->id)" :cancelUrl="route('admin.companies.index')" icon="building" color="warning">
        @method('PUT')
        @include('crm::admin.company._form', ['company' => $company])
    </x-admin.create-card>

    @include('crm::admin.company._quick_customer_modal')
</x-admin-layout>
