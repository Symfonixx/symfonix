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
        <x-can perform="sales.deals.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.deals.create') }}">
                <i class="bi bi-plus-lg me-1"></i>{{ __('crm::deal.actions.add') }}
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    @if(($tags ?? collect())->isNotEmpty())
        <div class="card sx-filter-bar mb-5">
            <div class="card-body py-4">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="sx-form-icon" style="width:2rem;height:2rem;font-size:.9rem;">
                        <i class="bi bi-tags"></i>
                    </span>
                    <span class="text-muted fs-7 fw-semibold me-1">{{ __('crm::lead.fields.tags') }}:</span>
                    <a href="{{ route('admin.deals.index', array_merge(collect($filters ?? [])->except('tag_id')->filter()->all(), ['view' => 'kanban'])) }}"
                       class="badge {{ empty($filters['tag_id']) ? 'badge-primary' : 'badge-light' }} text-decoration-none px-3 py-2">
                        {{ __('crm::lead.filters.all') }}
                    </a>
                    @foreach($tags as $tag)
                        @php
                            $isActiveTag = (int) ($filters['tag_id'] ?? 0) === (int) $tag->id;
                            $chipFilters = collect($filters ?? [])->filter()->all();
                            $chipFilters['view'] = 'kanban';
                            $chipFilters['tag_id'] = $tag->id;
                        @endphp
                        <a href="{{ route('admin.deals.index', $chipFilters) }}"
                           class="badge {{ $isActiveTag ? 'badge-' . $tag->color : 'badge-light-' . $tag->color }} text-decoration-none px-3 py-2">
                            <i class="bi bi-tag-fill me-1"></i>{{ $tag->display_name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="sx-kanban">
        @foreach($stages as $stage)
            <div class="sx-kanban-col">
                <div class="card h-100">
                    <div class="card-header border-0 pt-5 pb-3 border-top border-4 border-{{ $stage->color }}">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 d-flex align-items-center gap-2">
                                <span class="badge badge-circle badge-{{ $stage->color }}" style="width:.7rem;height:.7rem;"></span>
                                {{ $stage->display_name }}
                            </span>
                            <span class="text-muted mt-1 fw-semibold fs-7">
                                {{ __('crm::deal.kanban.deals_count', ['count' => $stage->deals->count()]) }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-{{ $stage->color }} fs-7">{{ $stage->deals->count() }}</span>
                        </div>
                    </div>
                    <div class="card-body pt-0 d-flex flex-column gap-4 bg-light-{{ $stage->color }} bg-opacity-25">
                        @forelse($stage->deals as $deal)
                            <div class="sx-kanban-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
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
                                        <i class="bi bi-building me-1 text-warning"></i>{{ $deal->company->name }}
                                    </div>
                                @endif
                                @if($deal->lead?->tags?->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @include('crm::admin.partials.lead-tags', [
                                            'tags' => $deal->lead->tags,
                                            'class' => '',
                                        ])
                                    </div>
                                @endif
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    @if($deal->assignee)
                                        <div class="d-flex align-items-center gap-2 text-muted fs-7">
                                            <span class="symbol symbol-25px symbol-circle">
                                                <span class="symbol-label bg-light-primary text-primary fw-bold fs-8">
                                                    {{ strtoupper(substr($deal->assignee->name, 0, 1)) }}
                                                </span>
                                            </span>
                                            {{ $deal->assignee->name }}
                                        </div>
                                    @else
                                        <span class="text-muted fs-8">{{ __('N/A') }}</span>
                                    @endif
                                    @if($deal->expected_close_date)
                                        <span class="text-muted fs-8">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $deal->expected_close_date->format('M d') }}
                                        </span>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('admin.deals.moveStage', $deal->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="pipeline_stage_id" class="form-select form-select-sm form-select-solid"
                                            onchange="this.form.submit()">
                                        @foreach($stages as $targetStage)
                                            <option value="{{ $targetStage->id }}" @selected($targetStage->id === $deal->pipeline_stage_id)>
                                                → {{ $targetStage->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @empty
                            <div class="sx-kanban-empty text-center text-muted fs-7 py-10">
                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                {{ __('crm::deal.kanban.empty_column') }}
                            </div>
                        @endforelse
                        <x-can perform="sales.deals.create">
                            <a href="{{ route('admin.deals.create') }}" class="btn btn-sm btn-light-{{ $stage->color }}">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('crm::deal.actions.add') }}
                            </a>
                        </x-can>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>
