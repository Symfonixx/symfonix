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
        $this->setActive('cms');
        $this->setActive('testimonials');
    }

    public function index()
    {
        $model = $this->testimonialRepository->all();

        return view('testimonial::admin.testimonial.index', compact('model'));
    }

    public function edit(Testimonial $testimonial)
    {
        $testimonial->load([
            'customer:id,name,email,img',
            'project:id,title,company_id',
            'project.company:id,name',
        ]);

        return view('testimonial::admin.testimonial.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $data = TestimonialData::validate([
            'quote' => $request->input('quote'),
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $data['auto_translate'] = $request->boolean('auto_translate');
        $this->testimonialRepository->update($data, $testimonial);

        return redirect()->route('admin.testimonials.index');
    }

    public function approve(Testimonial $testimonial): RedirectResponse
    {
        $this->testimonialRepository->approve($testimonial);

        return back();
    }

    public function unpublish(Testimonial $testimonial): RedirectResponse
    {
        $this->testimonialRepository->unpublish($testimonial);

        return back();
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->testimonialRepository->deleteMulti($request->input('ids'));

        return back();
    }
}
