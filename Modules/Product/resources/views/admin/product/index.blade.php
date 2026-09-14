@section('title', __('product::product.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('product::product.menu.products')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('product::product.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.product-categories.index') }}">
            <i class="bi bi-tags me-1"></i>{{ __('product::category.actions.manage_categories') }}
        </a>
        <x-can perform="product.catalog.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.products.create') }}">
                {{ __('product::product.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('product::product.filters.title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-4 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input id="search" type="text" name="search" class="form-control form-control-solid"
                           value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('product::product.search.placeholder') }}"/>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="product_category_id" class="form-label">{{ __('product::product.fields.category') }}</label>
                    <select id="product_category_id" name="product_category_id" class="form-select form-select-solid"
                            data-control="select2" data-placeholder="{{ __('product::product.filters.all_categories') }}">
                        <option value="">{{ __('product::product.filters.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) ($filters['product_category_id'] ?? 0) === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="status" class="form-label">{{ __('product::product.fields.status') }}</label>
                    <select id="status" name="status" class="form-select form-select-solid">
                        <option value="active" @selected(($filters['status'] ?? 'active') === 'active')>{{ __('product::product.status.active') }}</option>
                        <option value="archived" @selected(($filters['status'] ?? '') === 'archived')>{{ __('product::product.status.archived') }}</option>
                        <option value="" @selected(($filters['status'] ?? 'active') === '')>{{ __('product::product.filters.all_statuses') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="is_published" class="form-label">{{ __('product::product.fields.website_visibility') }}</label>
                    <select id="is_published" name="is_published" class="form-select form-select-solid">
                        <option value="">{{ __('product::product.filters.all_visibility') }}</option>
                        <option value="1" @selected(($filters['is_published'] ?? '') === '1')>{{ __('product::product.visibility.published') }}</option>
                        <option value="0" @selected(($filters['is_published'] ?? '') === '0')>{{ __('product::product.visibility.draft') }}</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('product::product.actions.apply_filters') }}</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light">{{ __('product::product.actions.clear_filters') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('product::product.pages.index_title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('product::product.fields.name') }}</th>
                        <th>{{ __('product::product.fields.category') }}</th>
                        <th>{{ __('product::product.fields.sku') }}</th>
                        <th>{{ __('product::product.fields.price') }}</th>
                        <th>{{ __('product::product.fields.billing') }}</th>
                        <th>{{ __('product::product.fields.status') }}</th>
                        <th>{{ __('product::product.fields.website_visibility') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($model as $product)
                        <tr>
                            <td>
                                {{ $product->name }}
                                @if($product->is_featured)
                                    <span class="badge badge-light-primary ms-1">{{ __('product::product.fields.featured') }}</span>
                                @endif
                            </td>
                            <td>{{ $product->category?->name }}</td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td>{{ number_format($product->price, 2) }} {{ $product->currency }}</td>
                            <td>{{ __('product::product.billing.'.$product->billing_type) }}</td>
                            <td>
                                <span class="badge badge-light-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ __('product::product.status.'.$product->status) }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.products.toggle-published', $product) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox"
                                               onchange="this.form.submit()"
                                               @checked($product->is_published)
                                               title="{{ $product->is_published ? __('product::product.visibility.published') : __('product::product.visibility.draft') }}"/>
                                    </div>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                    <i class="ki-duotone ki-message-edit fs-1"><span class="path1"></span><span class="path2"></span></i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('admin.products.destroy', $product) }}">
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
                            <td colspan="8" class="text-center text-muted py-10">{{ __('No records found') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $model->withQueryString()->links() }}</div>
        </div>
    </div>
</x-admin-layout>
