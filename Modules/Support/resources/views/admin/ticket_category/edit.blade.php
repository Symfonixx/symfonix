@section('title', __('support::ticket.category.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('support::ticket.category.pages.index_title'), 'url' => route('admin.ticket_categories.index')],
            ['label' => __('support::ticket.category.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('support::ticket.category.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('support::ticket.category.pages.edit_title')"
        :formUrl="route('admin.ticket_categories.update', $category)"
        :cancelUrl="route('admin.ticket_categories.index')">
        @method('PUT')
        @include('support::admin.ticket_category._form', ['category' => $category])
    </x-admin.create-card>
</x-admin-layout>
