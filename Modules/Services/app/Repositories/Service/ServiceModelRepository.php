<?php

namespace Modules\Services\Repositories\Service;

use Config;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\Services\Models\Service;
use Modules\Support\app\Services\SubscriberNotificationService;

class ServiceModelRepository implements ServiceRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $serviceUploadPath = 'services';

    public function all(array $columns = ['*']): LengthAwarePaginator
    {
        return Service::select($columns)
            ->with('category')
            ->latest()
            ->paginate(Config::get('core.page_size', 10));
    }

    public function find(int $id, array $columns = ['*']): ?Service
    {
        return Service::find($id, $columns);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            $serviceData = $this->prepareServiceData($data);
            $service = Service::create($serviceData);
            $this->clearServiceCache();
            app(SubscriberNotificationService::class)->notifyNewService($service);
            session()->flushMessage(true);
        });
    }

    private function prepareServiceData(array $data, ?string $existingImage = null): array
    {
        $path = $this->handleImageUpload($data, $existingImage);
        $transTitle = [app()->getLocale() => $data['title']];
        $transDesc = [app()->getLocale() => $data['description'] ?? ''];
        $transContent = [app()->getLocale() => $data['content']];
        $transKeywords = [app()->getLocale() => $data['keywords'] ?? ''];
        foreach (otherLangs() as $lang) {
            try {
                $transTitle[$lang] = autoGoogleTranslator($lang, $data['title'] ?? '');
                $transDesc[$lang] = autoGoogleTranslator($lang, $data['description'] ?? '');
                $transContent[$lang] = autoGoogleTranslator($lang, $data['content'] ?? '');
                $transKeywords[$lang] = autoGoogleTranslator($lang, $data['keywords'] ?? '');
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return array_merge($data, [
            'image' => $path,
            'title' => $transTitle,
            'description' => $transDesc,
            'content' => $transContent,
            'keywords' => $transKeywords,
            'service_category_id' => $data['service_category_id'] ?? null,
        ]);
    }

    /**
     * Prepare service data for update without auto translation.
     * Only the current locale is updated; other locales are kept as-is.
     */
    private function prepareServiceUpdateData(array $data, Service $service): array
    {
        $path = $this->handleImageUpload($data, $service->image);

        $locale = app()->getLocale();

        $transTitle = $service->getTranslations('title');
        $transDesc = $service->getTranslations('description');
        $transContent = $service->getTranslations('content');
        $transKeywords = $service->getTranslations('keywords');

        $transTitle[$locale] = $data['title'] ?? ($transTitle[$locale] ?? '');
        $transDesc[$locale] = $data['description'] ?? ($transDesc[$locale] ?? '');
        $transContent[$locale] = $data['content'] ?? ($transContent[$locale] ?? '');
        $transKeywords[$locale] = $data['keywords'] ?? ($transKeywords[$locale] ?? '');

        return array_merge($data, [
            'image' => $path,
            'title' => $transTitle,
            'description' => $transDesc,
            'content' => $transContent,
            'keywords' => $transKeywords,
            'service_category_id' => $data['service_category_id'] ?? $service->service_category_id,
        ]);
    }

    private function handleImageUpload(array $data, ?string $existingImage = null): ?string
    {
        return $data['image']
            ? $this->upload($data['image'], $this->serviceUploadPath, $data['slug'], $existingImage)
            : $existingImage;
    }

    public function update(array $data, Service $service): mixed
    {
        return $this->execute(function () use ($data, $service) {
            $serviceData = $this->prepareServiceUpdateData($data, $service);
            $service->update($serviceData);
            $this->clearServiceCache();
            session()->flushMessage(true);

            return true;
        });
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $images = Service::whereIn('id', $ids)->pluck('image')->filter()->toArray();
            Service::destroy($ids);
            $this->deleteFile($images);
            $this->clearServiceCache();
            session()->flushMessage(true);

            return true;
        });
    }

     private function clearServiceCache(): void
    {
        cache()->forget('services');
        cache()->forget('home_services');
    }
}
