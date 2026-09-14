<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Modules\Base\Models\Seo;
use Modules\Base\Support\CompanyBranding;
use Modules\Base\Support\Meta;
use Modules\CRM\Http\Requests\RespondQuoteRequest;
use Modules\CRM\Models\Quote;
use Modules\CRM\Services\Quote\QuoteService;
use Symfony\Component\HttpFoundation\Response;

class PublicQuoteController extends Controller
{
    public function __construct(
        private readonly QuoteService $quoteService,
    ) {}

    public function show(string $uuid)
    {
        $quote = $this->findAuthorizedQuote($uuid);
        $quote = $this->quoteService->refreshExpiry($quote);
        $quote->load(['company', 'deal', 'lines', 'project']);

        $user = auth()->user();
        $siteName = Seo::get('website_name', config('app.name'));
        $canonical = route('quotes.public', $quote->uuid);
        $meta = (new Meta)
            ->title(__('crm::quote.public.title').' '.$quote->quote_number.' | '.$siteName)
            ->description(__('crm::quote.public.subtitle'))
            ->canonical($canonical)
            ->toArray();

        $branding = CompanyBranding::forInvoice();

        return $this->inertia('CRM::QuoteShow', [
            'quote' => [
                'uuid' => $quote->uuid,
                'quote_number' => $quote->quote_number,
                'status' => $quote->status,
                'currency' => $quote->currency,
                'subtotal' => (float) $quote->subtotal,
                'discount_amount' => (float) $quote->discount_amount,
                'tax_amount' => (float) $quote->tax_amount,
                'total' => (float) $quote->total,
                'terms' => $quote->terms,
                'notes' => $quote->notes,
                'issued_at' => $quote->issued_at?->format('Y-m-d'),
                'expires_at' => $quote->expires_at?->format('Y-m-d'),
                'responded_at' => $quote->responded_at?->toIso8601String(),
                'responder_name' => $quote->responder_name,
                'company' => [
                    'name' => $quote->company?->name,
                    'email' => $quote->company?->email,
                ],
                'deal' => [
                    'title' => $quote->deal?->title,
                ],
                'project' => $quote->project ? [
                    'id' => $quote->project->id,
                    'title' => $quote->project->title,
                ] : null,
                'lines' => $quote->lines->map(fn ($line) => [
                    'description' => $line->description,
                    'item_type' => $line->item_type,
                    'quantity' => $line->quantity,
                    'unit_price' => (float) $line->unit_price,
                    'discount_percent' => (float) $line->discount_percent,
                    'tax_percent' => (float) $line->tax_percent,
                    'discount_amount' => (float) $line->discount_amount,
                    'tax_amount' => (float) $line->tax_amount,
                    'amount' => (float) $line->amount,
                ])->values(),
                'can_respond' => $quote->isRespondable() && $user?->isCustomer(),
            ],
            'viewer' => [
                'name' => $user?->name,
                'email' => $user?->email,
            ],
            'branding' => [
                'name' => $branding['name'],
                'logo_url' => $branding['logo_url'],
                'sign_url' => $branding['sign_url'],
                'phone' => $branding['phone'],
                'email' => $branding['email'],
                'address' => $branding['address'],
            ],
            'urls' => [
                'pdf' => route('quotes.public.pdf', $quote->uuid),
                'accept' => route('quotes.public.accept', $quote->uuid),
                'reject' => route('quotes.public.reject', $quote->uuid),
            ],
            'labels' => [
                'title' => __('crm::quote.public.title'),
                'subtitle' => __('crm::quote.public.subtitle'),
                'expired' => __('crm::quote.public.expired_message'),
                'accepted' => __('crm::quote.public.accepted_message'),
                'rejected' => __('crm::quote.public.rejected_message'),
                'void' => __('crm::quote.public.void_message'),
                'accept_title' => __('crm::quote.public.accept_title'),
                'reject_title' => __('crm::quote.public.reject_title'),
                'your_name' => __('crm::quote.public.your_name'),
                'your_email' => __('crm::quote.public.your_email'),
                'optional_note' => __('crm::quote.public.optional_note'),
                'confirm_accept' => __('crm::quote.public.confirm_accept'),
                'confirm_reject' => __('crm::quote.public.confirm_reject'),
                'download_pdf' => __('crm::quote.actions.download_pdf'),
                'description' => __('crm::quote.fields.description'),
                'quantity' => __('crm::quote.fields.quantity'),
                'unit_price' => __('crm::quote.fields.unit_price'),
                'discount' => __('crm::quote.fields.discount'),
                'tax' => __('crm::quote.fields.tax'),
                'amount' => __('crm::quote.fields.amount'),
                'subtotal' => __('crm::quote.fields.subtotal'),
                'total' => __('crm::quote.fields.total'),
                'terms' => __('crm::quote.fields.terms'),
                'issued_at' => __('crm::quote.fields.issued_at'),
                'expires_at' => __('crm::quote.fields.expires_at'),
                'quote_to' => __('crm::quote.pdf.quote_to'),
                'company_sign' => __('crm::quote.pdf.company_sign'),
                'status' => __('crm::quote.status.'.$quote->status),
            ],
        ], $meta);
    }

    public function accept(RespondQuoteRequest $request, string $uuid): RedirectResponse
    {
        $quote = $this->findAuthorizedQuote($uuid);
        $this->quoteService->accept($quote, $request->validated(), $request->ip());

        return redirect()
            ->route('quotes.public', $quote->uuid)
            ->with('success', __('crm::quote.messages.accepted'));
    }

    public function reject(RespondQuoteRequest $request, string $uuid): RedirectResponse
    {
        $quote = $this->findAuthorizedQuote($uuid);
        $this->quoteService->reject($quote, $request->validated(), $request->ip());

        return redirect()
            ->route('quotes.public', $quote->uuid)
            ->with('success', __('crm::quote.messages.rejected'));
    }

    public function downloadPdf(string $uuid): Response
    {
        $quote = $this->findAuthorizedQuote($uuid);
        $quote = $this->quoteService->refreshExpiry($quote);

        return $this->quoteService->downloadPdf($quote);
    }

    private function findAuthorizedQuote(string $uuid): Quote
    {
        $quote = Quote::query()->where('uuid', $uuid)->firstOrFail();

        if ($quote->status === Quote::STATUS_DRAFT) {
            abort(404);
        }

        $this->authorizeQuoteAccess($quote);

        return $quote;
    }

    private function authorizeQuoteAccess(Quote $quote): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if ($user->isAdmin() && $user->can('sales.quotes.view')) {
            return;
        }

        if ($user->isCustomer() && in_array((int) $quote->company_id, $user->companyIds(), true)) {
            return;
        }

        abort(403);
    }
}
