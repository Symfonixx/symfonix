@section('title', __('support::ticket.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('support::ticket.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('support::ticket.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="card mb-5">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('support::ticket.fields.status') }}</label>
                    <select name="status" class="form-select form-select-solid">
                        <option value="">{{ __('All') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected($filters['status'] === $status)>
                                {{ __('support::ticket.status.'.$status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('support::ticket.fields.priority') }}</label>
                    <select name="priority" class="form-select form-select-solid">
                        <option value="">{{ __('All') }}</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" @selected($filters['priority'] === $priority)>
                                {{ __('support::ticket.priority.'.$priority) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}" class="form-control form-control-solid"
                           placeholder="{{ __('support::ticket.search.placeholder') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <x-admin.table :model="$model" :search="false">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{ __('support::ticket.fields.ticket_number') }}</th>
            <th>{{ __('support::ticket.fields.subject') }}</th>
            <th>{{ __('support::ticket.fields.customer') }}</th>
            <th>{{ __('support::ticket.fields.category') }}</th>
            <th>{{ __('support::ticket.fields.priority') }}</th>
            <th>{{ __('support::ticket.fields.status') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $ticket)
            <tr>
                <td>
                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-gray-800 text-hover-primary fw-bold">
                        {{ $ticket->ticket_number }}
                    </a>
                </td>
                <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}</td>
                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-semibold">{{ $ticket->user?->name }}</span>
                        <span class="text-muted fs-7">{{ $ticket->user?->email }}</span>
                    </div>
                </td>
                <td>{{ $ticket->category?->getTranslation('name', app()->getLocale()) }}</td>
                <td>
                    <span class="badge badge-light-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'primary') }}">
                        {{ __('support::ticket.priority.'.$ticket->priority) }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-light-{{ $ticket->status === 'closed' || $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'in_progress' ? 'info' : 'secondary') }}">
                        {{ __('support::ticket.status.'.$ticket->status) }}
                    </span>
                </td>
                <td>{{ $ticket->created_at }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-light-primary">
                        {{ __('View') }}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
