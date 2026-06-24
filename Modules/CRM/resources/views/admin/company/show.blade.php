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
@endsection

<x-admin-layout>
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

    @if($company->contactForms->isNotEmpty())
        <div class="card mt-6">
            <div class="card-header align-items-center">
                <h3 class="card-title">{{ __('Contacts') }}</h3>
                <div class="card-toolbar">
                    <a href="{{ route('admin.contact_forms.create', ['company_id' => $company->id]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('crm::contact.actions.add') }}
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gy-4 mb-0">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($company->contactForms as $contact)
                            <tr>
                                <td>{{ $contact->name }}</td>
                                <td><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                                <td>{{ $contact->subject ?: __('N/A') }}</td>
                                <td>{{ $contact->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.contact_forms.edit', $contact) }}"
                                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm">
                                        <i class="bi bi-pencil fs-5"></i>
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
                <h3 class="card-title">{{ __('Contacts') }}</h3>
                <div class="card-toolbar">
                    <a href="{{ route('admin.contact_forms.create', ['company_id' => $company->id]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('crm::contact.actions.add') }}
                    </a>
                </div>
            </div>
            <div class="card-body text-center text-muted py-10">
                {{ __('N/A') }}
            </div>
        </div>
    @endif

    <div class="card mt-6">
        <div class="card-header align-items-center">
            <h3 class="card-title">{{ __('crm::subscription.sections.company_subscriptions') }}</h3>
            <div class="card-toolbar">
                <a href="{{ route('admin.subscriptions.create', ['company_id' => $company->id]) }}"
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('crm::subscription.actions.add_for_company') }}
                </a>
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
