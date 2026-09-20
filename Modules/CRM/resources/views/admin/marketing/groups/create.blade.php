@section('title', __('crm::marketing.pages.group_create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index')],
            ['label' => __('crm::marketing.pages.group_create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::marketing.pages.group_create_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('crm::marketing.pages.group_create_title')"
        :description="'crm::marketing.groups.form_hint'"
        :formUrl="route('admin.crm.marketing.groups.store')"
        :cancelUrl="route('admin.crm.marketing.index')"
        icon="megaphone"
        color="info">
        @include('crm::admin.marketing.groups._form', ['group' => new \Modules\CRM\Models\MarketingGroup()])
    </x-admin.create-card>
</x-admin-layout>
