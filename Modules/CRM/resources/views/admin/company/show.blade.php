@section('title', __('crm::company.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::company.menu.companies'), 'url' => route('admin.companies.index')],
            ['label' => __('crm::company.pages.show_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::company.pages.show_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.companies.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::company.actions.back_to_list') }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.companies.edit', $company) }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card sx-show-hero mb-8">
        <div class="card-body p-6 p-lg-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-5">
                <div class="d-flex align-items-center gap-4">
                    <span class="sx-avatar"><i class="bi bi-building"></i></span>
                    <div>
                        <h2 class="text-white fw-bold mb-2">{{ $company->name }}</h2>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge badge-light-{{ $company->status === 'active' ? 'success' : 'danger' }}">
                                {{ __('crm::company.status.'.$company->status) }}
                            </span>
                            @if($company->email)
                                <a href="mailto:{{ $company->email }}" class="text-white opacity-75 fs-7">
                                    <i class="bi bi-envelope me-1"></i>{{ $company->email }}
                                </a>
                            @endif
                            @if($company->phone)
                                <a href="tel:{{ $company->phone }}" class="text-white opacity-75 fs-7">
                                    <i class="bi bi-telephone me-1"></i>{{ $company->phone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <x-can perform="crm.contacts.create">
                    <a href="{{ route('admin.contacts.create', ['company_id' => $company->id]) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-person-plus me-1"></i>{{ __('crm::contact.actions.add') }}
                    </a>
                </x-can>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.name') }}</div>
                <div class="col-md-9">{{ $company->name }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.customer') }}</div>
                <div class="col-md-9">{{ $company->user?->name ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.email') }}</div>
                <div class="col-md-9">{{ $company->email ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.phone') }}</div>
                <div class="col-md-9">{{ $company->phone ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.country') }}</div>
                <div class="col-md-9">{{ $company->country ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.city') }}</div>
                <div class="col-md-9">{{ $company->city ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.address') }}</div>
                <div class="col-md-9">{{ $company->address ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.notes') }}</div>
                <div class="col-md-9">{{ $company->notes ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::company.fields.status') }}</div>
                <div class="col-md-9">
                    <span class="badge badge-light-{{ $company->status === 'active' ? 'success' : 'danger' }}">
                        {{ __('crm::company.status.'.$company->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($company->contacts->isNotEmpty())
        <div class="card mt-6">
            <div class="card-header align-items-center">
                <h3 class="card-title">{{ __('crm::contact.menu.contacts') }}</h3>
                <div class="card-toolbar">
                    <x-can perform="crm.contacts.create">
                        <a href="{{ route('admin.contacts.create', ['company_id' => $company->id]) }}"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>{{ __('crm::contact.actions.add') }}
                        </a>
                    </x-can>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gy-4 mb-0">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('crm::contact.fields.name') }}</th>
                            <th>{{ __('crm::contact.fields.email') }}</th>
                            <th>{{ __('crm::contact.fields.phone') }}</th>
                            <th>{{ __('crm::contact.fields.job_title') }}</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($company->contacts as $contact)
                            <tr>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email ?: __('N/A') }}</td>
                                <td>{{ $contact->phone ?: __('N/A') }}</td>
                                <td>{{ $contact->job_title ?: __('N/A') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.contacts.show', $contact) }}"
                                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm">
                                        <i class="bi bi-eye fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card mt-6">
            <div class="card-header align-items-center">
                <h3 class="card-title">{{ __('crm::contact.menu.contacts') }}</h3>
                <div class="card-toolbar">
                    <x-can perform="crm.contacts.create">
                        <a href="{{ route('admin.contacts.create', ['company_id' => $company->id]) }}"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>{{ __('crm::contact.actions.add') }}
                        </a>
                    </x-can>
                </div>
            </div>
            <div class="card-body text-center text-muted py-10">{{ __('N/A') }}</div>
        </div>
    @endif

    <div class="card mt-6">
        <div class="card-header align-items-center">
            <h3 class="card-title">{{ __('crm::lead.menu.leads') }}</h3>
            <div class="card-toolbar">
                <x-can perform="crm.leads.create">
                    <a href="{{ route('admin.leads.create', ['company_id' => $company->id]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('crm::lead.actions.add') }}
                    </a>
                </x-can>
            </div>
        </div>
        <div class="card-body p-0">
            @if($company->leads->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gy-4 mb-0">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('crm::lead.fields.name') }}</th>
                            <th>{{ __('crm::lead.fields.status') }}</th>
                            <th>{{ __('crm::lead.fields.tags') }}</th>
                            <th>{{ __('crm::lead.fields.email') }}</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($company->leads as $lead)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="text-hover-primary text-gray-800">
                                        {{ $lead->name ?? __('N/A') }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ \Modules\CRM\Models\Lead::statusBadgeColor($lead->status) }}">
                                        {{ __('crm::lead.status.' . ($lead->status ?? 'new')) }}
                                    </span>
                                </td>
                                <td>
                                    @include('crm::admin.partials.lead-tags', [
                                        'tags' => $lead->tags,
                                        'empty' => '—',
                                    ])
                                </td>
                                <td>{{ $lead->email ?: __('N/A') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.leads.show', $lead) }}"
                                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm">
                                        <i class="bi bi-eye fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-10">{{ __('N/A') }}</div>
            @endif
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header align-items-center">
            <h3 class="card-title">{{ __('crm::deal.menu.deals') }}</h3>
            <div class="card-toolbar">
                <x-can perform="sales.deals.create">
                    <a href="{{ route('admin.deals.create', ['company_id' => $company->id]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('crm::deal.actions.add') }}
                    </a>
                </x-can>
            </div>
        </div>
        <div class="card-body p-0">
            @if($company->deals->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gy-4 mb-0">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('crm::deal.fields.title') }}</th>
                            <th>{{ __('crm::deal.fields.stage') }}</th>
                            <th>{{ __('crm::lead.fields.tags') }}</th>
                            <th>{{ __('crm::deal.fields.value') }}</th>
                            <th>{{ __('crm::deal.fields.status') }}</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($company->deals as $deal)
                            <tr>
                                <td>{{ $deal->title }}</td>
                                <td>
                                    @if($deal->pipelineStage)
                                        <span class="badge" style="background-color: {{ $deal->pipelineStage->color }}20; color: {{ $deal->pipelineStage->color }};">
                                            {{ $deal->pipelineStage->name }}
                                        </span>
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </td>
                                <td>
                                    @include('crm::admin.partials.lead-tags', [
                                        'tags' => $deal->lead?->tags ?? collect(),
                                        'empty' => '—',
                                    ])
                                </td>
                                <td>{{ $deal->value ? number_format($deal->value, 2).' '.$deal->currency : __('N/A') }}</td>
                                <td>{{ __('crm::deal.status.'.$deal->status) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.deals.show', $deal) }}"
                                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm">
                                        <i class="bi bi-eye fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-10">{{ __('N/A') }}</div>
            @endif
        </div>
    </div>

    @if($company->contactForms->isNotEmpty())
        <div class="card mt-6">
            <div class="card-header align-items-center">
                <h3 class="card-title">{{ __('crm::contact_form.menu.inquiries') }}</h3>
                <div class="card-toolbar">
                    <a href="{{ route('admin.contact_forms.index') }}" class="btn btn-sm btn-light-primary">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('crm::contact_form.actions.back_to_list') }}
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gy-4 mb-0">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('crm::contact_form.fields.name') }}</th>
                            <th>{{ __('crm::contact_form.fields.email') }}</th>
                            <th>{{ __('crm::contact_form.fields.subject') }}</th>
                            <th>{{ __('Created At') }}</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($company->contactForms as $inquiry)
                            <tr>
                                <td>{{ $inquiry->name }}</td>
                                <td>{{ $inquiry->email }}</td>
                                <td>{{ $inquiry->subject ?: __('N/A') }}</td>
                                <td>{{ $inquiry->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card mt-6">
        <div class="card-header align-items-center">
            <h3 class="card-title">{{ __('crm::subscription.sections.company_subscriptions') }}</h3>
            <div class="card-toolbar">
                <x-can perform="sales.subscriptions.create">
                    <a href="{{ route('admin.subscriptions.create', ['company_id' => $company->id]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('crm::subscription.actions.add_for_company') }}
                    </a>
                </x-can>
            </div>
        </div>
        <div class="card-body p-0">
            @if($company->subscriptions->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gy-4 mb-0">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('crm::subscription.fields.name') }}</th>
                            <th>{{ __('crm::subscription.fields.amount') }}</th>
                            <th>{{ __('crm::subscription.fields.billing_cycle') }}</th>
                            <th>{{ __('crm::subscription.fields.status') }}</th>
                            <th>{{ __('crm::subscription.fields.renewal_at') }}</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($company->subscriptions as $subscription)
                            @php
                                $statusColor = match($subscription->status) {
                                    'active' => 'success',
                                    'trial' => 'info',
                                    'paused' => 'warning',
                                    'cancelled', 'expired' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $subscription->name }}</td>
                                <td>{{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}</td>
                                <td>{{ __('crm::subscription.billing_cycle.'.$subscription->billing_cycle) }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $statusColor }}">
                                        {{ __('crm::subscription.status.'.$subscription->status) }}
                                    </span>
                                </td>
                                <td>{{ $subscription->renewal_at?->format('Y-m-d') ?: __('N/A') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}"
                                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm">
                                        <i class="bi bi-eye fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-10">
                    {{ __('crm::subscription.empty.company') }}
                </div>
            @endif
        </div>
    </div>

    @include('crm::admin.partials.timeline', ['subject' => $company, 'subjectType' => 'company'])
</x-admin-layout>
