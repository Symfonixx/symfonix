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
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.deals.edit', $deal->id) }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
    </div>
@endsection

<x-admin-layout>
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
                    @endif
                    <div class="row mb-6">
                        <div class="col-md-3 fw-bold">{{ __('crm::deal.fields.stage') }}</div>
                        <div class="col-md-9">
                            <span class="badge badge-light-{{ $deal->pipelineStage?->color ?? 'primary' }}">
                                {{ $deal->pipelineStage?->name ?: __('N/A') }}
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
                                        {{ $stage->name }}
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
