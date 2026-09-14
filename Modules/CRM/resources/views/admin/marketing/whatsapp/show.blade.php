@section('title', __('crm::whatsapp.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index', ['channel' => 'whatsapp'])],
            ['label' => $campaign->template?->displayName() ?? __('crm::whatsapp.pages.show_title')],
        ];
        $sources = $campaign->recipient_sources ?? [];
        $sourceLabels = [];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::whatsapp.pages.show_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.crm.marketing.index', ['channel' => 'whatsapp']) }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::marketing.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title flex-column align-items-start">
                <h2 class="fw-bold mb-1">
                    <i class="bi bi-whatsapp text-success me-2"></i>
                    {{ $campaign->template?->displayName() ?? '—' }}
                </h2>
                @php
                    $status = $campaign->status ?? 'pending';
                    $statusBadge = $campaign::statusBadgeClass($status);
                @endphp
                <span class="text-muted fs-7">
                    <span class="badge badge-light-success me-1">{{ __('crm::marketing.channels.whatsapp') }}</span>
                    <span class="badge {{ $statusBadge }} me-1">{{ __('crm::whatsapp.status.'.$status) }}</span>
                    {{ __('crm::marketing.fields.sent_by') }}: {{ $campaign->user?->name ?? '—' }}
                    · {{ $campaign->created_at?->format('Y-m-d H:i') }}
                    · {{ __('crm::marketing.fields.recipients_count') }}: {{ $campaign->recipients_count }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="bg-light-success bg-opacity-10 border border-success border-dashed rounded p-6 mb-8">
                <pre class="mb-0 fs-6 text-gray-800" style="white-space:pre-wrap;font-family:inherit;">{{ $campaign->rendered_preview }}</pre>
            </div>

            @if(!empty($campaign->template_parameters))
                <div class="mb-8">
                    <h5 class="fw-bold mb-3">{{ __('crm::whatsapp.sections.parameters') }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($campaign->template_parameters as $index => $value)
                            <span class="badge badge-light-info">{{ '{{'.$index.'}}' }} = {{ $value }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('crm::whatsapp.sections.message_logs') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('crm::whatsapp.fields.phone') }}</th>
                        <th>{{ __('crm::whatsapp.fields.recipient_type') }}</th>
                        <th>{{ __('crm::marketing.fields.status') }}</th>
                        <th>{{ __('crm::whatsapp.fields.sent_at') }}</th>
                        <th>{{ __('crm::whatsapp.fields.error') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($campaign->messageLogs as $log)
                        @php
                            $logBadge = match ($log->status) {
                                'sent' => 'badge-light-success',
                                'failed' => 'badge-light-danger',
                                default => 'badge-light-warning',
                            };
                        @endphp
                        <tr>
                            <td>{{ $log->phone }}</td>
                            <td>{{ __('crm::whatsapp.recipient_types.'.$log->recipient_type) }}</td>
                            <td><span class="badge {{ $logBadge }}">{{ __('crm::whatsapp.log_status.'.$log->status) }}</span></td>
                            <td>{{ $log->sent_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ Str::limit($log->error_message ?? '—', 60) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-10">{{ __('No records found') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
