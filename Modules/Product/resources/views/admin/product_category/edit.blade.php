@section('title', __('product::category.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('product::product.menu.products'), 'url' => route('admin.products.index')],
            ['label' => __('product::category.pages.index_title'), 'url' => route('admin.product-categories.index')],
            ['label' => __('product::category.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('product::category.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('product::category.pages.edit_title')"
        :formUrl="route('admin.product-categories.update', $category)"
        :cancelUrl="route('admin.product-categories.index')">
        @method('PUT')
        @include('product::admin.product_category._form', ['category' => $category])
    </x-admin.create-card>
</x-admin-layout>
