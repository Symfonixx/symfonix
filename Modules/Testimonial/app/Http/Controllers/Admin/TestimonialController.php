<?php

namespace Modules\Testimonial\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\Testimonial\Data\TestimonialData;
use Modules\Testimonial\Models\Testimonial;
use Modules\Testimonial\Repositories\TestimonialRepository;

class TestimonialController extends Controller
{
    protected TestimonialRepository $testimonialRepository;

    public function __construct(TestimonialRepository $testimonialRepository)
    {
        $this->testimonialRepository = $testimonialRepository;
        $this->setActive('testimonials');
    }

    public function index()
    {
        $model = $this->testimonialRepository->all(['id', 'name', 'position', 'url', 'avatar', 'quote', 'status', 'created_at']);

        return view('testimonial::admin.testimonial.index', compact('model'));
    }

    public function create()
    {
        return view('testimonial::admin.testimonial.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = TestimonialData::validate([
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'url' => $request->input('url'),
            'avatar' => $request->file('avatar'),
            'quote' => $request->input('quote'),
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $this->testimonialRepository->store($data);

        return redirect()->route('admin.testimonials.index');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('testimonial::admin.testimonial.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $data = TestimonialData::validate([
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'url' => $request->input('url'),
            'avatar' => $request->file('avatar'),
            'quote' => $request->input('quote'),
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $this->testimonialRepository->update($data, $testimonial);

        return redirect()->route('admin.testimonials.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->testimonialRepository->deleteMulti($request->input('ids'));

        return back();
    }
}
