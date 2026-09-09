@section('title', __('crm::quote.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::quote.pages.index_title'), 'url' => route('admin.quotes.index')],
            ['label' => $quote->quote_number],
        ];
        $statusColor = match($quote->status) {
            'accepted' => 'success',
            'rejected', 'void', 'expired' => 'danger',
            'sent' => 'primary',
            default => 'warning',
        };
    @endphp
    <x-admin.breadcrumb :pageTitle="$quote->quote_number" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.quotes.index') }}" class="btn btn-sm btn-light-primary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::quote.actions.back_to_list') }}
        </a>
        <a href="{{ route('admin.quotes.pdf', $quote) }}" class="btn btn-sm btn-light-primary">
            <i class="bi bi-file-pdf me-1"></i>{{ __('crm::quote.actions.download_pdf') }}
        </a>
        @if(in_array($quote->status, ['draft', 'sent'], true))
            <a href="{{ route('admin.quotes.edit', $quote) }}" class="btn btn-sm btn-light">
                <i class="bi bi-pencil me-1"></i>{{ __('crm::quote.actions.edit') }}
            </a>
        @endif
        @if($quote->status === 'draft')
            <form method="POST" action="{{ route('admin.quotes.sent', $quote) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-send me-1"></i>{{ __('crm::quote.actions.mark_sent') }}
                </button>
            </form>
        @endif
        @if(!in_array($quote->status, ['accepted', 'void'], true))
            <form method="POST" action="{{ route('admin.quotes.void', $quote) }}"
                  data-confirm="{{ __('crm::quote.messages.confirm_void') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-light-danger">
                    <i class="bi bi-slash-circle me-1"></i>{{ __('crm::quote.actions.void') }}
                </button>
            </form>
        @endif
        @if($quote->status !== 'accepted')
            <form method="POST" action="{{ route('admin.quotes.destroy', $quote) }}"
                  data-confirm="{{ __('crm::quote.messages.confirm_delete') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash me-1"></i>{{ __('crm::quote.actions.delete') }}
                </button>
            </form>
        @endif
    </div>
@endsection

