@section('title', __('crm::custom_field.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::custom_field.pages.index_title'), 'url' => route('admin.crm.custom-fields.index')],
            ['label' => __('crm::custom_field.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::custom_field.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('crm::custom_field.pages.create_title')"
        :formUrl="route('admin.crm.custom-fields.store')"
        :cancelUrl="route('admin.crm.custom-fields.index')"
        icon="ui-checks-grid"
        color="primary">
        @include('crm::admin.custom-fields._form', ['field' => new \Modules\CRM\Models\LeadCustomField()])
    </x-admin.create-card>
</x-admin-layout>
