<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CRM\Http\Requests\StoreQuoteRequest;
use Modules\CRM\Http\Requests\UpdateQuoteRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Quote;
use Modules\CRM\Services\Quote\QuoteService;
use Modules\Finance\Services\CurrencyService;
use Modules\Product\Models\Product;
use Modules\Services\Models\Service;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends Controller
{
    public function __construct(
        private readonly QuoteService $quoteService,
        private readonly CurrencyService $currencyService,
    ) {
        $this->authorizeResource(Quote::class, 'quote');
        $this->setActive('crm');
        $this->setActive('quotes');
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'company_id', 'deal_id', 'search']);
        $quotes = $this->quoteService->paginate($filters);
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);

        return view('crm::admin.quote.index', compact('quotes', 'filters', 'companies'));
    }

    public function create(Request $request): View
    {
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $deals = Deal::query()
            ->with('company:id,name')
            ->when($request->integer('company_id'), fn ($q, $id) => $q->where('company_id', $id))
            ->latest()
            ->get(['id', 'title', 'company_id', 'currency', 'value']);

        $selectedDeal = null;
        $prefillLines = [['item_type' => 'service', 'service_id' => '', 'product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => '', 'discount_percent' => 0, 'tax_percent' => 0]];

        if ($request->filled('deal_id')) {
            $selectedDeal = Deal::query()->with('services')->find($request->integer('deal_id'));
            if ($selectedDeal && $selectedDeal->services->isNotEmpty()) {
                $prefillLines = $selectedDeal->services->map(fn ($service) => [
                    'item_type' => 'service',
                    'service_id' => $service->id,
                    'product_id' => '',
                    'description' => $service->getTranslation('title', app()->getLocale()),
                    'quantity' => (int) $service->pivot->quantity,
                    'unit_price' => $service->pivot->unit_price,
                    'discount_percent' => 0,
                    'tax_percent' => 0,
                ])->values()->all();
            }
        }

        return view('crm::admin.quote.create', [
            'companies' => $companies,
            'deals' => $deals,
            'services' => Service::query()->orderBy('id')->get(),
            'products' => Product::query()->orderBy('id')->get(),
            'currencies' => $this->currencyService->supportedCurrencies(),
            'defaultCurrency' => $this->currencyService->defaultCurrency(),
            'selectedDeal' => $selectedDeal,
            'prefillLines' => $prefillLines,
        ]);
    }

    public function store(StoreQuoteRequest $request): RedirectResponse
    {
        $quote = $this->quoteService->create($request->validated());

        session()->flushMessage(true, __('crm::quote.messages.created'));

        return redirect()->route('admin.quotes.show', $quote);
    }

    public function show(Quote $quote): View
    {
        $quote = $this->quoteService->refreshExpiry($quote);
        $quote->load(['company', 'deal', 'project', 'lines.service', 'lines.product']);
        $companyBranding = \Modules\Base\Support\CompanyBranding::forInvoice();

        return view('crm::admin.quote.show', compact('quote', 'companyBranding'));
    }

    public function edit(Quote $quote): View|RedirectResponse
    {
        if (! in_array($quote->status, [Quote::STATUS_DRAFT, Quote::STATUS_SENT], true)) {
            session()->flushMessage(false, __('crm::quote.messages.not_editable'));

            return redirect()->route('admin.quotes.show', $quote);
        }

        $quote->load('lines');
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $deals = Deal::query()->latest()->get(['id', 'title', 'company_id', 'currency', 'value']);

        $prefillLines = $quote->lines->map(fn ($line) => [
            'item_type' => $line->item_type,
            'service_id' => $line->service_id,
            'product_id' => $line->product_id,
            'description' => $line->description,
            'quantity' => $line->quantity,
            'unit_price' => $line->unit_price,
            'discount_percent' => $line->discount_percent,
            'tax_percent' => $line->tax_percent,
        ])->values()->all();

        return view('crm::admin.quote.edit', [
            'quote' => $quote,
            'companies' => $companies,
            'deals' => $deals,
            'services' => Service::query()->orderBy('id')->get(),
            'products' => Product::query()->orderBy('id')->get(),
            'currencies' => $this->currencyService->supportedCurrencies(),
            'defaultCurrency' => $quote->currency,
            'prefillLines' => $prefillLines,
        ]);
    }

    public function update(UpdateQuoteRequest $request, Quote $quote): RedirectResponse
    {
        $this->quoteService->update($quote, $request->validated());

        session()->flushMessage(true, __('crm::quote.messages.updated'));

        return redirect()->route('admin.quotes.show', $quote);
    }

    public function destroy(Quote $quote): RedirectResponse
    {
        $this->quoteService->deleteQuote($quote);

        session()->flushMessage(true, __('crm::quote.messages.deleted'));

        return redirect()->route('admin.quotes.index');
    }

    public function markSent(Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);
        $this->quoteService->markAsSent($quote);

        session()->flushMessage(true, __('crm::quote.messages.sent'));

        return back();
    }

    public function void(Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);
        $this->quoteService->voidQuote($quote);

        session()->flushMessage(true, __('crm::quote.messages.voided'));

        return back();
    }

    public function downloadPdf(Quote $quote): Response
    {
        $this->authorize('view', $quote);

        return $this->quoteService->downloadPdf($quote);
    }

    public function createFromDeal(Deal $deal): RedirectResponse
    {
        $this->authorize('create', Quote::class);

        if (! $deal->company_id) {
            session()->flushMessage(false, __('crm::quote.messages.deal_quote_failed'));

            return back();
        }

        try {
            $quote = $this->quoteService->createFromDeal($deal);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = collect($e->errors())->flatten()->first()
                ?: __('crm::quote.messages.deal_quote_failed');
            session()->flushMessage(false, $message);

            return back();
        }

        session()->flushMessage(true, __('crm::quote.messages.created'));

        return redirect()->route('admin.quotes.show', $quote);
    }
}
