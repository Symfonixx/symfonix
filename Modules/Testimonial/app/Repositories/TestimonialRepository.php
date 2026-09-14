<?php

namespace Modules\Testimonial\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Testimonial\Events\TestimonialSubmitted;
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
            $translations = buildTranslations([
                'quote' => $data['quote'] ?? '',
            ], wantsAutoTranslate($data), [
                'quote' => $testimonial->getTranslations('quote'),
            ]);

            $testimonial->update([
                'quote' => $translations['quote'],
                'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
            ]);

            session()->flushMessage(true);

            return true;
        });
    }

    /**
     * Client reviews start as Archived until an admin approves them for the website.
     */
    public function storeFromProjectReview(int $projectId, int $customerId, string $quote, string $status = 'Archived'): Testimonial
    {
        $transQuote = [app()->getLocale() => $quote];

        foreach (otherLangs() as $lang) {
            try {
                $transQuote[$lang] = autoGoogleTranslator($lang, $quote);
            } catch (\Throwable $e) {
                Log::warning('Auto-translate failed for testimonial quote.', [
                    'locale' => $lang,
                    'error' => $e->getMessage(),
                ]);
                $transQuote[$lang] = $quote;
            }
        }

        $testimonial = Testimonial::create([
            'project_id' => $projectId,
            'customer_id' => $customerId,
            'quote' => $transQuote,
            'status' => $status,
        ]);

        if ($status === CmsStatus::ARCHIVED->value) {
            event(new TestimonialSubmitted($testimonial));
        }

        return $testimonial;
    }

    public function approve(Testimonial $testimonial): mixed
    {
        return $this->execute(function () use ($testimonial) {
            $testimonial->update(['status' => CmsStatus::PUBLISHED->value]);
            session()->flushMessage(true);

            return true;
        });
    }

    public function unpublish(Testimonial $testimonial): mixed
    {
        return $this->execute(function () use ($testimonial) {
            $testimonial->update(['status' => CmsStatus::ARCHIVED->value]);
            session()->flushMessage(true);

            return true;
        });
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
