@section('title', $ticket->ticket_number.' - '.$ticket->subject)

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('support::ticket.pages.index_title'), 'url' => route('admin.tickets.index')],
            ['label' => $ticket->ticket_number],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="$ticket->ticket_number" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.tickets.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('support::ticket.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="row g-5">
        <div class="col-xl-4">
            <div class="card mb-5">
                <div class="card-header">
                    <h3 class="card-title">{{ __('support::ticket.pages.show_title') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">{{ __('support::ticket.fields.subject') }}</div>
                        <div class="fw-bold text-gray-800">{{ $ticket->subject }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">{{ __('support::ticket.fields.customer') }}</div>
                        <div class="fw-semibold">{{ $ticket->user?->name }}</div>
                        <div class="text-muted fs-7">{{ $ticket->user?->email }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">{{ __('support::ticket.fields.category') }}</div>
                        <div>{{ $ticket->category?->getTranslation('name', app()->getLocale()) }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">{{ __('support::ticket.fields.description') }}</div>
                        <div class="text-gray-700" style="white-space: pre-wrap;">{{ $ticket->description }}</div>
                    </div>
                    @if($ticket->attachment_path)
                        <div class="mb-4">
                            <div class="text-muted fs-7 mb-1">{{ __('support::ticket.fields.attachment') }}</div>
                            <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" class="text-primary">
                                <i class="bi bi-paperclip me-1"></i>{{ $ticket->attachment_name }}
                            </a>
                        </div>
                    @endif
                    <div class="text-muted fs-7">
                        {{ __('Created At') }}: {{ $ticket->created_at }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('support::ticket.actions.manage') }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="form-label">{{ __('support::ticket.fields.status') }}</label>
                            <select name="status" class="form-select form-select-solid">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @selected($ticket->status === $status)>
                                        {{ __('support::ticket.status.'.$status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ __('support::ticket.fields.priority') }}</label>
                            <select name="priority" class="form-select form-select-solid">
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority }}" @selected($ticket->priority === $priority)>
                                        {{ __('support::ticket.priority.'.$priority) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ __('support::ticket.fields.assigned_to') }}</label>
                            <select name="assigned_to" class="form-select form-select-solid">
                                <option value="">{{ __('support::ticket.fields.unassigned') }}</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" @selected($ticket->assigned_to === $admin->id)>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ __('Save Changes') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('support::ticket.pages.conversation') }}</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-4 mb-8">
                        <div class="border rounded p-4 bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="fw-bold text-gray-800">{{ $ticket->user?->name }}</span>
                                    <span class="badge badge-light-secondary ms-2">{{ __('support::ticket.labels.customer') }}</span>
                                </div>
                                <span class="text-muted fs-7">{{ $ticket->created_at }}</span>
                            </div>
                            <div class="text-gray-700" style="white-space: pre-wrap;">{{ $ticket->description }}</div>
                        </div>

                        @foreach($ticket->messages as $message)
                            <div class="border rounded p-4 {{ $message->user?->isAdmin() ? 'bg-light-primary' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="fw-bold text-gray-800">{{ $message->user?->name }}</span>
                                        <span class="badge badge-light-{{ $message->user?->isAdmin() ? 'primary' : 'secondary' }} ms-2">
                                            {{ $message->user?->isAdmin() ? __('support::ticket.labels.staff') : __('support::ticket.labels.customer') }}
                                        </span>
                                    </div>
                                    <span class="text-muted fs-7">{{ $message->created_at }}</span>
                                </div>
                                <div class="text-gray-700" style="white-space: pre-wrap;">{{ $message->body }}</div>
                                @if($message->attachment_path)
                                    <div class="mt-3">
                                        <a href="{{ asset('storage/'.$message->attachment_path) }}" target="_blank" class="text-primary">
                                            <i class="bi bi-paperclip me-1"></i>{{ $message->attachment_name }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if(!$ticket->isClosed())
                        <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label">{{ __('support::ticket.fields.reply') }}</label>
                                <textarea name="body" rows="4" class="form-control form-control-solid @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label">{{ __('support::ticket.fields.attachment') }}</label>
                                <input type="file" name="attachment" class="form-control form-control-solid @error('attachment') is-invalid @enderror">
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>{{ __('support::ticket.actions.send_reply') }}
                            </button>
                        </form>
                    @else
                        <div class="alert alert-secondary mb-0">{{ __('support::ticket.messages.closed') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
