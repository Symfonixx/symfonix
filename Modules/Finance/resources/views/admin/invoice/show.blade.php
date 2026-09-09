@section('title', __('finance::invoice.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::invoice.pages.index_title'), 'url' => route('admin.finance.invoices.index')],
            ['label' => $invoice->invoice_number],
        ];
        $statusColor = match($invoice->status) {
            'paid' => 'success',
            'overdue' => 'danger',
            'sent' => 'primary',
            'void' => 'secondary',
            default => 'warning',
        };
    @endphp
    <x-admin.breadcrumb :pageTitle="$invoice->invoice_number" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.finance.invoices.index') }}" class="btn btn-sm btn-light-primary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to List') }}
        </a>
        <a href="{{ route('admin.finance.invoices.pdf', $invoice) }}" class="btn btn-sm btn-light-primary">
            <i class="bi bi-file-pdf me-1"></i>{{ __('finance::invoice.actions.download_pdf') }}
        </a>
        @if($invoice->status === 'draft')
            <form method="POST" action="{{ route('admin.finance.invoices.sent', $invoice) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">{{ __('finance::invoice.actions.mark_sent') }}</button>
            </form>
        @endif
        @if(in_array($invoice->status, ['sent', 'overdue']))
            <form method="POST" action="{{ route('admin.finance.invoices.paid', $invoice) }}"
                  data-confirm="{{ __('Are you sure?') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    {{ __('finance::invoice.actions.mark_paid') }}
                </button>
            </form>
        @endif
        @if(!in_array($invoice->status, ['paid', 'void']))
            <form method="POST" action="{{ route('admin.finance.invoices.void', $invoice) }}"
                  data-confirm="{{ __('Are you sure?') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-light-danger">
                    {{ __('finance::invoice.actions.void') }}
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('admin.finance.invoices.destroy', $invoice) }}"
              data-confirm="{{ __('finance::invoice.messages.confirm_delete') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">
                {{ __('finance::invoice.actions.delete') }}
            </button>
        </form>
    </div>
@endsection

<x-admin-layout>
    <div class="row g-5">
        <div class="col-xl-8">
            <div class="card mb-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-6">
                        <div>
                            @if($companyBranding['logo_url'])
                                <img src="{{ $companyBranding['logo_url'] }}" alt="{{ $companyBranding['name'] }}" class="mb-3" style="max-height: 50px; max-width: 200px;">
                            @else
                                <div class="fw-bold fs-4 mb-2">{{ $companyBranding['name'] }}</div>
                            @endif
                            <div class="text-muted fs-7">
                                @if($companyBranding['phone'])
                                    <div><i class="bi bi-telephone me-1"></i>{{ $companyBranding['phone'] }}</div>
                                @endif
                                @if($companyBranding['email'])
                                    <div><i class="bi bi-envelope me-1"></i>{{ $companyBranding['email'] }}</div>
                                @endif
                                @if($companyBranding['address'])
                                    <div><i class="bi bi-geo-alt me-1"></i>{{ $companyBranding['address'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <h2 class="fw-bold mb-1">{{ $invoice->invoice_number }}</h2>
                            <span class="badge badge-light-{{ $statusColor }}">
                                {{ __('finance::invoice.status.'.$invoice->status) }}
                            </span>
                            <div class="fs-2hx fw-bold mt-3">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="text-muted fs-7 text-uppercase">{{ __('finance::invoice.pdf.bill_to') }}</div>
                            <div class="fw-semibold">{{ $invoice->company?->name }}</div>
                            @if($invoice->company?->email)
                                <div class="text-muted fs-7">{{ $invoice->company->email }}</div>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div><span class="text-muted">{{ __('finance::invoice.fields.issued_at') }}:</span> {{ $invoice->issued_at?->format('Y-m-d') }}</div>
                            <div><span class="text-muted">{{ __('finance::invoice.fields.due_at') }}:</span> {{ $invoice->due_at?->format('Y-m-d') }}</div>
                            @if($invoice->paid_at)
                                <div><span class="text-muted">{{ __('finance::invoice.fields.paid_at') }}:</span> {{ $invoice->paid_at->format('Y-m-d') }}</div>
                            @endif
                        </div>
                    </div>
                    @if($invoice->notes)
                        <p class="text-muted mb-6">{{ $invoice->notes }}</p>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-row-bordered">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
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
                                    <td class="text-end fw-bold">{{ number_format($line->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-end text-muted">{{ __('finance::invoice.fields.subtotal') }}</td>
                                <td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            @if($invoice->tax_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end text-muted">{{ __('finance::invoice.fields.tax') }}</td>
                                    <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-end fw-bold">{{ __('finance::invoice.fields.total') }}</td>
                                <td class="text-end fw-bold fs-5">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="border-top pt-8 mt-8">
                        <p class="text-center text-muted mb-0">{{ __('finance::invoice.pdf.thank_you') }}</p>

                        @if($companyBranding['sign_url'])
                            <div class="text-center mt-8">
                                <div class="text-muted fs-8 text-uppercase mb-2">{{ __('finance::invoice.pdf.company_sign') }}</div>
                                <img src="{{ $companyBranding['sign_url'] }}" alt="{{ $companyBranding['name'] }}" style="max-height: 80px; max-width: 200px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('Details') }}</h3></div>
                <div class="card-body">
                    @if($invoice->subscription)
                        <div class="mb-4">
                            <div class="text-muted fs-7">{{ __('finance::invoice.fields.subscription') }}</div>
                            <a href="{{ route('admin.subscriptions.show', $invoice->subscription_id) }}">{{ $invoice->subscription->name }}</a>
                        </div>
                    @endif
                    @if($invoice->deal)
                        <div class="mb-4">
                            <div class="text-muted fs-7">{{ __('finance::invoice.fields.deal') }}</div>
                            <a href="{{ route('admin.deals.show', $invoice->deal_id) }}">{{ $invoice->deal->title }}</a>
                        </div>
                    @endif
                    @if($invoice->project)
                        <div class="mb-4">
                            <div class="text-muted fs-7">{{ __('finance::invoice.fields.project') }}</div>
                            <a href="{{ route('admin.projects.show', $invoice->project_id) }}">{{ $invoice->project->title }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
