@section('title', __('product::product.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('product::product.menu.products'), 'url' => route('admin.products.index')],
            ['label' => __('product::product.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('product::product.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

@section('js')
    @include('base::shared._tinymce', ['selector' => '#product-description-editor', 'height' => 550])
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('product::product.pages.edit_title')" :formUrl="route('admin.products.update', $product)" :cancelUrl="route('admin.products.index')">
        @method('PUT')
        @include('product::admin.product._form', ['product' => $product])
    </x-admin.create-card>
</x-admin-layout>
