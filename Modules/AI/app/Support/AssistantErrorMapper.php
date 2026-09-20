<?php

namespace Modules\AI\Support;

final class AssistantErrorMapper
{
    public static function toUserMessage(?string $error = null): string
    {
        $error = strtolower((string) $error);

        if ($error === '') {
            return __('ai::assistant.errors.connect');
        }

        if (str_contains($error, 'not_configured') || str_contains($error, 'api key') || str_contains($error, 'unauthenticated')) {
            return __('ai::assistant.errors.not_configured');
        }

        if (str_contains($error, 'rate') || str_contains($error, '429')) {
            return __('ai::assistant.errors.rate_limit');
        }

        if (str_contains($error, 'timeout') || str_contains($error, 'timed out')) {
            return __('ai::assistant.errors.connect');
        }

        if (str_contains($error, __('ai::assistant.errors.connect'))
            || str_contains($error, __('ai::assistant.errors.not_configured'))
            || str_contains($error, __('ai::assistant.errors.rate_limit'))
            || str_contains($error, __('ai::assistant.errors.empty'))
            || str_contains($error, __('ai::assistant.errors.incomplete'))
        ) {
            return (string) $error;
        }

        return __('ai::assistant.errors.connect');
    }
}
