<?php

namespace Modules\AI\Support;

final class PublicChatContext
{
    /**
     * @param  list<array{role: string, message: string, meta?: array<string, mixed>, at?: string}>  $transcript
     * @param  list<array{label: string, value: string}>  $quickReplies
     */
    public function __construct(
        public readonly ?string $botmanUserId = null,
        public readonly ?string $botmanDriver = null,
        public readonly string $locale = 'en',
        public readonly ?string $ipAddress = null,
        public array $transcript = [],
        public ?int $leadId = null,
        public ?int $serviceId = null,
        public ?string $problemStatement = null,
        public array $quickReplies = [],
    ) {}

    public function leadAlreadyCaptured(): bool
    {
        return $this->leadId !== null;
    }
}
