@section('title', __('product::product.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('product::product.menu.products'), 'url' => route('admin.products.index')],
            ['label' => __('product::product.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('product::product.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

@section('js')
    @include('base::shared._tinymce', ['selector' => '#product-description-editor', 'height' => 550])
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('product::product.pages.create_title')" :formUrl="route('admin.products.store')" :cancelUrl="route('admin.products.index')">
        @include('product::admin.product._form')
    </x-admin.create-card>
</x-admin-layout>
