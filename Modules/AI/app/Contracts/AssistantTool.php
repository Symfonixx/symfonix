<?php

namespace Modules\AI\Contracts;

use App\Models\User;
use Modules\AI\Support\ToolResult;

interface AssistantTool
{
    public function name(): string;

    public function description(): string;

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array;

    public function authorized(User $user): bool;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function handle(User $user, array $arguments): ToolResult;
}
