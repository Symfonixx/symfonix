<?php

namespace Modules\Cms\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cms\Data\FaqData;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Faq;
use Modules\Cms\Repositories\Faq\FaqRepository;
use Modules\Cms\Support\RankConstraint;
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
        $minRank = RankConstraint::minRank(Faq::class);

        return view('cms::admin.faq.create', compact('minRank'));
    }

    public function store(Request $request): RedirectResponse
    {
        $minRank = RankConstraint::minRank(Faq::class);
        $rank = (int) $request->input('rank', $minRank);

        if ($redirect = RankConstraint::rejectIfBelow($rank, $minRank)) {
            return $redirect;
        }

        $this->faqRepository->store($this->payload($request, $rank));

        return redirect()->route('admin.faqs.index');
    }

    public function edit(Faq $faq)
    {
        $minRank = RankConstraint::minRank(Faq::class, $faq->id);

        return view('cms::admin.faq.edit', compact('faq', 'minRank'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $minRank = RankConstraint::minRank(Faq::class, $faq->id);
        $rank = (int) $request->input('rank', $faq->rank);

        if ($redirect = RankConstraint::rejectIfBelow($rank, min((int) $faq->rank, $minRank))) {
            return $redirect;
        }

        $this->faqRepository->update($this->payload($request, $rank), $faq);

        return redirect()->route('admin.faqs.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->faqRepository->deleteMulti($request->input('ids'));

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, int $rank): array
    {
        $data = FaqData::validate([
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
            'rank' => $rank,
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $data['auto_translate'] = $request->boolean('auto_translate');

        return $data;
    }
}
