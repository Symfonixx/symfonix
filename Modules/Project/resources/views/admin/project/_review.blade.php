@if($project->testimonial)
    @php $review = $project->testimonial; @endphp
    <div class="d-flex flex-column flex-md-row align-items-start gap-5">
        <div class="symbol symbol-70px symbol-circle flex-shrink-0">
            <img src="{{ $review->avatar_link }}" alt="{{ $review->name }}">
        </div>
        <div class="flex-grow-1 w-100">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="fw-bold text-gray-900 fs-5">{{ $review->name }}</span>
                @if($review->customer?->email)
                    <span class="text-muted fs-7">{{ $review->customer->email }}</span>
                @endif
                <span class="badge badge-light-{{ $review->status === 'Published' ? 'success' : 'warning' }}">
                    {{ $review->status === 'Published'
                        ? __('project::project.review.approved')
                        : __('project::project.review.pending') }}
                </span>
            </div>

            <blockquote class="border-start border-3 border-primary ps-5 mb-5">
                <p class="fs-5 text-gray-800 fst-italic mb-0">“{{ $review->quote }}”</p>
            </blockquote>

            <div class="text-muted fs-7 mb-5">
                {{ __('project::project.fields.submitted_at') }}:
                {{ $review->created_at?->format('Y-m-d H:i') }}
            </div>

            <div class="d-flex flex-wrap gap-2">
                @can('cms.testimonials.view')
                    @if($review->status !== 'Published')
                        <form action="{{ route('admin.testimonials.approve', $review) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check-lg me-1"></i>{{ __('project::project.actions.approve_review') }}
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.testimonials.unpublish', $review) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="bi bi-eye-slash me-1"></i>{{ __('project::project.actions.unpublish_review') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.testimonials.edit', $review) }}" class="btn btn-sm btn-light-primary">
                        <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                    </a>
                @endcan
            </div>

            <p class="text-muted fs-7 mt-6 mb-0">
                {{ __('project::project.hints.client_review') }}
            </p>
        </div>
    </div>
@else
    <div class="text-center py-10 border border-dashed border-gray-300 rounded">
        <i class="bi bi-chat-quote fs-2x text-gray-400 mb-3 d-block"></i>
        <div class="text-muted">{{ __('project::project.messages.no_review') }}</div>
    </div>
@endif
