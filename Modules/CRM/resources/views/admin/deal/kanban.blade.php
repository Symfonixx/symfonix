@section('title', __('crm::deal.pages.kanban_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::deal.menu.deals'), 'url' => route('admin.deals.index')],
            ['label' => __('crm::deal.pages.kanban_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::deal.pages.kanban_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.deals.index') }}">
            <i class="bi bi-list-ul me-1"></i>{{ __('crm::deal.actions.list_view') }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.deals.create') }}">
            {{ __('crm::deal.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="d-flex flex-nowrap gap-5 overflow-auto pb-5" style="min-height: 70vh;">
        @foreach($stages as $stage)
            <div class="flex-shrink-0" style="width: 300px;">
                <div class="card h-100">
                    <div class="card-header border-0 pt-5 pb-3">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">
                                <span class="badge badge-light-{{ $stage->color }} me-2">&nbsp;</span>
                                {{ $stage->name }}
                            </span>
                            <span class="text-muted mt-1 fw-semibold fs-7">
                                {{ __('crm::deal.kanban.deals_count', ['count' => $stage->deals->count()]) }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 d-flex flex-column gap-4">
                        @forelse($stage->deals as $deal)
                            <div class="border border-gray-300 border-dashed rounded p-4 bg-light-{{ $stage->color }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <a href="{{ route('admin.deals.show', $deal->id) }}"
                                       class="fw-bold text-gray-900 text-hover-primary fs-6">
                                        {{ $deal->title }}
                                    </a>
                                    @if($deal->value)
                                        <span class="badge badge-light-success fs-8">
                                            {{ number_format($deal->value, 0) }} {{ $deal->currency }}
                                        </span>
                                    @endif
                                </div>
                                @if($deal->company)
                                    <div class="text-muted fs-7 mb-2">
                                        <i class="bi bi-building me-1"></i>{{ $deal->company->name }}
                                    </div>
                                @endif
                                @if($deal->assignee)
                                    <div class="text-muted fs-7 mb-3">
                                        <i class="bi bi-person me-1"></i>{{ $deal->assignee->name }}
                                    </div>
                                @endif
                                <form method="POST" action="{{ route('admin.deals.moveStage', $deal->id) }}" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="pipeline_stage_id" class="form-select form-select-sm form-select-solid"
                                            onchange="this.form.submit()">
                                        @foreach($stages as $targetStage)
                                            <option value="{{ $targetStage->id }}" @selected($targetStage->id === $deal->pipeline_stage_id)>
                                                → {{ $targetStage->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted fs-7 py-10">
                                {{ __('crm::deal.kanban.empty_column') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>
