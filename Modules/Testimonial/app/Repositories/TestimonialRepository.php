<?php

namespace Modules\Testimonial\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Log;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\Testimonial\Models\Testimonial;

class TestimonialRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $avatarUploadPath = 'testimonials';

    public function all(array $columns = ['*']): Collection
    {
        return Testimonial::all($columns);
    }

    public function find($id): ?Testimonial
    {
        return Testimonial::find($id);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            $testimonialData = $this->prepareTestimonialData($data);
            Testimonial::create($testimonialData);
            session()->flushMessage(true);
        });
    }

    private function prepareTestimonialData(array $data, ?string $existingImage = null): array
    {
        $path = $this->handleImageUpload($data, $existingImage);
        $transName = [app()->getLocale() => $data['name']];
        $transPosition = [app()->getLocale() => $data['position'] ?? ''];
        $transQuote = [app()->getLocale() => $data['quote']];
        foreach (otherLangs() as $lang) {
            try {
                $transName[$lang] = autoGoogleTranslator($lang, $data['name'] ?? '');
                $transPosition[$lang] = autoGoogleTranslator($lang, $data['position'] ?? '');
                $transQuote[$lang] = autoGoogleTranslator($lang, $data['quote'] ?? '');
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return array_merge($data, [
            'avatar' => $path,
            'name' => $transName,
            'position' => $transPosition,
            'quote' => $transQuote,
            // Persist status as enum string into the status column
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    /**
     * Prepare testimonial data for update without auto translation.
     * Only the current locale is updated; other locales are preserved.
     */
    private function prepareTestimonialUpdateData(array $data, Testimonial $testimonial): array
    {
        $path = $this->handleImageUpload($data, $testimonial->avatar);

        $locale = app()->getLocale();

        $transName = $testimonial->getTranslations('name');
        $transPosition = $testimonial->getTranslations('position');
        $transQuote = $testimonial->getTranslations('quote');

        $transName[$locale] = $data['name'] ?? ($transName[$locale] ?? '');
        $transPosition[$locale] = $data['position'] ?? ($transPosition[$locale] ?? '');
        $transQuote[$locale] = $data['quote'] ?? ($transQuote[$locale] ?? '');

        return array_merge($data, [
            'avatar' => $path,
            'name' => $transName,
            'position' => $transPosition,
            'quote' => $transQuote,
            // Persist status as enum string into the status column
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    private function handleImageUpload(array $data, ?string $existingImage = null): ?string
    {
        return $data['avatar']
            ? $this->upload($data['avatar'], $this->avatarUploadPath, null, $existingImage)
            : $existingImage;
    }

    public function update(array $data, Testimonial $testimonial): mixed
    {
        return $this->execute(function () use ($data, $testimonial) {
            $testimonialData = $this->prepareTestimonialUpdateData($data, $testimonial);
            $testimonial->update($testimonialData);
            session()->flushMessage(true);

            return true;
        });
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $images = Testimonial::whereIn('id', $ids)->pluck('avatar')->filter()->toArray();
            Testimonial::destroy($ids);
            $this->deleteFile($images);
            session()->flushMessage(true);

            return true;
        });
    }
}
