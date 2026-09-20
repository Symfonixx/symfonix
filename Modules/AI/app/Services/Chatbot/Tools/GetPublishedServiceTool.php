<?php

namespace Modules\AI\Services\Chatbot\Tools;

use Modules\AI\Contracts\PublicChatTool;
use Modules\AI\Services\Chatbot\PublicServiceCatalog;
use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\ToolResult;

class GetPublishedServiceTool implements PublicChatTool
{
    public function __construct(private readonly PublicServiceCatalog $catalog) {}

    public function name(): string
    {
        return 'get_published_service';
    }

    public function description(): string
    {
        return 'Get details for one published service by id, slug, or name. Use list_published_services first if you only have a vague need.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'service_id' => [
                    'type' => 'integer',
                    'description' => 'Published service id',
                ],
                'slug' => [
                    'type' => 'string',
                    'description' => 'Service URL slug',
                ],
                'query' => [
                    'type' => 'string',
                    'description' => 'Service title or keywords when id/slug are unknown',
                ],
            ],
        ];
    }

    public function handle(array $arguments, PublicChatContext $context): ToolResult
    {
        $id = isset($arguments['service_id']) ? (int) $arguments['service_id'] : null;
        $slug = isset($arguments['slug']) && is_string($arguments['slug']) ? $arguments['slug'] : null;
        $query = isset($arguments['query']) && is_string($arguments['query']) ? $arguments['query'] : null;

        $service = $this->catalog->find($id, $slug, $query);

        if ($service === null) {
            return ToolResult::empty('No matching published service was found.', ['Services']);
        }

        if (isset($service['id'])) {
            $context->serviceId = (int) $service['id'];
        }

        return ToolResult::success($service, ['Services']);
    }
}
