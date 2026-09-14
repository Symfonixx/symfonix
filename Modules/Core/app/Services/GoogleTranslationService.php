<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

class GoogleTranslationService
{
    private ?GoogleTranslate $translator = null;

    private float $lastRequestAt = 0.0;

    /**
     * @throws LargeTextException
     * @throws RateLimitException
     * @throws TranslationRequestException
     */
    public function translate(string $targetLang, string $content, ?string $sourceLang = null): string
    {
        $content = trim($content);

        if ($content === '') {
            return '';
        }

        $sourceLang ??= app()->getLocale();

        if ($sourceLang === $targetLang) {
            return $content;
        }

        $cacheKey = $this->cacheKey($sourceLang, $targetLang, $content);
        $cached = Cache::get($cacheKey);

        if (is_string($cached)) {
            return $cached;
        }

        $this->throttle();

        $attempts = max(1, (int) config('core.translation.retry_attempts', 3));
        $baseDelayMs = max(100, (int) config('core.translation.retry_delay_ms', 1500));

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $translation = $this->translator()
                    ->setSource($sourceLang)
                    ->setTarget($targetLang)
                    ->translate($content);

                $result = $translation ?? $content;

                Cache::put(
                    $cacheKey,
                    $result,
                    now()->addSeconds((int) config('core.translation.cache_ttl', 604800))
                );

                return $result;
            } catch (RateLimitException|TranslationRequestException $exception) {
                if (! $this->isRateLimited($exception) || $attempt >= $attempts) {
                    throw $exception;
                }

                $delayMs = $baseDelayMs * (2 ** ($attempt - 1));
                Log::warning('Google Translate rate limited, retrying.', [
                    'attempt' => $attempt,
                    'delay_ms' => $delayMs,
                    'source' => $sourceLang,
                    'target' => $targetLang,
                ]);

                usleep($delayMs * 1000);
                $this->resetTranslator();
            }
        }

        return $content;
    }

    private function translator(): GoogleTranslate
    {
        if ($this->translator === null) {
            $this->translator = new GoogleTranslate(
                options: [
                    'timeout' => 30,
                    'connect_timeout' => 10,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    ],
                ]
            );
        }

        return $this->translator;
    }

    private function resetTranslator(): void
    {
        $this->translator = null;
    }

    private function throttle(): void
    {
        $throttleMs = max(0, (int) config('core.translation.throttle_ms', 500));

        if ($throttleMs === 0) {
            return;
        }

        $now = microtime(true);
        $elapsedMs = ($now - $this->lastRequestAt) * 1000;

        if ($this->lastRequestAt > 0 && $elapsedMs < $throttleMs) {
            usleep((int) (($throttleMs - $elapsedMs) * 1000));
        }

        $this->lastRequestAt = microtime(true);
    }

    private function cacheKey(string $sourceLang, string $targetLang, string $content): string
    {
        return 'google_translate:'.hash('xxh128', $sourceLang.'|'.$targetLang.'|'.$content);
    }

    private function isRateLimited(Throwable $exception): bool
    {
        if ($exception instanceof RateLimitException) {
            return true;
        }

        $message = strtolower($exception->getMessage());

        return str_contains($message, '429')
            || str_contains($message, 'too many requests')
            || str_contains($message, '503');
    }
}
