@php
    use Modules\Finance\Support\DomPdfText;

    $isRtl = DomPdfText::isRtl();
    $t = static fn (?string $text): string => DomPdfText::shape($text);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
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
            padding: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .totals td { border: none; }
        .grand-total { font-size: 16px; font-weight: bold; }
        .footer { margin-top: 24px; text-align: center; color: #64748b; }
        .invoice-bottom { margin-top: 48px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
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
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            @if($isRtl)
                <td class="text-start">
                    <div class="title">{{ $t(__('finance::invoice.pdf.title')) }} {{ $invoice->invoice_number }}</div>
                    <div class="meta">
                        {{ $t(__('finance::invoice.pdf.issue_date')) }}: {{ $invoice->issued_at?->format('Y-m-d') }}
                        &nbsp;|&nbsp;
                        {{ $t(__('finance::invoice.pdf.due_date')) }}: {{ $invoice->due_at?->format('Y-m-d') }}
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
                    <div class="title">{{ __('finance::invoice.pdf.title') }} {{ $invoice->invoice_number }}</div>
                    <div class="meta">
                        {{ __('finance::invoice.pdf.issue_date') }}: {{ $invoice->issued_at?->format('Y-m-d') }}
                        &nbsp;|&nbsp;
                        {{ __('finance::invoice.pdf.due_date') }}: {{ $invoice->due_at?->format('Y-m-d') }}
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <div class="bill-to">
        <div class="section-title">{{ $t(__('finance::invoice.pdf.bill_to')) }}</div>
        <strong>{{ $t($invoice->company?->name) }}</strong><br>
        @if($invoice->company?->email){{ $invoice->company->email }}<br>@endif
        @if($invoice->company?->address){{ $t($invoice->company->address) }}<br>@endif
        @if($invoice->company?->city){{ $t($invoice->company->city) }}@if($invoice->company?->country), {{ $t($invoice->company->country) }}@endif<br>@endif
    </div>

    <table>
        <thead>
        <tr>
            <th>{{ $t(__('finance::invoice.fields.description')) }}</th>
            <th class="text-end">{{ $t(__('finance::invoice.fields.quantity')) }}</th>
            <th class="text-end">{{ $t(__('finance::invoice.fields.unit_price')) }}</th>
            <th class="text-end">{{ $t(__('finance::invoice.fields.amount')) }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->lines as $line)
            <tr>
                <td>{{ $t($line->description) }}</td>
                <td class="text-end">{{ $line->quantity }}</td>
                <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                <td class="text-end">{{ number_format($line->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="totals">
        <tr>
            <td colspan="3" class="text-end">{{ $t(__('finance::invoice.fields.subtotal')) }}</td>
            <td class="text-end">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
        </tr>
        @if($invoice->tax_amount > 0)
            <tr>
                <td colspan="3" class="text-end">{{ $t(__('finance::invoice.fields.tax')) }}</td>
                <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="3" class="text-end grand-total">{{ $t(__('finance::invoice.fields.total')) }}</td>
            <td class="text-end grand-total">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
        </tr>
        </tfoot>
    </table>

    @if($invoice->notes)
        <p style="margin-top: 24px; color: #64748b;">{{ $t($invoice->notes) }}</p>
    @endif

    <div class="invoice-bottom">
        <div class="footer">{{ $t(__('finance::invoice.pdf.thank_you')) }}</div>

        @if($companyBranding['sign_path'])
            <div class="sign-block">
                <div class="sign-label">{{ $t(__('finance::invoice.pdf.company_sign')) }}</div>
                <img src="{{ $companyBranding['sign_path'] }}" class="sign-image" alt="{{ $companyBranding['name'] }}">
            </div>
        @endif
    </div>
</body>
</html>
