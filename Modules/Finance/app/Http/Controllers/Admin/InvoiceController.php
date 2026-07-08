<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\Base\Support\CompanyBranding;
use Modules\Finance\Http\Requests\StoreInvoiceRequest;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\InvoiceService;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {
        $this->authorizeResource(Invoice::class, 'invoice');
        $this->setActive('finance_invoices');
    }

    public function index(): View
    {
        $filters = request()->only(['status', 'company_id']);
        $invoices = $this->invoiceService->paginate($filters);
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);

        return view('finance::admin.invoice.index', compact('invoices', 'filters', 'companies'));
    }

    public function create(): View
    {
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);

        return view('finance::admin.invoice.create', compact('companies'));
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoiceService->markAsSent(
            $this->invoiceService->createManual($request->validated())
        );

        session()->flushMessage(true, __('finance::invoice.messages.created_and_sent'));

        return redirect()->route('admin.finance.invoices.show', $invoice);
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['company', 'lines.service', 'subscription', 'deal', 'project']);
        $companyBranding = CompanyBranding::forInvoice();

        return view('finance::admin.invoice.show', compact('invoice', 'companyBranding'));
    }

    public function markSent(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $this->invoiceService->markAsSent($invoice);

        session()->flushMessage(true, __('finance::invoice.messages.sent'));

        return back();
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $this->invoiceService->markAsPaid($invoice);

        session()->flushMessage(true, __('finance::invoice.messages.paid'));

        return back();
    }

    public function void(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $this->invoiceService->voidInvoice($invoice);

        session()->flushMessage(true, __('finance::invoice.messages.voided'));

        return back();
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->deleteInvoice($invoice);

        session()->flushMessage(true, __('finance::invoice.messages.deleted'));

        return redirect()->route('admin.finance.invoices.index');
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        return $this->invoiceService->downloadPdf($invoice);
    }

    public function createFromDeal(Deal $deal): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $invoice = $this->invoiceService->createFromDeal($deal);

        if (! $invoice) {
            session()->flushMessage(false, __('finance::invoice.messages.deal_invoice_failed'));

            return back();
        }

        session()->flushMessage(true, __('finance::invoice.messages.created_and_sent'));

        return redirect()->route('admin.finance.invoices.show', $invoice);
    }
}
