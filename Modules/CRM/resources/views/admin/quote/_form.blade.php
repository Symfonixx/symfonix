@php
    $lines = old('lines', $prefillLines ?? [['item_type' => 'service', 'service_id' => '', 'product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => '', 'discount_percent' => 0, 'tax_percent' => 0]]);
    $selectedCompanyId = (int) old('company_id', $quote->company_id ?? $selectedDeal?->company_id ?? request('company_id'));
    $selectedDealId = (int) old('deal_id', $quote->deal_id ?? $selectedDeal?->id ?? request('deal_id'));
@endphp

<div class="row g-5 mb-8">
    <div class="col-md-6">
        <label class="form-label required">{{ __('crm::quote.fields.company') }}</label>
        <select name="company_id" id="quote-company" class="form-select form-select-solid @error('company_id') is-invalid @enderror" required data-control="select2">
            <option value="">{{ __('crm::quote.fields.select_company') }}</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" @selected($selectedCompanyId === $company->id)>{{ $company->name }}</option>
            @endforeach
        </select>
        @error('company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label required">{{ __('crm::quote.fields.deal') }}</label>
        <select name="deal_id" id="quote-deal" class="form-select form-select-solid @error('deal_id') is-invalid @enderror" required data-control="select2">
            <option value="">{{ __('crm::quote.fields.select_deal') }}</option>
            @foreach($deals as $deal)
                <option value="{{ $deal->id }}"
                        data-company="{{ $deal->company_id }}"
                        data-currency="{{ $deal->currency }}"
                        @selected($selectedDealId === $deal->id)>
                    {{ $deal->title }}
                </option>
            @endforeach
        </select>
        @error('deal_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label required">{{ __('crm::quote.fields.currency') }}</label>
        <select name="currency" class="form-select form-select-solid" required>
            @foreach($currencies as $currency)
                <option value="{{ $currency }}" @selected(old('currency', $quote->currency ?? $selectedDeal?->currency ?? $defaultCurrency) === $currency)>
                    {{ $currency }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label required">{{ __('crm::quote.fields.issued_at') }}</label>
        <input type="date" name="issued_at" class="form-control form-control-solid"
               value="{{ old('issued_at', isset($quote) ? $quote->issued_at?->format('Y-m-d') : now()->toDateString()) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('crm::quote.fields.expires_at') }}</label>
        <input type="date" name="expires_at" class="form-control form-control-solid"
               value="{{ old('expires_at', isset($quote) ? $quote->expires_at?->format('Y-m-d') : now()->addDays(30)->toDateString()) }}">
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('crm::quote.fields.terms') }}</label>
        <textarea name="terms" rows="3" class="form-control form-control-solid">{{ old('terms', $quote->terms ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('crm::quote.fields.notes') }}</label>
        <textarea name="notes" rows="2" class="form-control form-control-solid">{{ old('notes', $quote->notes ?? '') }}</textarea>
    </div>
</div>

<h4 class="fw-bold mb-4">{{ __('crm::quote.fields.line_items') }}</h4>
<div id="quote-line-items">
    @foreach($lines as $index => $line)
        <div class="border rounded p-4 mb-4 quote-line-row">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">{{ __('crm::quote.fields.item_type') }}</label>
                    <select name="lines[{{ $index }}][item_type]" class="form-select form-select-solid quote-item-type">
                        <option value="service" @selected(($line['item_type'] ?? 'service') === 'service')>{{ __('crm::quote.fields.service') }}</option>
                        <option value="product" @selected(($line['item_type'] ?? '') === 'product')>{{ __('crm::quote.fields.product') }}</option>
                    </select>
                </div>
                <div class="col-md-4 quote-service-wrap" @style([ 'display: none' => ($line['item_type'] ?? 'service') === 'product' ])>
                    <label class="form-label">{{ __('crm::quote.fields.service') }}</label>
                    <select name="lines[{{ $index }}][service_id]" class="form-select form-select-solid quote-service-select" data-control="select2">
                        <option value="">{{ __('crm::quote.fields.select_service') }}</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @selected((int)($line['service_id'] ?? 0) === $service->id)>
                                {{ $service->getTranslation('title', app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 quote-product-wrap" @style([ 'display: none' => ($line['item_type'] ?? 'service') !== 'product' ])>
                    <label class="form-label">{{ __('crm::quote.fields.product') }}</label>
                    <select name="lines[{{ $index }}][product_id]" class="form-select form-select-solid quote-product-select" data-control="select2">
                        <option value="">{{ __('crm::quote.fields.select_product') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" @selected((int)($line['product_id'] ?? 0) === $product->id)>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crm::quote.fields.quantity') }}</label>
                    <input type="number" min="1" name="lines[{{ $index }}][quantity]" class="form-control form-control-solid"
                           value="{{ $line['quantity'] ?? 1 }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crm::quote.fields.unit_price') }}</label>
                    <input type="number" step="0.01" min="0" name="lines[{{ $index }}][unit_price]" class="form-control form-control-solid quote-unit-price"
                           value="{{ $line['unit_price'] ?? '' }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crm::quote.fields.discount_percent') }}</label>
                    <input type="number" step="0.01" min="0" max="100" name="lines[{{ $index }}][discount_percent]" class="form-control form-control-solid"
                           value="{{ $line['discount_percent'] ?? 0 }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crm::quote.fields.tax_percent') }}</label>
                    <input type="number" step="0.01" min="0" max="100" name="lines[{{ $index }}][tax_percent]" class="form-control form-control-solid"
                           value="{{ $line['tax_percent'] ?? 0 }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('crm::quote.fields.description') }}</label>
                    <input type="text" name="lines[{{ $index }}][description]" class="form-control form-control-solid"
                           value="{{ $line['description'] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    @if($index > 0)
                        <button type="button" class="btn btn-light-danger w-100 remove-quote-line">{{ __('crm::quote.actions.remove_line') }}</button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
<button type="button" class="btn btn-light-primary btn-sm mb-8" id="add-quote-line">
    <i class="bi bi-plus-lg me-1"></i>{{ __('crm::quote.actions.add_line') }}
</button>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('quote-line-items');
    const companySelect = document.getElementById('quote-company');
    const dealSelect = document.getElementById('quote-deal');
    const currencySelect = document.querySelector('select[name="currency"]');
    let lineIndex = container.querySelectorAll('.quote-line-row').length;

    const allDeals = Array.from(dealSelect?.querySelectorAll('option') || [])
        .filter((opt) => opt.value)
        .map((opt) => ({
            id: opt.value,
            title: opt.textContent.trim(),
            companyId: String(opt.dataset.company || ''),
            currency: opt.dataset.currency || '',
        }));

    const selectedDealId = @json((string) ($selectedDealId ?: ''));
    const placeholderDeal = @json(__('crm::quote.fields.select_deal'));

    function rebuildDealOptions(companyId, preferredDealId = '') {
        if (!dealSelect) return;

        const previousValue = preferredDealId || dealSelect.value;
        const $deal = window.jQuery ? $(dealSelect) : null;

        if ($deal?.data('select2')) {
            $deal.select2('destroy');
        }

        dealSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = placeholderDeal;
        dealSelect.appendChild(placeholder);

        const filtered = companyId
            ? allDeals.filter((deal) => deal.companyId === String(companyId))
            : [];

        filtered.forEach((deal) => {
            const option = document.createElement('option');
            option.value = deal.id;
            option.textContent = deal.title;
            option.dataset.company = deal.companyId;
            option.dataset.currency = deal.currency;
            if (String(previousValue) === String(deal.id)) {
                option.selected = true;
            }
            dealSelect.appendChild(option);
        });

        if (!filtered.some((deal) => String(deal.id) === String(previousValue))) {
            dealSelect.value = '';
        }

        if ($deal && $.fn.select2) {
            $deal.select2({ width: '100%' });
        }
    }

    function syncCurrencyFromDeal() {
        const selected = dealSelect?.selectedOptions?.[0];
        if (selected?.dataset?.currency && currencySelect) {
            currencySelect.value = selected.dataset.currency;
        }
    }

    function filterDeals(preferredDealId = '') {
        rebuildDealOptions(companySelect?.value || '', preferredDealId);
        syncCurrencyFromDeal();
    }

    function toggleLineType(row) {
        const type = row.querySelector('.quote-item-type')?.value;
        const serviceWrap = row.querySelector('.quote-service-wrap');
        const productWrap = row.querySelector('.quote-product-wrap');
        if (type === 'product') {
            serviceWrap.style.display = 'none';
            productWrap.style.display = '';
            row.querySelector('.quote-service-select').value = '';
        } else {
            productWrap.style.display = 'none';
            serviceWrap.style.display = '';
            row.querySelector('.quote-product-select').value = '';
        }
    }

    if (window.jQuery) {
        $(companySelect).on('change', function () {
            filterDeals('');
        });
        $(dealSelect).on('change', syncCurrencyFromDeal);
    } else {
        companySelect?.addEventListener('change', () => filterDeals(''));
        dealSelect?.addEventListener('change', syncCurrencyFromDeal);
    }

    filterDeals(selectedDealId);

    container.querySelectorAll('.quote-line-row').forEach(toggleLineType);

    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('quote-item-type')) {
            toggleLineType(e.target.closest('.quote-line-row'));
        }
        if (e.target.classList.contains('quote-product-select')) {
            const price = e.target.selectedOptions[0]?.dataset?.price;
            const row = e.target.closest('.quote-line-row');
            if (price && row) {
                row.querySelector('.quote-unit-price').value = price;
            }
        }
    });

    document.getElementById('add-quote-line')?.addEventListener('click', function () {
        const template = container.querySelector('.quote-line-row').cloneNode(true);
        template.querySelectorAll('input, select').forEach(function (el) {
            if (el.name) {
                el.name = el.name.replace(/lines\[\d+\]/, `lines[${lineIndex}]`);
            }
            if (el.tagName === 'INPUT') {
                if (el.name.includes('[quantity]')) el.value = 1;
                else if (el.name.includes('[discount_percent]') || el.name.includes('[tax_percent]')) el.value = 0;
                else el.value = '';
            }
            if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
                el.classList.remove('select2-hidden-accessible');
                el.removeAttribute('data-select2-id');
                el.removeAttribute('aria-hidden');
                el.removeAttribute('tabindex');
            }
        });
        template.querySelectorAll('.select2-container').forEach(n => n.remove());
        const removeBtnWrap = template.querySelector('.d-flex.align-items-end');
        if (removeBtnWrap) {
            removeBtnWrap.innerHTML = `<button type="button" class="btn btn-light-danger w-100 remove-quote-line">{{ __('crm::quote.actions.remove_line') }}</button>`;
        }
        container.appendChild(template);
        toggleLineType(template);
        if (window.jQuery && $.fn.select2) {
            $(template).find('[data-control="select2"]').select2({ width: '100%' });
        }
        lineIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-quote-line')) {
            e.target.closest('.quote-line-row')?.remove();
        }
    });
});
</script>
@endpush
