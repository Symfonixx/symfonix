@section('title', __('crm::contact.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::contact.pages.index_title'), 'url' => route('admin.contacts.index')],
            ['label' => __('crm::contact.pages.show_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::contact.pages.show_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.contacts.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::contact.actions.back_to_list') }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.contacts.edit', $contact) }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card sx-show-hero mb-8">
        <div class="card-body p-6 p-lg-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-5">
                <div class="d-flex align-items-center gap-4">
                    <span class="sx-avatar">{{ strtoupper(substr($contact->name ?? 'C', 0, 1)) }}</span>
                    <div>
                        <h2 class="text-white fw-bold mb-2">{{ $contact->name }}</h2>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            @if($contact->job_title)
                                <span class="text-white opacity-75 fs-7">{{ $contact->job_title }}</span>
                            @endif
                            @if($contact->is_primary)
                                <span class="badge badge-light-primary">{{ __('crm::contact.fields.is_primary') }}</span>
                            @endif
                            @if($contact->company)
                                <a href="{{ route('admin.companies.show', $contact->company) }}" class="text-white opacity-75 fs-7">
                                    <i class="bi bi-building me-1"></i>{{ $contact->company->name }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if($contact->email)
                        <a href="mailto:{{ $contact->email }}" class="btn btn-light btn-sm">
                            <i class="bi bi-envelope me-1"></i>{{ $contact->email }}
                        </a>
                    @endif
                    @if($contact->phone)
                        <a href="tel:{{ $contact->phone }}" class="btn btn-light btn-sm">
                            <i class="bi bi-telephone me-1"></i>{{ $contact->phone }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::contact.fields.name') }}</div>
                <div class="col-md-9">{{ $contact->name }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::contact.fields.email') }}</div>
                <div class="col-md-9">{{ $contact->email ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::contact.fields.phone') }}</div>
                <div class="col-md-9">{{ $contact->phone ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::contact.fields.job_title') }}</div>
                <div class="col-md-9">{{ $contact->job_title ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::contact.fields.company') }}</div>
                <div class="col-md-9">
                    @if($contact->company)
                        <a href="{{ route('admin.companies.show', $contact->company) }}">{{ $contact->company->name }}</a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::contact.fields.is_primary') }}</div>
                <div class="col-md-9">
                    <span class="badge badge-light-{{ $contact->is_primary ? 'success' : 'secondary' }}">
                        {{ $contact->is_primary ? __('Yes') : __('No') }}
                    </span>
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::contact.fields.notes') }}</div>
                <div class="col-md-9">{{ $contact->notes ?: __('N/A') }}</div>
            </div>
        </div>
    </div>

    @include('crm::admin.partials.timeline', ['subject' => $contact, 'subjectType' => 'contact'])
</x-admin-layout>
