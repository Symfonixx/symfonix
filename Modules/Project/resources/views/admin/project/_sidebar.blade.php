@php
    $paymentColor = match($collectionSummary['payment_status'] ?? $project->payment_status) {
        'fully_paid' => 'success',
        'partially_paid' => 'warning',
        default => 'danger',
    };
@endphp

@if(isset($collectionSummary))
    <div class="card mb-5">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('project::project.sections.collection') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="d-flex align-items-center justify-content-between mb-5">
                <span class="text-muted">{{ __('project::project.fields.payment_status') }}</span>
                <span class="badge badge-light-{{ $paymentColor }}">
                    {{ __('project::project.payment_status.'.$collectionSummary['payment_status']) }}
                </span>
            </div>

            <div class="mb-6">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span class="text-muted fs-7">{{ __('project::project.fields.collection_rate') }}</span>
                    <span class="fw-bold fs-3">{{ $collectionSummary['collection_rate'] }}%</span>
                </div>
                <div class="progress h-8px">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: {{ min(100, max(0, $collectionSummary['collection_rate'])) }}%"
                         aria-valuenow="{{ $collectionSummary['collection_rate'] }}"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <span class="text-muted">{{ __('project::project.fields.budget') }}</span>
                <span class="fw-bold">
                    {{ number_format($collectionSummary['budget'], 2) }} {{ $collectionSummary['currency'] }}
                </span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <span class="text-muted">{{ __('project::project.fields.invoiced') }}</span>
                <span class="fw-bold">
                    {{ number_format($collectionSummary['invoiced'], 2) }} {{ $collectionSummary['currency'] }}
                </span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">{{ __('project::project.fields.remaining') }}</span>
                <span class="fw-bold {{ $collectionSummary['remaining'] <= 0 ? 'text-success' : 'text-primary' }}">
                    {{ number_format($collectionSummary['remaining'], 2) }} {{ $collectionSummary['currency'] }}
                </span>
            </div>
        </div>
    </div>
@endif

<div class="card mb-5">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold">{{ __('project::project.sections.profit_and_loss') }}</h3>
    </div>
    <div class="card-body pt-0">
        <p class="text-muted fs-8 mb-5">{{ __('project::project.hints.profit_and_loss') }}</p>

        <div class="d-flex justify-content-between mb-4">
            <span class="text-muted">{{ __('project::project.fields.invoiced') }}</span>
            <span class="fw-bold">{{ number_format($profitAndLoss['invoiced'], 2) }} {{ $profitAndLoss['currency'] }}</span>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <span class="text-muted">{{ __('project::project.fields.labor_cost') }}</span>
            <span class="fw-bold">{{ number_format($profitAndLoss['labor_cost'], 2) }} {{ $profitAndLoss['currency'] }}</span>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <span class="text-muted">{{ __('project::project.fields.project_expenses') }}</span>
            <span class="fw-bold">{{ number_format($profitAndLoss['expenses'], 2) }} {{ $profitAndLoss['currency'] }}</span>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <span class="text-muted">{{ __('project::project.fields.total_cost') }}</span>
            <span class="fw-bold">{{ number_format($profitAndLoss['total_cost'], 2) }} {{ $profitAndLoss['currency'] }}</span>
        </div>

        <div class="separator my-5"></div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">{{ __('project::project.fields.profit_or_loss') }}</span>
            <span class="fw-bold fs-4 {{ $profitAndLoss['is_profit'] ? 'text-success' : ($profitAndLoss['is_loss'] ? 'text-danger' : '') }}">
                {{ number_format($profitAndLoss['profit_or_loss'], 2) }} {{ $profitAndLoss['currency'] }}
            </span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted">{{ __('project::project.fields.margin_rate') }}</span>
            <span class="badge badge-light-{{ $profitAndLoss['is_profit'] ? 'success' : ($profitAndLoss['is_loss'] ? 'danger' : 'secondary') }} fs-7">
                {{ $profitAndLoss['margin_rate'] }}%
            </span>
        </div>
    </div>
</div>

@php
    $canUpdateProject = auth()->user()?->can('update', $project);
    $canManageFinance = auth()->user()?->can('finance.invoices.create');
    $canLogExpense = auth()->user()?->can('project.projects.edit');
@endphp
@if($canUpdateProject || $canManageFinance || $canLogExpense)
    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('project::project.sections.quick_actions') }}</h3>
        </div>
        <div class="card-body pt-0 d-grid gap-3">
            @if($canUpdateProject)
                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#assignEmployeeModal">
                    <i class="bi bi-person-plus me-2"></i>{{ __('project::project.actions.assign_employee') }}
                </button>
            @endif
            @if($canLogExpense)
                <button type="button" class="btn btn-light-danger" data-bs-toggle="modal" data-bs-target="#logExpenseModal">
                    <i class="bi bi-cash-stack me-2"></i>{{ __('project::project.actions.log_expense') }}
                </button>
            @endif
            @if($canManageFinance && isset($collectionSummary) && $collectionSummary['remaining'] > 0)
                <button type="button" class="btn btn-light-success" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                    <i class="bi bi-receipt me-2"></i>{{ __('project::project.actions.add_invoice') }}
                </button>
            @endif
            @if($canUpdateProject)
                <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-light">
                    <i class="bi bi-pencil me-2"></i>{{ __('Edit') }}
                </a>
            @endif
        </div>
    </div>
@endif
