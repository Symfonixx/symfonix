<?php

namespace Modules\Cms\Repositories\Blog;

use Config;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Blog;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\Support\app\Services\SubscriberNotificationService;

class BlogModelRepository implements BlogRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $blogUploadPath = 'blogs';

    public function all(array $columns = ['*']): LengthAwarePaginator
    {
        $request = request();

        return Blog::select($columns)->latest()->paginate(Config::get('core.page_size', 10));
    }

    public function find(int $id, array $columns = ['*']): ?Blog
    {
        return Blog::find($id, $columns);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            $blogData = $this->prepareBlogData($data);
            $blog = Blog::create($blogData);
            $this->clearBlogCache();
            app(SubscriberNotificationService::class)->notifyNewBlog($blog);
            session()->flushMessage(true);
        });
    }

    private function prepareBlogData(array $data, ?string $existingImage = null): array
    {
        $path = $this->handleImageUpload($data, $existingImage);
        $keywords = $this->parseKeywords($data['keywords']);
        $transTitle = [app()->getLocale() => $data['title']];

        $transDesc = [app()->getLocale() => $data['description']];
        $transKeywords = [app()->getLocale() => $keywords];
        $transContent = [app()->getLocale() => $data['content']];
        foreach (otherLangs() as $lang) {
            try {
                $transTitle[$lang] = autoGoogleTranslator($lang, $data['title'] ?? '');
                $transDesc[$lang] = autoGoogleTranslator($lang, $data['description'] ?? '');
                $transKeywords[$lang] = autoGoogleTranslator($lang, $keywords ?? '');
                $transContent[$lang] = autoGoogleTranslator($lang, $data['content'] ?? '');
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
            // Persist status to the correct column as a string value.
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
            'featured' => (int) $data['featured'],
        ]);
    }

    /**
     * Prepare blog data for update without auto translation.
     * Only the current locale is updated; other locales are kept as-is.
     */
    private function prepareBlogUpdateData(array $data, Blog $blog): array
    {
        $path = $this->handleImageUpload($data, $blog->image);
        $keywords = $this->parseKeywords($data['keywords'] ?? null);

        $locale = app()->getLocale();

        $transTitle = $blog->getTranslations('title');
        $transDesc = $blog->getTranslations('description');
        $transKeywords = $blog->getTranslations('keywords');
        $transContent = $blog->getTranslations('content');

        $transTitle[$locale] = $data['title'] ?? ($transTitle[$locale] ?? '');
        $transDesc[$locale] = $data['description'] ?? ($transDesc[$locale] ?? '');
        $transKeywords[$locale] = $keywords ?? ($transKeywords[$locale] ?? '');
        $transContent[$locale] = $data['content'] ?? ($transContent[$locale] ?? '');

        return array_merge($data, [
            'image' => $path,
            'title' => $transTitle,
            'description' => $transDesc,
            'content' => $transContent,
            'keywords' => $transKeywords,
            // Persist status to the correct column as a string value.
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
            'featured' => (int) $data['featured'],
        ]);
    }

    private function handleImageUpload(array $data, ?string $existingImage = null): ?string
    {
        return $data['image']
            ? $this->upload($data['image'], $this->blogUploadPath, $data['slug'], $existingImage)
            : $existingImage;
    }

    private function parseKeywords(?string $keywordsInput): string
    {
        if (! $keywordsInput) {
            return '';
        }
        $decoded = json_decode($keywordsInput, true);

        return $decoded ? implode(', ', array_column($decoded, 'value')) : '';
    }

    private function clearBlogCache(): void
    {
        cache()->forget('blogs');
        cache()->forget('home_blogs');
    }

    public function update(array $data, Blog $blog): mixed
    {
        return $this->execute(function () use ($data, $blog) {
            $blogData = $this->prepareBlogUpdateData($data, $blog);
            $blog->update($blogData);
            $this->clearBlogCache();
            session()->flushMessage(true);

            return true;
        });
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $images = Blog::whereIn('id', $ids)->pluck('image')->filter()->toArray();
            Blog::destroy($ids);
            $this->deleteFile($images);
            $this->clearBlogCache();
            session()->flushMessage(true);

            return true;
        });
    }
}
