<?php

namespace Modules\AI\Contracts;

use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\ToolResult;

interface PublicChatTool
{
    public function name(): string;

    public function description(): string;

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function handle(array $arguments, PublicChatContext $context): ToolResult;
}
