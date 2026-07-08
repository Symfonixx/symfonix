<?php

namespace Modules\Testimonial\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Log;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Testimonial\Models\Testimonial;

class TestimonialRepository
{
    use ExceptionHandlerTrait;

    public function all(array $columns = ['*']): Collection
    {
        return Testimonial::query()
            ->withDisplayRelations()
            ->latest()
            ->get();
    }

    public function find($id): ?Testimonial
    {
        return Testimonial::query()->withDisplayRelations()->find($id);
    }

    public function update(array $data, Testimonial $testimonial): mixed
    {
        return $this->execute(function () use ($data, $testimonial) {
            $locale = app()->getLocale();
            $transQuote = $testimonial->getTranslations('quote');
            $transQuote[$locale] = $data['quote'] ?? ($transQuote[$locale] ?? '');

            $testimonial->update([
                'quote' => $transQuote,
                'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
            ]);

            session()->flushMessage(true);

            return true;
        });
    }

    public function storeFromProjectReview(int $projectId, int $customerId, string $quote, string $status = 'Published'): Testimonial
    {
        $transQuote = [app()->getLocale() => $quote];

        foreach (otherLangs() as $lang) {
            try {
                $transQuote[$lang] = autoGoogleTranslator($lang, $quote);
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $transQuote[$lang] = $quote;
            }
        }

        return Testimonial::create([
            'project_id' => $projectId,
            'customer_id' => $customerId,
            'quote' => $transQuote,
            'status' => $status,
        ]);
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            Testimonial::destroy($ids);
            session()->flushMessage(true);

            return true;
        });
    }
}
