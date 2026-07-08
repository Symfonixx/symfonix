@if($project->description)
    <div class="mb-8">
        <div class="text-muted fs-7 fw-semibold text-uppercase mb-2">{{ __('project::project.fields.description') }}</div>
        <div class="text-gray-800 fs-6" style="white-space: pre-wrap;">{{ $project->description }}</div>
    </div>
@endif

<div class="row g-5 mb-8">
    <div class="col-sm-6">
        <div class="border border-dashed border-gray-300 rounded p-4 h-100">
            <div class="text-muted fs-7 mb-1">{{ __('project::project.fields.company') }}</div>
            <div class="fw-bold text-gray-800">
                @if($project->company)
                    <a href="{{ route('admin.companies.show', $project->company_id) }}" class="text-hover-primary text-gray-800">
                        {{ $project->company->name }}
                    </a>
                @else
                    {{ __('N/A') }}
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="border border-dashed border-gray-300 rounded p-4 h-100">
            <div class="text-muted fs-7 mb-1">{{ __('project::project.fields.deal') }}</div>
            <div class="fw-bold text-gray-800">
                @if($project->deal)
                    <a href="{{ route('admin.deals.show', $project->deal_id) }}" class="text-hover-primary text-gray-800">
                        {{ $project->deal->title }}
                    </a>
                @else
                    {{ __('N/A') }}
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="border border-dashed border-gray-300 rounded p-4 h-100">
            <div class="text-muted fs-7 mb-1">{{ __('project::project.fields.start_date') }}</div>
            <div class="fw-bold text-gray-800">{{ $project->start_date?->format('Y-m-d') ?: __('N/A') }}</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="border border-dashed border-gray-300 rounded p-4 h-100">
            <div class="text-muted fs-7 mb-1">{{ __('project::project.fields.due_date') }}</div>
            <div class="fw-bold text-gray-800">{{ $project->due_date?->format('Y-m-d') ?: __('N/A') }}</div>
        </div>
    </div>
</div>

<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="text-muted fs-7 fw-semibold text-uppercase">{{ __('project::project.fields.attachments') }}</div>
        @if(!empty($project->attachments))
            <span class="badge badge-light">{{ count($project->attachments) }}</span>
        @endif
    </div>
    @if(!empty($project->attachments))
        <div class="d-flex flex-column gap-2">
            @foreach($project->attachments as $attachment)
                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank"
                   class="d-flex align-items-center gap-3 p-3 bg-light rounded text-hover-primary">
                    <span class="symbol symbol-35px">
                        <span class="symbol-label bg-white">
                            <i class="bi bi-paperclip text-primary"></i>
                        </span>
                    </span>
                    <span class="fw-semibold text-gray-800">
                        {{ $attachment['name'] ?? basename($attachment['path']) }}
                    </span>
                    <i class="bi bi-box-arrow-up-right ms-auto text-muted fs-7"></i>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-muted fs-7 py-6 text-center border border-dashed border-gray-300 rounded">
            {{ __('project::project.messages.no_attachments') }}
        </div>
    @endif
</div>
