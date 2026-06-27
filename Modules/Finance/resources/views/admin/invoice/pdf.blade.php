<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        .header { margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; color: #0f172a; }
        .meta { margin-top: 8px; color: #64748b; }
        .section-title { font-size: 11px; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }
        .bill-to { margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #f8fafc; text-align: left; padding: 10px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .text-end { text-align: right; }
        .totals td { border: none; }
        .grand-total { font-size: 16px; font-weight: bold; }
        .footer { margin-top: 40px; text-align: center; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ __('finance::invoice.pdf.title') }} {{ $invoice->invoice_number }}</div>
        <div class="meta">
            {{ __('finance::invoice.pdf.issue_date') }}: {{ $invoice->issued_at?->format('Y-m-d') }}
            &nbsp;|&nbsp;
            {{ __('finance::invoice.pdf.due_date') }}: {{ $invoice->due_at?->format('Y-m-d') }}
        </div>
    </div>

    <div class="bill-to">
        <div class="section-title">{{ __('finance::invoice.pdf.bill_to') }}</div>
        <strong>{{ $invoice->company?->name }}</strong><br>
        @if($invoice->company?->email){{ $invoice->company->email }}<br>@endif
        @if($invoice->company?->address){{ $invoice->company->address }}<br>@endif
        @if($invoice->company?->city){{ $invoice->company->city }}@if($invoice->company?->country), {{ $invoice->company->country }}@endif<br>@endif
    </div>

    <table>
        <thead>
        <tr>
            <th>{{ __('finance::invoice.fields.description') }}</th>
            <th class="text-end">{{ __('finance::invoice.fields.quantity') }}</th>
            <th class="text-end">{{ __('finance::invoice.fields.unit_price') }}</th>
            <th class="text-end">{{ __('finance::invoice.fields.amount') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->lines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td class="text-end">{{ $line->quantity }}</td>
                <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                <td class="text-end">{{ number_format($line->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="totals">
        <tr>
            <td colspan="3" class="text-end">{{ __('finance::invoice.fields.subtotal') }}</td>
            <td class="text-end">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
        </tr>
        @if($invoice->tax_amount > 0)
            <tr>
                <td colspan="3" class="text-end">{{ __('finance::invoice.fields.tax') }}</td>
                <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="3" class="text-end grand-total">{{ __('finance::invoice.fields.total') }}</td>
            <td class="text-end grand-total">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
        </tr>
        </tfoot>
    </table>

    @if($invoice->notes)
        <p style="margin-top: 24px; color: #64748b;">{{ $invoice->notes }}</p>
    @endif

    <div class="footer">{{ __('finance::invoice.pdf.thank_you') }}</div>
</body>
</html>
