@section('title', __('crm::lead.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::lead.menu.leads'), 'url' => route('admin.leads.index')],
            ['label' => __('crm::lead.pages.show_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::lead.pages.show_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.leads.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::lead.actions.back_to_list') }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.leads.edit', $lead) }}">
            <i class="bi bi-pencil me-1"></i>{{ __('crm::lead.actions.edit') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card sx-show-hero mb-8">
        <div class="card-body p-6 p-lg-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-5">
                <div class="d-flex align-items-center gap-4">
                    <span class="sx-avatar">{{ strtoupper(substr($lead->name ?? 'L', 0, 1)) }}</span>
                    <div>
                        <h2 class="text-white fw-bold mb-2">{{ $lead->name ?? __('crm::lead.pages.show_title') }}</h2>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            @if($lead->source)
                                <span class="badge badge-light-{{ \Modules\CRM\Models\Lead::sourceBadgeColor($lead->source) }}">
                                    {{ __('crm::lead.sources.' . $lead->source) }}
                                </span>
                            @endif
                            <span class="badge badge-light-{{ \Modules\CRM\Models\Lead::statusBadgeColor($lead->status) }}">
                                {{ __('crm::lead.status.' . ($lead->status ?? 'new')) }}
                            </span>
                            <span class="badge badge-light-{{ $lead->blocked ? 'danger' : 'success' }}">
                                {{ $lead->blocked ? __('crm::lead.status.blocked') : __('crm::lead.status.active') }}
                            </span>
                            @if($lead->deal_id)
                                <span class="badge badge-light-info">{{ __('crm::lead.conversion.converted_badge') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" class="btn btn-light btn-sm">
                            <i class="bi bi-envelope me-1"></i>{{ $lead->email }}
                        </a>
                    @endif
                    @if($lead->phone)
                        <a href="tel:{{ $lead->phone }}" class="btn btn-light btn-sm">
                            <i class="bi bi-telephone me-1"></i>{{ $lead->phone }}
                        </a>
                    @endif
                    <a class="btn btn-primary btn-sm" href="{{ route('admin.leads.edit', $lead) }}">
                        <i class="bi bi-pencil me-1"></i>{{ __('crm::lead.actions.edit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-8">
            <div class="mb-10">
                <h4 class="fw-bold mb-4">{{ __('crm::lead.sections.contact_information') }}</h4>
                <div class="row g-6">
                    <div class="col-md-6">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.name') }}</div>
                            <div class="fw-semibold text-gray-800">{{ $lead->name ?? __('N/A') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.email') }}</div>
                            @if($lead->email)
                                <a href="mailto:{{ $lead->email }}" class="fw-semibold text-hover-primary">{{ $lead->email }}</a>
                            @else
                                <div class="fw-semibold text-gray-800">{{ __('N/A') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.phone') }}</div>
                            @if($lead->phone)
                                <a href="tel:{{ $lead->phone }}" class="fw-semibold text-hover-primary">{{ $lead->phone }}</a>
                            @else
                                <div class="fw-semibold text-gray-800">{{ __('N/A') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <h4 class="fw-bold mb-4">{{ __('crm::lead.sections.company') }}</h4>
                <div class="row g-6">
                    <div class="col-md-6">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.company') }}</div>
                            @if($lead->company)
                                <a href="{{ route('admin.companies.show', $lead->company) }}" class="fw-semibold text-hover-primary">
                                    {{ $lead->company->name }}
                                </a>
                                @if($lead->company->email)
                                    <div class="text-muted fs-7 mt-2">{{ $lead->company->email }}</div>
                                @endif
                            @else
                                <div class="fw-semibold text-gray-800">{{ __('N/A') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.company_name') }}</div>
                            <div class="fw-semibold text-gray-800">{{ $lead->company_name ?? __('N/A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <h4 class="fw-bold mb-0">{{ __('crm::lead.fields.tags') }}</h4>
                    <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-sm btn-light-primary">
                        <i class="bi bi-pencil me-1"></i>{{ __('crm::lead.actions.edit_tags') }}
                    </a>
                </div>
                <div class="border border-dashed border-gray-300 rounded p-5">
                    @include('crm::admin.partials.lead-tags', [
                        'tags' => $lead->tags,
                        'solid' => true,
                        'class' => 'fs-7 me-2 mb-2 px-4 py-2',
                        'empty' => __('crm::lead.hints.no_tags_assigned'),
                    ])
                </div>
            </div>

            @php($visibleCustomFields = ($customFields ?? collect())->filter(fn ($field) => $field->is_active || filled($lead->customFieldValue($field->key))))
            @if($visibleCustomFields->isNotEmpty())
                <div class="mb-10">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <h4 class="fw-bold mb-0">{{ __('crm::lead.sections.custom_fields') }}</h4>
                        <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-sm btn-light-primary">
                            <i class="bi bi-pencil me-1"></i>{{ __('crm::lead.actions.edit_custom_fields') }}
                        </a>
                    </div>
                    <div class="row g-6">
                        @foreach($visibleCustomFields as $customField)
                            <div class="col-md-6">
                                <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                                    <div class="text-muted fs-7 mb-1">{{ $customField->display_label }}</div>
                                    <div class="fw-semibold text-gray-800">
                                        {{ $customField->formatValue($lead->customFieldValue($customField->key)) ?: __('N/A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-10">
                <h4 class="fw-bold mb-4">{{ __('crm::lead.sections.lead_details') }}</h4>
                <div class="row g-6">
                    <div class="col-md-4">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.services') }}</div>
                            <div class="fw-semibold text-gray-800">
                                @if($lead->services->isNotEmpty())
                                    @foreach($lead->services as $service)
                                        <span class="badge badge-light-primary me-1 mb-1">
                                            {{ $service->getTranslation('title', app()->getLocale()) }}
                                        </span>
                                    @endforeach
                                @elseif($lead->service)
                                    {{ $lead->service->getTranslation('title', app()->getLocale()) }}
                                @else
                                    {{ $lead->service_interest ?? __('N/A') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.project_budget') }}</div>
                            <div class="fw-semibold text-gray-800">{{ $lead->project_budget ?? __('N/A') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border border-dashed border-gray-300 rounded p-5 h-100">
                            <div class="text-muted fs-7 mb-1">{{ __('crm::lead.fields.ip_address') }}</div>
                            @if($lead->ip_address)
                                <a href="https://whatismyipaddress.com/ip/{{ $lead->ip_address }}" target="_blank" class="fw-semibold text-hover-primary">
                                    {{ $lead->ip_address }}
                                </a>
                            @else
                                <div class="fw-semibold text-gray-800">{{ __('N/A') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <h4 class="fw-bold mb-4">{{ __('crm::lead.fields.problem_statement') }}</h4>
                <div class="bg-light-primary bg-opacity-10 border border-primary border-dashed rounded p-5">
                    {{ $lead->problem_statement ?? __('N/A') }}
                </div>
            </div>

            @if(!empty($lead->chat_transcript))
                <div class="mb-10">
                    <h4 class="fw-bold mb-4">{{ __('crm::lead.fields.chat_transcript') }}</h4>
                    <div class="bg-light rounded p-5" style="max-height: 360px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0">
                            @foreach($lead->chat_transcript as $message)
                                <li class="mb-3 pb-3 border-bottom border-gray-200">
                                    <span class="badge badge-light-{{ ($message['role'] ?? 'bot') === 'user' ? 'primary' : 'secondary' }}">
                                        {{ strtoupper($message['role'] ?? 'BOT') }}
                                    </span>
                                    <span class="ms-2 text-gray-800">{{ $message['message'] ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.leads.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('crm::lead.actions.back_to_list') }}
                </a>
                @if($lead->deal_id)
                    <a href="{{ route('admin.deals.show', $lead->deal_id) }}" class="btn btn-success">
                        <i class="bi bi-briefcase me-1"></i>{{ __('crm::lead.conversion.view_deal') }}
                    </a>
                @elseif(auth()->user()?->can('crm.leads.edit'))
                    @if($lead->company_name || $lead->company_id)
                        <form method="POST" action="{{ route('admin.leads.convertCustomer', $lead) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-person-check me-1"></i>{{ __('crm::lead.conversion.convert_customer') }}
                            </button>
                        </form>
                    @endif
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#convertLeadModal">
                        <i class="bi bi-arrow-right-circle me-1"></i>{{ __('crm::lead.conversion.convert') }}
                    </button>
                @endif
                <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>{{ __('crm::lead.actions.edit') }}
                </a>
                @if($lead->blocked)
                    <form method="POST" action="{{ route('admin.leads.unblock', $lead) }}">
                        @csrf
                        <button type="submit" class="btn btn-light-warning">
                            <i class="bi bi-unlock"></i> {{ __('crm::lead.actions.unblock') }}
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.leads.block', $lead) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-lock"></i> {{ __('crm::lead.actions.block') }}
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-light-danger">
                        <i class="bi bi-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(!$lead->deal_id && auth()->user()?->can('crm.leads.edit'))
        <div class="modal fade" id="convertLeadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.leads.convert', $lead) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('crm::lead.conversion.modal_title') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-5">{{ __('crm::lead.conversion.modal_hint') }}</p>
                            <div class="mb-5">
                                <label class="form-label">{{ __('crm::lead.conversion.deal_title') }}</label>
                                <input type="text" name="title" class="form-control form-control-solid"
                                       value="{{ $lead->service_interest ?: $lead->name }}"
                                       placeholder="{{ __('crm::lead.conversion.deal_title_placeholder') }}"/>
                            </div>
                            <div class="mb-5">
                                <label class="form-label">{{ __('crm::deal.fields.stage') }}</label>
                                <select name="pipeline_stage_id" class="form-select form-select-solid">
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}" @selected($stage->is_default)>
                                            {{ $stage->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">{{ __('crm::lead.fields.assignee') }}</label>
                                <select name="assigned_to" class="form-select form-select-solid">
                                    <option value="">{{ __('crm::lead.fields.select_assignee') }}</option>
                                    @foreach(\Modules\User\Support\EmployeeAccess::assignableQuery()->get() as $assignee)
                                        <option value="{{ $assignee->id }}" @selected($lead->assigned_to === $assignee->id)>
                                            {{ $assignee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('crm::lead.conversion.confirm') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @include('crm::admin.partials.timeline', ['subject' => $lead, 'subjectType' => 'lead'])
</x-admin-layout>
