@section('title', __('crm::deal.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::deal.menu.deals'), 'url' => route('admin.deals.index')],
            ['label' => __('crm::deal.pages.show_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::deal.pages.show_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.deals.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::deal.actions.back_to_list') }}
        </a>
        <a class="btn btn-sm fw-bold btn-light" href="{{ route('admin.deals.index', ['view' => 'kanban']) }}">
            <i class="bi bi-kanban me-1"></i>{{ __('crm::deal.menu.pipeline') }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.deals.edit', $deal->id) }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    @php
        $statusColor = match($deal->status) {
            'won' => 'success',
            'lost' => 'danger',
            default => 'primary',
        };
    @endphp
    <div class="card sx-show-hero mb-8">
        <div class="card-body p-6 p-lg-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-5">
                <div class="d-flex align-items-center gap-4">
                    <span class="sx-avatar"><i class="bi bi-briefcase"></i></span>
                    <div>
                        <h2 class="text-white fw-bold mb-2">{{ $deal->title }}</h2>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge badge-light-{{ $deal->pipelineStage?->color ?? 'primary' }}">
                                {{ $deal->pipelineStage?->display_name ?: __('N/A') }}
                            </span>
                            <span class="badge badge-light-{{ $statusColor }}">
                                {{ __('crm::deal.status.'.$deal->status) }}
                            </span>
                            @if($deal->company)
                                <span class="text-white opacity-75 fs-7">
                                    <i class="bi bi-building me-1"></i>{{ $deal->company->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-white opacity-75 fs-7 mb-1">{{ __('crm::deal.fields.value') }}</div>
                    <div class="text-white fw-bold fs-2x">
                        @if($deal->value)
                            {{ number_format($deal->value, 0) }} {{ $deal->currency }}
                        @else
                            {{ __('N/A') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-10">
        <div class="col-xl-8">
            <div class="card mb-5">
                <div class="card-header">
                    <h3 class="card-title">{{ __('crm::deal.sections.basic_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.title') }}</div>
                        <div class="col-md-9">{{ $deal->title }}</div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.company') }}</div>
                        <div class="col-md-9">{{ $deal->company?->name ?: __('N/A') }}</div>
                    </div>
                    @if($deal->lead)
                        <div class="row mb-6">
                            <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.source_lead') }}</div>
                            <div class="col-md-9">
                                <a href="{{ route('admin.leads.show', $deal->lead) }}" class="text-hover-primary">
                                    {{ $deal->lead->name ?? $deal->lead->email ?? __('crm::deal.fields.source_lead') }}
                                </a>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-md-3 fw-bold">{{ __('crm::lead.fields.tags') }}</div>
                            <div class="col-md-9">
                                @include('crm::admin.partials.lead-tags', [
                                    'tags' => $deal->lead->tags,
                                    'solid' => true,
                                    'class' => 'me-2 mb-2 px-3 py-2',
                                    'empty' => __('crm::lead.hints.no_tags_assigned'),
                                ])
                            </div>
                        </div>
                    @endif
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.stage') }}</div>
                        <div class="col-md-9">
                            <span class="badge badge-light-{{ $deal->pipelineStage?->color ?? 'primary' }}">
                                {{ $deal->pipelineStage?->display_name ?: __('N/A') }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.assignee') }}</div>
                        <div class="col-md-9">{{ $deal->assignee?->name ?: __('N/A') }}</div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.value') }}</div>
                        <div class="col-md-9">
                            @if($deal->value)
                                {{ number_format($deal->value, 2) }} {{ $deal->currency }}
                            @else
                                {{ __('N/A') }}
                            @endif
                        </div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.probability') }}</div>
                        <div class="col-md-9">{{ $deal->probability !== null ? $deal->probability.'%' : __('N/A') }}</div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.expected_close_date') }}</div>
                        <div class="col-md-9">{{ $deal->expected_close_date?->format('Y-m-d') ?: __('N/A') }}</div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.source') }}</div>
                        <div class="col-md-9">{{ $deal->source ?: __('N/A') }}</div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.description') }}</div>
                        <div class="col-md-9">{{ $deal->description ?: __('N/A') }}</div>
                    </div>
                    @if($deal->lost_reason)
                        <div class="row mb-6">
                            <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.lost_reason') }}</div>
                            <div class="col-md-9">{{ $deal->lost_reason }}</div>
                        </div>
                    @endif
                    <div class="row mb-0">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.status') }}</div>
                        <div class="col-md-9">
                            @php
                                $statusColor = match($deal->status) {
                                    'won' => 'success',
                                    'lost' => 'danger',
                                    default => 'primary',
                                };
                            @endphp
                            <span class="badge badge-light-{{ $statusColor }}">
                                {{ __('crm::deal.status.'.$deal->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($deal->services->isNotEmpty())
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('crm::deal.sections.services') }}</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered align-middle gy-4 mb-0">
                                <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th>{{ __('crm::deal.fields.service') }}</th>
                                    <th>{{ __('crm::deal.fields.quantity') }}</th>
                                    <th>{{ __('crm::deal.fields.unit_price') }}</th>
                                    <th>{{ __('crm::deal.fields.line_total') }}</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                @foreach($deal->services as $service)
                                    <tr>
                                        <td>{{ $service->getTranslation('title', app()->getLocale()) }}</td>
                                        <td>{{ $service->pivot->quantity }}</td>
                                        <td>{{ number_format($service->pivot->unit_price, 2) }} {{ $deal->currency }}</td>
                                        <td>{{ number_format($service->pivot->quantity * $service->pivot->unit_price, 2) }} {{ $deal->currency }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ __('crm::quote.sections.related_quotes') }}</h3>
                    @if($deal->company_id && $deal->services->isNotEmpty())
                        <form method="POST" action="{{ route('admin.quotes.from-deal', $deal) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light-primary">
                                <i class="bi bi-file-earmark-text me-1"></i>{{ __('crm::quote.actions.from_deal') }}
                            </button>
                        </form>
                    @elseif($deal->company_id)
                        <a href="{{ route('admin.quotes.create', ['deal_id' => $deal->id, 'company_id' => $deal->company_id]) }}"
                           class="btn btn-sm btn-light-primary">
                            <i class="bi bi-file-earmark-text me-1"></i>{{ __('crm::quote.actions.from_deal') }}
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($deal->quotes->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-row-bordered align-middle gy-4 mb-0">
                                <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th>{{ __('crm::quote.fields.quote_number') }}</th>
                                    <th>{{ __('crm::quote.fields.status') }}</th>
                                    <th>{{ __('crm::quote.fields.total') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                @foreach($deal->quotes as $quote)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.quotes.show', $quote) }}" class="text-hover-primary fw-bold">
                                                {{ $quote->quote_number }}
                                            </a>
                                        </td>
                                        <td>{{ __('crm::quote.status.'.$quote->status) }}</td>
                                        <td>{{ number_format($quote->total, 2) }} {{ $quote->currency }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.quotes.pdf', $quote) }}" class="btn btn-sm btn-light">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-muted">{{ __('No records found') }}</div>
                    @endif
                </div>
            </div>

            @if($deal->project)
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('crm::deal.sections.linked_project') }}</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.projects.edit', $deal->project) }}" class="text-hover-primary fw-bold">
                            {{ $deal->project->title }}
                        </a>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('crm::deal.sections.stage_history') }}</h3>
                </div>
                <div class="card-body">
                    @forelse($deal->stageHistories as $history)
                        <div class="d-flex align-items-start mb-6">
                            <div class="symbol symbol-35px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="bi bi-arrow-right-circle text-primary"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-gray-900">
                                    @if($history->fromStage)
                                        {{ __('crm::deal.history.moved_from', [
                                            'from' => $history->fromStage->name,
                                            'to' => $history->toStage->name,
                                        ]) }}
                                    @else
                                        {{ $history->notes ?: __('crm::deal.history.created') }}
                                        — {{ $history->toStage->name }}
                                    @endif
                                </div>
                                <div class="text-muted fs-7">
                                    {{ $history->created_at->diffForHumans() }}
                                    @if($history->changedBy)
                                        · {{ __('crm::deal.history.by') }} {{ $history->changedBy->name }}
                                    @endif
                                </div>
                                @if($history->notes && $history->fromStage)
                                    <div class="text-gray-600 fs-7 mt-1">{{ $history->notes }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('crm::deal.history.no_history') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            @if($ledgerSummary)
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('crm::deal.sections.ledger_reconciliation') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">{{ __('crm::deal.ledger.expected_revenue') }}</span>
                            <span class="fw-bold">
                                {{ number_format($ledgerSummary['expected_revenue'], 2) }} {{ $ledgerSummary['currency'] }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">{{ __('crm::deal.ledger.recorded_in_ledger') }}</span>
                            <span class="fw-bold">
                                {{ number_format($ledgerSummary['ledger_total'], 2) }} {{ $ledgerSummary['currency'] }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">{{ __('crm::deal.ledger.variance') }}</span>
                            <span class="fw-bold {{ $ledgerSummary['is_reconciled'] ? 'text-success' : 'text-danger' }}">
                                {{ number_format($ledgerSummary['variance'], 2) }} {{ $ledgerSummary['currency'] }}
                            </span>
                        </div>
                        <div class="mb-5">
                            @if($ledgerSummary['is_reconciled'])
                                <span class="badge badge-light-success">{{ __('crm::deal.ledger.reconciled') }}</span>
                            @else
                                <span class="badge badge-light-danger">{{ __('crm::deal.ledger.not_reconciled') }}</span>
                            @endif
                        </div>

                        @can('finance.invoices.create')
                            @if($deal->status === 'won' && $deal->company_id)
                                <form method="POST" action="{{ route('admin.finance.invoices.from-deal', $deal) }}" class="mb-5">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light-primary w-100">
                                        <i class="bi bi-receipt me-1"></i>{{ __('finance::invoice.actions.from_deal') }}
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @if($ledgerSummary['transactions']->isNotEmpty())
                            <div class="separator my-5"></div>
                            <h5 class="fw-bold mb-4">{{ __('crm::deal.ledger.transactions') }}</h5>
                            @foreach($ledgerSummary['transactions'] as $transaction)
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="pe-3">
                                        <div class="fw-semibold fs-7">{{ $transaction->description ?: __('crm::deal.ledger.income_entry') }}</div>
                                        <div class="text-muted fs-8">{{ $transaction->transaction_date?->format('Y-m-d') }}</div>
                                    </div>
                                    <span class="fw-bold text-success text-nowrap">
                                        {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0 fs-7">{{ __('crm::deal.ledger.no_transactions') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('crm::deal.sections.move_stage') }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.deals.moveStage', $deal->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-5">
                            <label class="form-label required">{{ __('crm::deal.fields.stage') }}</label>
                            <select name="pipeline_stage_id" class="form-select form-select-solid" required>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}" @selected($stage->id === $deal->pipeline_stage_id)>
                                        {{ $stage->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label">{{ __('crm::deal.fields.notes') }}</label>
                            <textarea name="notes" class="form-control form-control-solid" rows="3"
                                      placeholder="{{ __('crm::deal.placeholders.notes') }}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('crm::deal.actions.move_stage') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('crm::admin.partials.timeline', ['subject' => $deal, 'subjectType' => 'deal'])
</x-admin-layout>
