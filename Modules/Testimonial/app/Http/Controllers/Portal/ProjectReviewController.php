<?php

namespace Modules\Testimonial\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Project\Models\Project;
use Modules\Testimonial\Http\Requests\Portal\StoreProjectReviewRequest;
use Modules\Testimonial\Repositories\TestimonialRepository;

class ProjectReviewController extends Controller
{
    public function __construct(private readonly TestimonialRepository $testimonialRepository) {}

    public function store(StoreProjectReviewRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('view', $project);

        $this->testimonialRepository->storeFromProjectReview(
            $project->id,
            $request->user()->id,
            $request->validated('quote'),
        );

        return back()->with('success', __('user::portal.projects.review_submitted'));
    }
}
