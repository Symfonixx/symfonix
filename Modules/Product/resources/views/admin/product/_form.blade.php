@php($productData = $product ?? null)

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('Please fix the following errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('product::product.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $productData?->name) }}" required autofocus/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="product_category_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('product::product.fields.category') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="product_category_id" class="form-select form-select-solid @error('product_category_id') is-invalid @enderror"
                data-control="select2" name="product_category_id" required>
            <option value="">{{ __('product::product.fields.select_category') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('product_category_id', $productData?->product_category_id) === $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('product_category_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="sku" class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.sku') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="sku" type="text" class="form-control form-control-solid @error('sku') is-invalid @enderror"
               name="sku" value="{{ old('sku', $productData?->sku) }}"/>
        @error('sku')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="description" class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.description') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="description" class="form-control form-control-solid @error('description') is-invalid @enderror"
                  name="description" rows="4">{{ old('description', $productData?->description) }}</textarea>
        @error('description')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="price" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('product::product.fields.price') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-4">
            <div class="col-md-6">
                <input id="price" type="number" step="0.01" min="0"
                       class="form-control form-control-solid @error('price') is-invalid @enderror"
                       name="price" value="{{ old('price', $productData?->price) }}" required/>
                @error('price')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-3">
                <select id="currency" class="form-select form-select-solid @error('currency') is-invalid @enderror" name="currency">
                    @foreach(['USD', 'EUR', 'GBP', 'TRY'] as $currency)
                        <option value="{{ $currency }}" @selected(old('currency', $productData?->currency ?? 'USD') === $currency)>{{ $currency }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="billing_type" class="form-select form-select-solid @error('billing_type') is-invalid @enderror" name="billing_type" required>
                    @foreach(['one_time', 'monthly', 'quarterly', 'yearly'] as $billingType)
                        <option value="{{ $billingType }}" @selected(old('billing_type', $productData?->billing_type ?? 'one_time') === $billingType)>
                            {{ __('product::product.billing.'.$billingType) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('product::product.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="status" class="form-select form-select-solid @error('status') is-invalid @enderror" name="status" required>
            <option value="active" @selected(old('status', $productData?->status ?? 'active') === 'active')>{{ __('product::product.status.active') }}</option>
            <option value="archived" @selected(old('status', $productData?->status) === 'archived')>{{ __('product::product.status.archived') }}</option>
        </select>
        @error('status')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.featured') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                   id="is_featured" @checked(old('is_featured', $productData?->is_featured))/>
            <label class="form-check-label" for="is_featured">{{ __('product::product.fields.featured_help') }}</label>
        </div>
    </div>
</div>
