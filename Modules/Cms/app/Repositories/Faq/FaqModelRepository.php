<?php

namespace Modules\Cms\Repositories\Faq;

use Config;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Faq;
use Modules\Core\Traits\ExceptionHandlerTrait;

class FaqModelRepository implements FaqRepository
{
    use ExceptionHandlerTrait;

    public function all(array $columns = ['*']): LengthAwarePaginator
    {
        $request = request();

        return Faq::select($columns)->ordered()->paginate(Config::get('core.page_size', 10));
    }

    public function find(int $id, array $columns = ['*']): ?Faq
    {
        return Faq::find($id, $columns);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            $faqData = $this->prepareFaqData($data);
            Faq::create($faqData);
            $this->clearFaqCache();
            session()->flushMessage(true);
        });
    }

    private function prepareFaqData(array $data): array
    {
        $transQuestion = [app()->getLocale() => $data['question']];
        $transAnswer = [app()->getLocale() => $data['answer']];

        foreach (otherLangs() as $lang) {
            try {
                $transQuestion[$lang] = autoGoogleTranslator($lang, $data['question'] ?? '');
                $transAnswer[$lang] = autoGoogleTranslator($lang, $data['answer'] ?? '');
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return array_merge($data, [
            'question' => $transQuestion,
            'answer' => $transAnswer,
            'rank' => $data['rank'] ?? 0,
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    /**
     * Prepare FAQ data for update without auto translation.
     * Only the current locale is updated; other locales are kept as-is.
     */
    private function prepareFaqUpdateData(array $data, Faq $faq): array
    {
        $locale = app()->getLocale();

        $transQuestion = $faq->getTranslations('question');
        $transAnswer = $faq->getTranslations('answer');

        $transQuestion[$locale] = $data['question'] ?? ($transQuestion[$locale] ?? '');
        $transAnswer[$locale] = $data['answer'] ?? ($transAnswer[$locale] ?? '');

        return array_merge($data, [
            'question' => $transQuestion,
            'answer' => $transAnswer,
            'rank' => $data['rank'] ?? $faq->rank,
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    private function clearFaqCache(): void
    {
        cache()->forget('faqs');
    }

    public function update(array $data, Faq $faq): mixed
    {
        return $this->execute(function () use ($data, $faq) {
            $faqData = $this->prepareFaqUpdateData($data, $faq);
            $faq->update($faqData);
            $this->clearFaqCache();
            session()->flushMessage(true);

            return true;
        });
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            Faq::destroy($ids);
            $this->clearFaqCache();
            session()->flushMessage(true);

            return true;
        });
    }
}
