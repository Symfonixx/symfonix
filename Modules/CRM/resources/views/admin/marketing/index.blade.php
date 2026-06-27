@section('title', __('crm::marketing.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::marketing.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.crm.marketing.create') }}">
            {{ __('crm::marketing.actions.compose') }} <i class="bi bi-envelope-plus mx-1"></i>
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-0">{{ __('crm::marketing.sections.history') }}</h3>
            </div>
        </div>
        <div class="card-body pt-0">
            @if($model->isEmpty())
                <div class="text-center text-muted py-10">
                    <i class="bi bi-envelope fs-2x d-block mb-3"></i>
                    {{ __('crm::marketing.messages.empty') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 gs-0">
                            <th>{{ __('crm::marketing.fields.subject') }}</th>
                            <th>{{ __('crm::marketing.fields.recipients_count') }}</th>
                            <th>{{ __('crm::marketing.fields.sent_by') }}</th>
                            <th>{{ __('crm::marketing.fields.sent_at') }}</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($model as $campaign)
                            <tr>
                                <td>{{ $campaign->subject }}</td>
                                <td>
                                    <span class="badge badge-light-primary">{{ $campaign->recipients_count }}</span>
                                </td>
                                <td>{{ $campaign->user?->name ?? '—' }}</td>
                                <td>{{ $campaign->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.crm.marketing.show', $campaign) }}"
                                       class="btn btn-sm btn-light btn-active-light-primary">
                                        {{ __('crm::marketing.actions.view') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    {{ $model->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
