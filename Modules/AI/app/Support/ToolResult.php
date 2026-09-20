<?php

namespace Modules\AI\Support;

final class ToolResult
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $sources
     */
    public function __construct(
        public readonly bool $ok,
        public readonly array $data,
        public readonly array $sources = [],
        public readonly bool $denied = false,
        public readonly ?string $error = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $sources
     */
    public static function success(array $data, array $sources = []): self
    {
        return new self(true, $data, $sources);
    }

    public static function denied(string $reason): self
    {
        return new self(false, ['reason' => $reason], [], true, $reason);
    }

    public static function empty(string $message, array $sources = []): self
    {
        return new self(true, ['empty' => true, 'message' => $message], $sources);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'ok' => $this->ok,
            'denied' => $this->denied,
            'data' => $this->data,
            'sources' => $this->sources,
        ];

        if ($this->error !== null) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
