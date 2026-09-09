@section('title', __('crm::lead_tag.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::lead_tag.pages.index_title'), 'url' => route('admin.crm.lead-tags.index')],
            ['label' => __('crm::lead_tag.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::lead_tag.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('crm::lead_tag.pages.create_title')"
        :formUrl="route('admin.crm.lead-tags.store')"
        :cancelUrl="route('admin.crm.lead-tags.index')"
        icon="tags"
        color="warning">
        @include('crm::admin.lead-tags._form', ['tag' => new \Modules\CRM\Models\LeadTag()])
    </x-admin.create-card>
</x-admin-layout>
