<?php

namespace Modules\Cms\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cms\Data\FaqData;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Faq;
use Modules\Cms\Repositories\Faq\FaqRepository;
use Modules\Core\Http\Requests\DeleteMultiRequest;

class FaqController extends Controller
{
    protected FaqRepository $faqRepository;

    public function __construct(FaqRepository $faqRepository)
    {
        $this->faqRepository = $faqRepository;
        $this->setActive('cms');
        $this->setActive('faqs');
    }

    public function index()
    {
        $model = $this->faqRepository->all([
            'id', 'question', 'answer', 'rank', 'status', 'created_at',
        ]);

        return view('cms::admin.faq.index', compact('model'));
    }

    public function create()
    {
        $maxRank = Faq::max('rank') ?? 0;
        $minRank = max($maxRank, 1);

        return view('cms::admin.faq.create', compact('minRank'));
    }

    public function store(Request $request): RedirectResponse
    {
        $maxRank = Faq::max('rank') ?? 0;
        $minRank = max($maxRank, 1);
        $rank = (int) $request->input('rank', $minRank);

        if ($rank < $minRank) {
            return back()->withErrors(['rank' => __('Rank must be at least :min', ['min' => $minRank])])->withInput();
        }

        $data = FaqData::validate([
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
            'rank' => $rank,
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $this->faqRepository->store($data);

        return redirect()->route('admin.faqs.index');
    }

    public function edit(Faq $faq)
    {
        $maxRank = Faq::where('id', '!=', $faq->id)->max('rank') ?? 0;
        $minRank = max($maxRank, 1);

        return view('cms::admin.faq.edit', compact('faq', 'minRank'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $maxRank = Faq::where('id', '!=', $faq->id)->max('rank') ?? 0;
        $minRank = max($maxRank, 1);
        $rank = (int) $request->input('rank', $faq->rank);

        // Allow current rank or higher
        if ($rank < min($faq->rank, $minRank)) {
            return back()->withErrors(['rank' => __('Rank must be at least :min', ['min' => min($faq->rank, $minRank)])])->withInput();
        }

        $data = FaqData::validate([
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
            'rank' => $rank,
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $this->faqRepository->update($data, $faq);

        return redirect()->route('admin.faqs.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->faqRepository->deleteMulti($request->input('ids'));

        return back();
    }
}