<x-admin-layout>
    <div class="card sx-show-hero mb-8">
        <div class="card-body p-6 p-lg-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-5">
                <div class="d-flex align-items-center gap-4">
                    <span class="sx-avatar"><i class="bi bi-file-earmark-text"></i></span>
                    <div>
                        <h2 class="text-white fw-bold mb-2">{{ $quote->quote_number }}</h2>
                        <span class="badge badge-light-{{ $statusColor }}">{{ __('crm::quote.status.'.$quote->status) }}</span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-white opacity-75 fs-7 mb-1">{{ __('crm::quote.fields.total') }}</div>
                    <div class="text-white fw-bold fs-2x">{{ number_format($quote->total, 2) }} {{ $quote->currency }}</div>
                </div>
            </div>
        </div>
    </div>
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
                            <h2 class="fw-bold mb-1">{{ $quote->quote_number }}</h2>
                            <span class="badge badge-light-{{ $statusColor }}">
                                {{ __('crm::quote.status.'.$quote->status) }}
                            </span>
                            <div class="fs-2hx fw-bold mt-3">{{ number_format($quote->total, 2) }} {{ $quote->currency }}</div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="text-muted fs-7 text-uppercase">{{ __('crm::quote.pdf.quote_to') }}</div>
                            <div class="fw-semibold">
                                <a href="{{ route('admin.companies.show', $quote->company) }}">{{ $quote->company?->name }}</a>
                            </div>
                            @if($quote->deal)
                                <div class="text-muted fs-7">
                                    {{ __('crm::quote.fields.deal') }}:
                                    <a href="{{ route('admin.deals.show', $quote->deal) }}">{{ $quote->deal->title }}</a>
                                </div>
                            @endif
                            @if($quote->project)
                                <div class="text-muted fs-7">
                                    {{ __('crm::quote.fields.project') }}:
                                    <a href="{{ route('admin.projects.edit', $quote->project) }}">{{ $quote->project->title }}</a>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div><span class="text-muted">{{ __('crm::quote.fields.issued_at') }}:</span> {{ $quote->issued_at?->format('Y-m-d') }}</div>
                            <div><span class="text-muted">{{ __('crm::quote.fields.expires_at') }}:</span> {{ $quote->expires_at?->format('Y-m-d') ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-row-bordered">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
                                <th>{{ __('crm::quote.fields.description') }}</th>
                                <th>{{ __('crm::quote.fields.item_type') }}</th>
                                <th class="text-end">{{ __('crm::quote.fields.quantity') }}</th>
                                <th class="text-end">{{ __('crm::quote.fields.unit_price') }}</th>
                                <th class="text-end">{{ __('crm::quote.fields.discount') }}</th>
                                <th class="text-end">{{ __('crm::quote.fields.tax') }}</th>
                                <th class="text-end">{{ __('crm::quote.fields.amount') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($quote->lines as $line)
                                <tr>
                                    <td>{{ $line->description }}</td>
                                    <td>{{ __('crm::quote.fields.'.$line->item_type) }}</td>
                                    <td class="text-end">{{ $line->quantity }}</td>
                                    <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($line->discount_percent, 2) }}% ({{ number_format($line->discount_amount, 2) }})</td>
                                    <td class="text-end">{{ number_format($line->tax_percent, 2) }}% ({{ number_format($line->tax_amount, 2) }})</td>
                                    <td class="text-end">{{ number_format($line->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6" class="text-end">{{ __('crm::quote.fields.subtotal') }}</td>
                                <td class="text-end">{{ number_format($quote->subtotal, 2) }} {{ $quote->currency }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">{{ __('crm::quote.fields.discount') }}</td>
                                <td class="text-end">{{ number_format($quote->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">{{ __('crm::quote.fields.tax') }}</td>
                                <td class="text-end">{{ number_format($quote->tax_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">{{ __('crm::quote.fields.total') }}</td>
                                <td class="text-end fw-bold">{{ number_format($quote->total, 2) }} {{ $quote->currency }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($quote->terms)
                        <div class="mt-8">
                            <h5 class="fw-bold">{{ __('crm::quote.fields.terms') }}</h5>
                            <p class="text-muted mb-0" style="white-space: pre-wrap;">{{ $quote->terms }}</p>
                        </div>
                    @endif

                    @if($quote->notes)
                        <div class="mt-6">
                            <h5 class="fw-bold">{{ __('crm::quote.fields.notes') }}</h5>
                            <p class="text-muted mb-0">{{ $quote->notes }}</p>
                        </div>
                    @endif

                    @if($companyBranding['sign_url'])
                        <div class="mt-10 text-center">
                            <div class="text-muted fs-8 text-uppercase mb-2">{{ __('crm::quote.pdf.company_sign') }}</div>
                            <img src="{{ $companyBranding['sign_url'] }}" alt="{{ $companyBranding['name'] }}" style="max-height: 80px; max-width: 200px;">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            @if(in_array($quote->status, ['draft', 'sent'], true) || $quote->status === 'accepted')
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('crm::quote.fields.public_link') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-solid" id="quote-public-link"
                                   value="{{ $quote->publicUrl() }}" readonly>
                            <button type="button" class="btn btn-light-primary" id="copy-quote-link">
                                {{ __('crm::quote.actions.copy_link') }}
                            </button>
                        </div>
                        @if($quote->status === 'draft')
                            <p class="text-muted fs-7 mb-0">{{ __('crm::quote.messages.sent') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if($quote->responded_at)
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('crm::quote.sections.approval') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted fs-7">{{ __('crm::quote.fields.responder_name') }}</div>
                            <div class="fw-semibold">{{ $quote->responder_name }}</div>
                        </div>
                        @if($quote->responder_email)
                            <div class="mb-3">
                                <div class="text-muted fs-7">{{ __('crm::quote.fields.responder_email') }}</div>
                                <div class="fw-semibold">{{ $quote->responder_email }}</div>
                            </div>
                        @endif
                        <div class="mb-3">
                            <div class="text-muted fs-7">{{ __('crm::quote.fields.responded_at') }}</div>
                            <div class="fw-semibold">{{ $quote->responded_at->format('Y-m-d H:i') }}</div>
                        </div>
                        @if($quote->response_note)
                            <div>
                                <div class="text-muted fs-7">{{ __('crm::quote.fields.response_note') }}</div>
                                <div class="fw-semibold">{{ $quote->response_note }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('copy-quote-link')?.addEventListener('click', async function () {
                const input = document.getElementById('quote-public-link');
                try {
                    await navigator.clipboard.writeText(input.value);
                    alert(@json(__('crm::quote.messages.link_copied')));
                } catch (e) {
                    input.select();
                    document.execCommand('copy');
                    alert(@json(__('crm::quote.messages.link_copied')));
                }
            });
        </script>
    @endpush
</x-admin-layout>
