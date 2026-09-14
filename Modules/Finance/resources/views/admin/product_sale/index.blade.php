@section('title', __('finance::product_sale.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::product_sale.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::product_sale.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-can perform="finance.product_sales.create">
    <div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('finance::product_sale.actions.record_sale') }}</h3>
        </div>
        <div class="card-body pt-0">
            <form method="POST" action="{{ route('admin.finance.product-sales.store') }}" class="row g-4 align-items-end">
                @csrf
                <div class="col-md-3">
                    <label for="product_id" class="form-label required">{{ __('finance::product_sale.fields.product') }}</label>
                    <select id="product_id" name="product_id" class="form-select form-select-solid @error('product_id') is-invalid @enderror"
                            data-control="select2" required>
                        <option value="">{{ __('finance::product_sale.fields.select_product') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    data-tax-rate-id="{{ $product->tax_rate_id }}"
                                    @selected((int) old('product_id') === $product->id)>
                                {{ $product->name }} ({{ number_format($product->price, 2) }} {{ $product->currency }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label for="quantity" class="form-label required">{{ __('finance::product_sale.fields.quantity') }}</label>
                    <input id="quantity" type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                           class="form-control form-control-solid @error('quantity') is-invalid @enderror" required/>
                    @error('quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label for="unit_price" class="form-label">{{ __('finance::product_sale.fields.unit_price') }}</label>
                    <input id="unit_price" type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price') }}"
                           class="form-control form-control-solid @error('unit_price') is-invalid @enderror"
                           placeholder="{{ __('finance::product_sale.fields.default_price') }}"/>
                </div>
                <div class="col-md-2">
                    <label for="sale_tax_rate_id" class="form-label">{{ __('tax::tax_rate.fields.name') }}</label>
                    <x-tax::tax-rate-select
                        name="tax_rate_id"
                        id="sale_tax_rate_id"
                        :selected="old('tax_rate_id')"
                        :tax-rates="$taxRates ?? []"
                    />
                </div>
                <div class="col-md-2">
                    <label for="company_id" class="form-label">{{ __('finance::product_sale.fields.company') }}</label>
                    <select id="company_id" name="company_id" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('N/A') }}</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected((int) old('company_id') === $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sold_at" class="form-label">{{ __('finance::product_sale.fields.sold_at') }}</label>
                    <input id="sold_at" type="date" name="sold_at" value="{{ old('sold_at', now()->toDateString()) }}"
                           class="form-control form-control-solid"/>
                </div>
                <div class="col-md-12">
                    <div class="text-muted fs-7" id="sale-total-preview"></div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </x-can>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const productSelect = document.getElementById('product_id');
                const qtyInput = document.getElementById('quantity');
                const priceInput = document.getElementById('unit_price');
                const taxSelect = document.getElementById('sale_tax_rate_id');
                const preview = document.getElementById('sale-total-preview');

                function updateSalePreview() {
                    if (!preview) return;
                    const qty = parseFloat(qtyInput?.value || 1);
                    const price = parseFloat(priceInput?.value || productSelect?.selectedOptions[0]?.dataset.price || 0);
                    const rateOpt = taxSelect?.selectedOptions[0];
                    const base = qty * price;
                    let tax = 0, total = base;
                    if (rateOpt?.value) {
                        const pct = parseFloat(rateOpt.dataset.percentage || 0);
                        const type = rateOpt.dataset.type || 'exclusive';
                        if (type === 'inclusive') {
                            tax = base * (pct / (100 + pct));
                            total = base;
                        } else {
                            tax = base * (pct / 100);
                            total = base + tax;
                        }
                    }
                    preview.textContent = `{{ __('finance::invoice.fields.total') }}: ${total.toFixed(2)} ({{ __('tax::report.fields.output_tax') }}: ${tax.toFixed(2)})`;
                }

                productSelect?.addEventListener('change', function () {
                    const opt = this.selectedOptions[0];
                    if (opt?.dataset.price && !priceInput.value) {
                        priceInput.value = opt.dataset.price;
                    }
                    if (opt?.dataset.taxRateId && taxSelect) {
                        taxSelect.value = opt.dataset.taxRateId;
                    }
                    updateSalePreview();
                });

                [qtyInput, priceInput, taxSelect].forEach(function (el) {
                    el?.addEventListener('input', updateSalePreview);
                    el?.addEventListener('change', updateSalePreview);
                });

                updateSalePreview();
            });
        </script>
    @endpush

    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('finance::product_sale.pages.index_title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('finance::product_sale.fields.product') }}</th>
                        <th>{{ __('finance::product_sale.fields.company') }}</th>
                        <th>{{ __('finance::product_sale.fields.quantity') }}</th>
                        <th>{{ __('finance::product_sale.fields.total') }}</th>
                        <th>{{ __('finance::product_sale.fields.sold_at') }}</th>
                        <th>{{ __('finance::product_sale.fields.deal') }}</th>
                        <th>{{ __('finance::product_sale.fields.invoice') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($sales as $sale)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $sale->product?->name }}</div>
                                <div class="text-muted fs-7">{{ $sale->product?->sku }}</div>
                            </td>
                            <td>{{ $sale->company?->name ?? __('N/A') }}</td>
                            <td>{{ $sale->quantity }} × {{ number_format($sale->unit_price, 2) }}</td>
                            <td class="fw-bold text-success">
                                {{ number_format($sale->total_amount, 2) }} {{ $sale->currency }}
                                @if((float) $sale->tax_amount > 0)
                                    <div class="text-muted fs-8">{{ __('tax::report.fields.output_tax') }}: {{ number_format($sale->tax_amount, 2) }}</div>
                                @endif
                            </td>
                            <td>{{ $sale->sold_at?->format('Y-m-d') }}</td>
                            <td>{{ $sale->deal?->title ?? '—' }}</td>
                            <td>
                                @if($sale->invoice)
                                    <a href="{{ route('admin.finance.invoices.show', $sale->invoice) }}" class="text-primary fw-semibold">
                                        {{ $sale->invoice->invoice_number }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <form class="d-inline" method="POST" action="{{ route('admin.finance.product-sales.destroy', $sale) }}"
                                      data-confirm="{{ __('finance::product_sale.messages.confirm_delete') }}">
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
                            <td colspan="8" class="text-center text-muted py-6">{{ __('finance::product_sale.messages.no_sales') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $sales->links() }}</div>
        </div>
    </div>
</x-admin-layout>
