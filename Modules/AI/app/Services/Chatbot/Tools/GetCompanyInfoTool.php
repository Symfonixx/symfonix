<?php

namespace Modules\AI\Services\Chatbot\Tools;

use Modules\AI\Contracts\PublicChatTool;
use Modules\AI\Support\CompanyContentProfile;
use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\ToolResult;

class GetCompanyInfoTool implements PublicChatTool
{
    public function name(): string
    {
        return 'get_company_info';
    }

    public function description(): string
    {
        return 'Get public company name, about text, and contact details for visitors.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
        ];
    }

    public function handle(array $arguments, PublicChatContext $context): ToolResult
    {
        $facts = array_filter(CompanyContentProfile::facts(), fn ($value) => is_string($value) && $value !== '');

        if ($facts === []) {
            return ToolResult::empty('No public company profile is configured yet.', ['Company']);
        }

        return ToolResult::success($facts, ['Company']);
    }
}
