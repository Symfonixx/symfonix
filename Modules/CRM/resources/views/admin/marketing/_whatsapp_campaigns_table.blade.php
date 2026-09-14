@if($model->isEmpty())
    <div class="text-center text-muted py-10">
        <i class="bi bi-whatsapp fs-2x d-block mb-3"></i>
        {{ __('crm::whatsapp.messages.empty') }}
    </div>
@else
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
            <thead>
            <tr class="text-start text-muted fw-bold fs-7 gs-0">
                <th>{{ __('crm::whatsapp.fields.template_name') }}</th>
                <th>{{ __('crm::marketing.fields.channel') }}</th>
                <th>{{ __('crm::marketing.fields.status') }}</th>
                <th>{{ __('crm::marketing.fields.recipients_count') }}</th>
                <th>{{ __('crm::marketing.fields.sent_by') }}</th>
                <th>{{ __('crm::marketing.fields.sent_at') }}</th>
                <th class="text-end"></th>
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
            @foreach($model as $campaign)
                @php
                    $status = $campaign->status ?? 'pending';
                    $statusBadge = $campaign::statusBadgeClass($status);
                @endphp
                <tr>
                    <td>{{ $campaign->template?->displayName() ?? '—' }}</td>
                    <td><span class="badge badge-light-success"><i class="bi bi-whatsapp me-1"></i>{{ __('crm::marketing.channels.whatsapp') }}</span></td>
                    <td>
                        <span class="badge {{ $statusBadge }}">
                            {{ __('crm::whatsapp.status.'.$status) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-light-primary">{{ $campaign->recipients_count }}</span>
                    </td>
                    <td>{{ $campaign->user?->name ?? '—' }}</td>
                    <td>{{ $campaign->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.crm.marketing.whatsapp.show', $campaign) }}"
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
