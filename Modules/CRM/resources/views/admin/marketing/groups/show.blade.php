@section('title', $group->title)

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index')],
            ['label' => $group->title],
        ];
        $missing = $group->missingKeys();
        $incomplete = $group->isIncomplete();
    @endphp
    <x-admin.breadcrumb :pageTitle="$group->title" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3 sx-actions">
        <a class="btn btn-sm fw-bold btn-light" href="{{ route('admin.crm.marketing.index') }}" data-action="back">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::marketing.actions.back_to_list') }}
        </a>
        @canany(['marketing.email.send', 'marketing.whatsapp.send'])
            <a class="btn btn-sm fw-bold btn-light-warning" href="{{ route('admin.crm.marketing.groups.edit', $group) }}" data-action="edit">
                <i class="bi bi-pencil me-1"></i>{{ __('crm::marketing.actions.edit_campaign') }}
            </a>
        @endcanany
        <x-can perform="marketing.email.send">
            <a class="btn btn-sm fw-bold btn-success" href="{{ route('admin.crm.marketing.create', ['group' => $group->id]) }}" data-action="create">
                {{ __('crm::marketing.actions.send_email') }} <i class="bi bi-envelope-plus mx-1"></i>
            </a>
        </x-can>
        <x-can perform="marketing.whatsapp.send">
            <a class="btn btn-sm fw-bold btn-success" href="{{ route('admin.crm.marketing.whatsapp.create', ['group' => $group->id]) }}" data-action="create">
                {{ __('crm::marketing.actions.send_whatsapp') }} <i class="bi bi-whatsapp mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title flex-column align-items-start">
                <h2 class="fw-bold mb-1">{{ $group->title }}</h2>
                <span class="text-muted fs-7">
                    @if($incomplete)
                        <span class="badge badge-light-warning me-1">{{ __('crm::marketing.groups.incomplete') }}</span>
                    @else
                        <span class="badge badge-light-success me-1">{{ __('crm::marketing.groups.complete') }}</span>
                    @endif
                    {{ __('crm::marketing.fields.sent_by') }}: {{ $group->user?->name ?? '—' }}
                    · {{ $group->created_at?->format('Y-m-d H:i') }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-8">
                <div class="text-muted fw-semibold mb-2">{{ __('crm::marketing.fields.goal') }}</div>
                <div class="fs-6 text-gray-800" style="white-space: pre-wrap;">{{ $group->goal }}</div>
            </div>

            @if($missing !== [])
                <div class="alert alert-warning d-flex align-items-start p-5 mb-0">
                    <i class="bi bi-exclamation-triangle-fill fs-2hx text-warning me-4 mt-1"></i>
                    <div>
                        <h5 class="mb-2">{{ __('crm::marketing.missing.heading') }}</h5>
                        <ul class="mb-0 ps-4">
                            @foreach($missing as $key)
                                <li>{{ __('crm::marketing.missing.'.$key) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @can('marketing.email.view')
        <div class="card mb-8">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold mb-0">{{ __('crm::marketing.fields.email_sends') }}</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                @if($group->emailCampaigns->isEmpty())
                    <div class="text-muted py-6">{{ __('crm::marketing.missing.email') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                            <tr class="text-start text-muted fw-bold fs-7 gs-0">
                                <th>{{ __('crm::marketing.fields.subject') }}</th>
                                <th>{{ __('crm::marketing.fields.status') }}</th>
                                <th>{{ __('crm::marketing.fields.recipients_count') }}</th>
                                <th>{{ __('crm::marketing.fields.sent_at') }}</th>
                                <th class="text-end"></th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                            @foreach($group->emailCampaigns as $campaign)
                                @php
                                    $status = $campaign->status ?? 'pending';
                                    $statusBadge = $campaign::statusBadgeClass($status);
                                @endphp
                                <tr>
                                    <td>{{ Str::limit(strip_tags($campaign->subject), 80) }}</td>
                                    <td><span class="badge {{ $statusBadge }}">{{ __('crm::marketing.status.'.$status) }}</span></td>
                                    <td><span class="badge badge-light-primary">{{ $campaign->recipients_count }}</span></td>
                                    <td>{{ $campaign->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.crm.marketing.show', $campaign) }}" class="btn btn-sm btn-light-info">
                                            {{ __('crm::marketing.actions.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endcan

    @can('marketing.whatsapp.view')
        <div class="card mb-8">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold mb-0">{{ __('crm::marketing.fields.whatsapp_sends') }}</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                @if($group->whatsappCampaigns->isEmpty())
                    <div class="text-muted py-6">{{ __('crm::marketing.missing.whatsapp') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                            <tr class="text-start text-muted fw-bold fs-7 gs-0">
                                <th>{{ __('crm::whatsapp.fields.template_name') }}</th>
                                <th>{{ __('crm::marketing.fields.status') }}</th>
                                <th>{{ __('crm::marketing.fields.recipients_count') }}</th>
                                <th>{{ __('crm::marketing.fields.sent_at') }}</th>
                                <th class="text-end"></th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                            @foreach($group->whatsappCampaigns as $campaign)
                                @php
                                    $status = $campaign->status ?? 'pending';
                                    $statusBadge = $campaign::statusBadgeClass($status);
                                @endphp
                                <tr>
                                    <td>{{ $campaign->template?->displayName() ?? '—' }}</td>
                                    <td><span class="badge {{ $statusBadge }}">{{ __('crm::whatsapp.status.'.$status) }}</span></td>
                                    <td><span class="badge badge-light-primary">{{ $campaign->recipients_count }}</span></td>
                                    <td>{{ $campaign->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.crm.marketing.whatsapp.show', $campaign) }}" class="btn btn-sm btn-light-info">
                                            {{ __('crm::marketing.actions.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endcan

    @canany(['marketing.email.send', 'marketing.whatsapp.send'])
        @if($group->emailCampaigns->isEmpty() && $group->whatsappCampaigns->isEmpty())
            <form method="POST" action="{{ route('admin.crm.marketing.groups.destroy', $group) }}"
                  onsubmit="return confirm(@json(__('crm::marketing.groups.delete_confirm')));">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-light-danger" data-action="delete">
                    <i class="bi bi-trash me-1"></i>{{ __('crm::marketing.actions.delete_campaign') }}
                </button>
            </form>
        @endif
    @endcanany
</x-admin-layout>
