@extends('product::layouts.master')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1>{{ __('product::product.pages.catalog_title') }}</h1>
                <p class="text-muted">{{ __('product::product.meta.index_description') }}</p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="{{ $product->main_image_link }}" class="card-img-top" alt="{{ $product->name }}"
                             style="height: 220px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            @if($product->category)
                                <span class="badge bg-light text-primary mb-2 align-self-start">{{ $product->category->name }}</span>
                            @endif
                            @if($product->is_featured)
                                <span class="badge bg-primary mb-2 align-self-start">{{ __('product::product.fields.featured') }}</span>
                            @endif
                            <h5 class="card-title">{{ $product->name }}</h5>
                            @if($product->short_description)
                                <p class="card-text text-muted">{{ $product->getTranslation('short_description', app()->getLocale()) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <p>{{ __('No records found') }}</p>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
