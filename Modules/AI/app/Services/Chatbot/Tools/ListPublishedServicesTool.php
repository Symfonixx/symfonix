<?php

namespace Modules\AI\Services\Chatbot\Tools;

use Modules\AI\Contracts\PublicChatTool;
use Modules\AI\Services\Chatbot\PublicServiceCatalog;
use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\ToolResult;

class ListPublishedServicesTool implements PublicChatTool
{
    public function __construct(private readonly PublicServiceCatalog $catalog) {}

    public function name(): string
    {
        return 'list_published_services';
    }

    public function description(): string
    {
        return 'List published public services. Always call this before recommending work. Optional query ranks matches first but every published service is still returned.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Optional search text such as web, mobile, cloud, or AI',
                ],
            ],
        ];
    }

    public function handle(array $arguments, PublicChatContext $context): ToolResult
    {
        $query = isset($arguments['query']) && is_string($arguments['query'])
            ? $arguments['query']
            : null;

        $items = $this->catalog->list($query);

        if ($items === []) {
            return ToolResult::empty('No published services matched.', ['Services']);
        }

        return ToolResult::success([
            'count' => count($items),
            'services' => $items,
        ], ['Services']);
    }
}
