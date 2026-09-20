@if($model->isEmpty())
    <div class="text-center text-muted py-10">
        <i class="bi bi-megaphone fs-2x d-block mb-3"></i>
        {{ __('crm::marketing.groups.empty') }}
    </div>
@else
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
            <thead>
            <tr class="text-start text-muted fw-bold fs-7 gs-0">
                <th>{{ __('crm::marketing.fields.title') }}</th>
                <th>{{ __('crm::marketing.fields.goal') }}</th>
                <th>{{ __('crm::marketing.fields.email_sends') }}</th>
                <th>{{ __('crm::marketing.fields.whatsapp_sends') }}</th>
                <th>{{ __('crm::marketing.fields.status') }}</th>
                <th>{{ __('crm::marketing.fields.sent_by') }}</th>
                <th class="text-end"></th>
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
            @foreach($model as $group)
                @php
                    $incomplete = $group->isIncomplete();
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('admin.crm.marketing.groups.show', $group) }}" class="fw-bold text-gray-800">
                            {{ $group->title }}
                        </a>
                    </td>
                    <td>{{ Str::limit($group->goal, 80) }}</td>
                    <td><span class="badge badge-light-primary">{{ $group->email_campaigns_count }}</span></td>
                    <td><span class="badge badge-light-success">{{ $group->whatsapp_campaigns_count }}</span></td>
                    <td>
                        @if($incomplete)
                            <span class="badge badge-light-warning">{{ __('crm::marketing.groups.incomplete') }}</span>
                        @else
                            <span class="badge badge-light-success">{{ __('crm::marketing.groups.complete') }}</span>
                        @endif
                    </td>
                    <td>{{ $group->user?->name ?? '—' }}</td>
                    <td class="text-end sx-actions">
                        <a href="{{ route('admin.crm.marketing.groups.show', $group) }}"
                           class="btn btn-sm btn-light-info btn-active-light-primary" data-action="view">
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
