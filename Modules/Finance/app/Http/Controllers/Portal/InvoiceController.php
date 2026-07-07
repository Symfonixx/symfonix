<?php

namespace Modules\Finance\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\InvoiceService;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function downloadPdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        return $this->invoiceService->downloadPdf($invoice);
    }
}
