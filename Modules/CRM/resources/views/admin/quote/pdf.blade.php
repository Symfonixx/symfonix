@php
    use Modules\Finance\Support\DomPdfText;

    $isRtl = DomPdfText::isRtl();
    $t = static fn (?string $text): string => DomPdfText::shape($text);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $quote->quote_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1e293b;
            @if($isRtl)
                text-align: right;
            @endif
        }
        .header-table { width: 100%; margin-bottom: 30px; }
        .header-table td { vertical-align: top; }
        .issuer-logo { max-height: 50px; max-width: 200px; margin-bottom: 8px; }
        .issuer-name { font-size: 16px; font-weight: bold; color: #0f172a; margin-bottom: 6px; }
        .issuer-contact { color: #64748b; line-height: 1.6; }
        .title { font-size: 24px; font-weight: bold; color: #0f172a; }
        .meta { margin-top: 8px; color: #64748b; }
        .section-title {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 6px;
            @unless($isRtl)
                text-transform: uppercase;
            @endunless
        }
        .bill-to { margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th {
            background: #f8fafc;
            text-align: {{ $isRtl ? 'right' : 'left' }};
            padding: 8px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 11px;
        }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .totals td { border: none; }
        .grand-total { font-size: 14px; font-weight: bold; }
        .footer { margin-top: 24px; text-align: center; color: #64748b; }
        .quote-bottom { margin-top: 48px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
        .sign-block { margin-top: 32px; text-align: center; }
        .sign-label {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 8px;
            @unless($isRtl)
                text-transform: uppercase;
            @endunless
        }
        .sign-image { max-height: 80px; max-width: 200px; }
        .terms { margin-top: 24px; color: #64748b; white-space: pre-wrap; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            @if($isRtl)
                <td class="text-start">
                    <div class="title">{{ $t(__('crm::quote.pdf.title')) }} {{ $quote->quote_number }}</div>
                    <div class="meta">
                        {{ $t(__('crm::quote.pdf.issue_date')) }}: {{ $quote->issued_at?->format('Y-m-d') }}
                        @if($quote->expires_at)
                            &nbsp;|&nbsp;
                            {{ $t(__('crm::quote.pdf.expiry_date')) }}: {{ $quote->expires_at->format('Y-m-d') }}
                        @endif
                    </div>
                </td>
                <td class="text-end">
                    @if($companyBranding['logo_path'])
                        <img src="{{ $companyBranding['logo_path'] }}" class="issuer-logo" alt="{{ $companyBranding['name'] }}">
                    @else
                        <div class="issuer-name">{{ $t($companyBranding['name']) }}</div>
                    @endif
                    <div class="issuer-contact">
                        @if($companyBranding['phone']){{ $companyBranding['phone'] }}<br>@endif
                        @if($companyBranding['email']){{ $companyBranding['email'] }}<br>@endif
                        @if($companyBranding['address']){{ $t($companyBranding['address']) }}@endif
                    </div>
                </td>
            @else
                <td>
                    @if($companyBranding['logo_path'])
                        <img src="{{ $companyBranding['logo_path'] }}" class="issuer-logo" alt="{{ $companyBranding['name'] }}">
                    @else
                        <div class="issuer-name">{{ $companyBranding['name'] }}</div>
                    @endif
                    <div class="issuer-contact">
                        @if($companyBranding['phone']){{ $companyBranding['phone'] }}<br>@endif
                        @if($companyBranding['email']){{ $companyBranding['email'] }}<br>@endif
                        @if($companyBranding['address']){{ $companyBranding['address'] }}@endif
                    </div>
                </td>
                <td class="text-end">
                    <div class="title">{{ __('crm::quote.pdf.title') }} {{ $quote->quote_number }}</div>
                    <div class="meta">
                        {{ __('crm::quote.pdf.issue_date') }}: {{ $quote->issued_at?->format('Y-m-d') }}
                        @if($quote->expires_at)
                            &nbsp;|&nbsp;
                            {{ __('crm::quote.pdf.expiry_date') }}: {{ $quote->expires_at->format('Y-m-d') }}
                        @endif
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <div class="bill-to">
        <div class="section-title">{{ $t(__('crm::quote.pdf.quote_to')) }}</div>
        <strong>{{ $t($quote->company?->name) }}</strong><br>
        @if($quote->company?->email){{ $quote->company->email }}<br>@endif
        @if($quote->deal){{ $t(__('crm::quote.fields.deal')) }}: {{ $t($quote->deal->title) }}<br>@endif
    </div>

    <table>
        <thead>
        <tr>
            <th>{{ $t(__('crm::quote.fields.description')) }}</th>
            <th class="text-end">{{ $t(__('crm::quote.fields.quantity')) }}</th>
            <th class="text-end">{{ $t(__('crm::quote.fields.unit_price')) }}</th>
            <th class="text-end">{{ $t(__('crm::quote.fields.discount')) }}</th>
            <th class="text-end">{{ $t(__('crm::quote.fields.tax')) }}</th>
            <th class="text-end">{{ $t(__('crm::quote.fields.amount')) }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($quote->lines as $line)
            <tr>
                <td>{{ $t($line->description) }}</td>
                <td class="text-end">{{ $line->quantity }}</td>
                <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                <td class="text-end">{{ number_format($line->discount_amount, 2) }}</td>
                <td class="text-end">{{ number_format($line->tax_amount, 2) }}</td>
                <td class="text-end">{{ number_format($line->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="totals">
        <tr>
            <td colspan="5" class="text-end">{{ $t(__('crm::quote.fields.subtotal')) }}</td>
            <td class="text-end">{{ number_format($quote->subtotal, 2) }} {{ $quote->currency }}</td>
        </tr>
        <tr>
            <td colspan="5" class="text-end">{{ $t(__('crm::quote.fields.discount')) }}</td>
            <td class="text-end">{{ number_format($quote->discount_amount, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="text-end">{{ $t(__('crm::quote.fields.tax')) }}</td>
            <td class="text-end">{{ number_format($quote->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="text-end grand-total">{{ $t(__('crm::quote.fields.total')) }}</td>
            <td class="text-end grand-total">{{ number_format($quote->total, 2) }} {{ $quote->currency }}</td>
        </tr>
        </tfoot>
    </table>

    @if($quote->terms)
        <div class="terms">
            <strong>{{ $t(__('crm::quote.pdf.terms')) }}</strong><br>
            {{ $t($quote->terms) }}
        </div>
    @endif

    @if($quote->notes)
        <p style="margin-top: 16px; color: #64748b;">{{ $t($quote->notes) }}</p>
    @endif

    <div class="quote-bottom">
        <div class="footer">{{ $t(__('crm::quote.pdf.thank_you')) }}</div>

        @if($companyBranding['sign_path'])
            <div class="sign-block">
                <div class="sign-label">{{ $t(__('crm::quote.pdf.company_sign')) }}</div>
                <img src="{{ $companyBranding['sign_path'] }}" class="sign-image" alt="{{ $companyBranding['name'] }}">
            </div>
        @endif
    </div>
</body>
</html>
