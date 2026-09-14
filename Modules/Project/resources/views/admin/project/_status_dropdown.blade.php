@props(['project', 'statuses'])

@can('update', $project)
    <div class="dropdown d-inline-block">
        <button type="button"
                class="badge border-0 fw-semibold fs-7 py-1 px-2 dropdown-toggle"
                style="background-color: {{ $project->status?->color_code ?? '#6c757d' }}; color: #fff;"
                data-bs-toggle="dropdown" aria-expanded="false">
            {{ $project->status?->name ?? __('N/A') }}
        </button>
        <ul class="dropdown-menu">
            @foreach($statuses as $status)
                @if($status->id === $project->project_status_id)
                    <li>
                        <span class="dropdown-item active d-flex align-items-center gap-2">
                            <span class="rounded-circle flex-shrink-0" style="width: 10px; height: 10px; background-color: {{ $status->color_code }};"></span>
                            {{ $status->name }}
                            <i class="bi bi-check2 ms-auto"></i>
                        </span>
                    </li>
                @else
                    <li>
                        <form method="POST" action="{{ route('admin.projects.updateStatus', $project) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="project_status_id" value="{{ $status->id }}">
                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                <span class="rounded-circle flex-shrink-0" style="width: 10px; height: 10px; background-color: {{ $status->color_code }};"></span>
                                {{ $status->name }}
                            </button>
                        </form>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@else
    @if($project->status)
        <span class="badge fs-7 py-1 px-2" style="background-color: {{ $project->status->color_code }}; color: #fff;">
            {{ $project->status->name }}
        </span>
    @else
        <span class="text-muted">{{ __('N/A') }}</span>
    @endif
@endcan
