@section('title', __('tax::tax_rate.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('tax::tax.menu.tax'), 'url' => route('admin.tax.rates.index')],
            ['label' => __('tax::tax_rate.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('tax::tax_rate.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('tax::tax_rate.pages.edit_title')"
        :formUrl="route('admin.tax.rates.update', $taxRate->id)"
        :cancelUrl="route('admin.tax.rates.index')">
        @method('PUT')
        @include('tax::admin.tax_rate._form', ['taxRate' => $taxRate])
    </x-admin.create-card>
</x-admin-layout>
