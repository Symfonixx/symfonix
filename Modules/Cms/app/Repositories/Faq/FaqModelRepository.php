<?php

namespace Modules\Cms\Repositories\Faq;

use Config;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $autoTranslate = wantsAutoTranslate($data);
        $translations = buildTranslations([
            'question' => $data['question'] ?? '',
            'answer' => $data['answer'] ?? '',
        ], $autoTranslate);

        return array_merge($data, $translations, [
            'rank' => $data['rank'] ?? 0,
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    private function prepareFaqUpdateData(array $data, Faq $faq): array
    {
        $autoTranslate = wantsAutoTranslate($data);
        $translations = buildTranslations([
            'question' => $data['question'] ?? '',
            'answer' => $data['answer'] ?? '',
        ], $autoTranslate, [
            'question' => $faq->getTranslations('question'),
            'answer' => $faq->getTranslations('answer'),
        ]);

        return array_merge($data, $translations, [
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
