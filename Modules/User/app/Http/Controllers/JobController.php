<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
use Modules\User\Http\Requests\StoreJobApplicationRequest;
use Modules\User\Models\Candidate;
use Modules\User\Models\JobApplication;
use Modules\User\Models\JobPosition;

class JobController extends Controller
{
    public function index()
    {
        $positions = JobPosition::query()
            ->open()
            ->latest('posted_at')
            ->paginate(12)
            ->through(fn (JobPosition $position) => $this->positionData($position));

        return $this->inertia('User::Jobs/Index', [
            'positions' => $positions,
        ], $this->meta(__('Careers'), __('Explore open roles and build your career with us.')));
    }

    public function show(JobPosition $position)
    {
        abort_unless(
            $position->status === JobPosition::STATUS_ACTIVE && $position->posted_at->lessThanOrEqualTo(today()),
            404,
        );

        return $this->inertia('User::Jobs/Show', [
            'position' => $this->positionData($position, detailed: true),
        ], $this->meta($position->title, __('Apply for an open role and join our team.')));
    }

    public function store(StoreJobApplicationRequest $request, JobPosition $position): RedirectResponse
    {
        abort_unless(
            $position->status === JobPosition::STATUS_ACTIVE && $position->posted_at->lessThanOrEqualTo(today()),
            404,
        );

        $validated = $request->validated();
        $resumePath = $request->file('resume')->store('resumes', 'public');

        DB::transaction(function () use ($validated, $resumePath, $position): void {
            $candidate = Candidate::query()->updateOrCreate(
                ['email' => $validated['email']],
                [
                    'full_name' => $validated['full_name'],
                    'phone' => $validated['phone'],
                    'expected_salary' => $validated['expected_salary'] ?? null,
                    'motivation' => $validated['motivation'],
                    'resume_path' => $resumePath,
                    'cover_letter' => $validated['cover_letter'] ?? null,
                ],
            );

            JobApplication::query()->firstOrCreate(
                [
                    'candidate_id' => $candidate->id,
                    'job_position_id' => $position->id,
                ],
                [
                    'status' => JobApplication::STATUS_APPLIED,
                    'submitted_at' => now(),
                ],
            );
        });

        session()->flushMessage(true, __('Your application has been submitted successfully.'));

        return redirect()->route('jobs.show', $position);
    }

    private function positionData(JobPosition $position, bool $detailed = false): array
    {
        $data = [
            'id' => $position->id,
            'slug' => $position->slug,
            'title' => $position->title,
            'department' => $position->department,
            'location' => $position->location,
            'employment_type' => $position->employment_type,
            'posted_at' => $position->posted_at->toDateString(),
        ];

        if ($detailed) {
            $data['description'] = $position->description;
            $data['requirements'] = $position->requirements;
        }

        return $data;
    }

    private function meta(string $title, string $description): array
    {
        $siteName = Seo::get('website_name', config('app.name'));

        return (new Meta)
            ->title($title.' | '.$siteName)
            ->description($description)
            ->canonical(request()->url())
            ->toArray();
    }
}
