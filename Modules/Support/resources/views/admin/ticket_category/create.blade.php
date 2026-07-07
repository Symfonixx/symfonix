@section('title', __('support::ticket.category.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('support::ticket.category.pages.index_title'), 'url' => route('admin.ticket_categories.index')],
            ['label' => __('support::ticket.category.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('support::ticket.category.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('support::ticket.category.pages.create_title')"
        :formUrl="route('admin.ticket_categories.store')"
        :cancelUrl="route('admin.ticket_categories.index')">
        @include('support::admin.ticket_category._form', ['category' => new \Modules\Support\Models\TicketCategory()])
    </x-admin.create-card>
</x-admin-layout>
