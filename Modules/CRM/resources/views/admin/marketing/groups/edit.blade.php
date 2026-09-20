@section('title', __('crm::marketing.pages.group_edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index')],
            ['label' => $group->title, 'url' => route('admin.crm.marketing.groups.show', $group)],
            ['label' => __('crm::marketing.pages.group_edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::marketing.pages.group_edit_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('crm::marketing.pages.group_edit_title')"
        :description="'crm::marketing.groups.form_hint'"
        :formUrl="route('admin.crm.marketing.groups.update', $group)"
        :cancelUrl="route('admin.crm.marketing.groups.show', $group)"
        icon="megaphone"
        color="info">
        @method('PUT')
        @include('crm::admin.marketing.groups._form', ['group' => $group])
    </x-admin.create-card>
</x-admin-layout>
