@section('title', __('tax::tax_rate.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('tax::tax.menu.tax'), 'url' => route('admin.tax.rates.index')],
            ['label' => __('tax::tax_rate.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('tax::tax_rate.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('tax::tax_rate.pages.create_title')"
        :formUrl="route('admin.tax.rates.store')"
        :cancelUrl="route('admin.tax.rates.index')">
        @include('tax::admin.tax_rate._form')
    </x-admin.create-card>
</x-admin-layout>
