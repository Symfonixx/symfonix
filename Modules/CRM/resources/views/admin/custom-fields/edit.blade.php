@section('title', __('crm::custom_field.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::custom_field.pages.index_title'), 'url' => route('admin.crm.custom-fields.index')],
            ['label' => __('crm::custom_field.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::custom_field.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('crm::custom_field.pages.edit_title')"
        :formUrl="route('admin.crm.custom-fields.update', $field)"
        :cancelUrl="route('admin.crm.custom-fields.index')"
        icon="ui-checks-grid"
        color="primary">
        @method('PUT')
        @include('crm::admin.custom-fields._form', ['field' => $field])
    </x-admin.create-card>
</x-admin-layout>
