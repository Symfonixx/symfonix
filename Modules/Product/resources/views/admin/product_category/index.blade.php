@section('title', __('product::category.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('product::product.menu.products'), 'url' => route('admin.products.index')],
            ['label' => __('product::category.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('product::category.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.products.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('product::category.actions.back_to_products') }}
        </a>
        <x-can perform="product.categories.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.product-categories.create') }}">
                {{ __('product::category.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('product::category.pages.index_title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('product::category.fields.name') }}</th>
                        <th>{{ __('product::category.fields.slug') }}</th>
                        <th>{{ __('product::category.fields.description') }}</th>
                        <th>{{ __('product::category.fields.products_count') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($categories as $category)
                        <tr>
                            <td class="fw-bold">{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->description ? \Illuminate\Support\Str::limit($category->description, 80) : '—' }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.product-categories.edit', $category) }}"
                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                    <i class="ki-duotone ki-message-edit fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('admin.product-categories.destroy', $category) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-10">{{ __('No records found') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
