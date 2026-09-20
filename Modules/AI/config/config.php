<?php

use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Page;
use Modules\Product\Models\Product;
use Modules\Project\Models\ProjectUseCase;

return [

    /*
    |--------------------------------------------------------------------------
    | Ask Symfonix AI
    |--------------------------------------------------------------------------
    |
    | Cost and routing controls for the admin business assistant. The active
    | provider can be overridden from Settings → Integrations.
    |
    */
    'assistant' => [
        'provider' => env('AI_ASSISTANT_PROVIDER', 'auto'),
        'max_history_messages' => (int) env('AI_ASSISTANT_MAX_HISTORY', 16),
        'max_tool_rounds' => (int) env('AI_ASSISTANT_MAX_TOOL_ROUNDS', 3),
        'max_list_items' => (int) env('AI_ASSISTANT_MAX_LIST_ITEMS', 12),
        'max_record_chars' => (int) env('AI_ASSISTANT_MAX_RECORD_CHARS', 1200),
        'temperature' => (float) env('AI_ASSISTANT_TEMPERATURE', 0.3),
        'max_tokens' => (int) env('AI_ASSISTANT_MAX_TOKENS', 1200),
        'timeout' => (int) env('AI_ASSISTANT_TIMEOUT', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public website chatbot
    |--------------------------------------------------------------------------
    |
    | BotMan uses the same OpenAI/Gemini providers as Ask Symfonix AI, with a
    | public-safe tool set (services, company profile, lead capture).
    |
    */
    'chatbot' => [
        'enabled' => filter_var(env('AI_CHATBOT_ENABLED', true), FILTER_VALIDATE_BOOL),
        'max_history_messages' => (int) env('AI_CHATBOT_MAX_HISTORY', 12),
        'max_tool_rounds' => (int) env('AI_CHATBOT_MAX_TOOL_ROUNDS', 3),
        'max_tokens' => (int) env('AI_CHATBOT_MAX_TOKENS', 2048),
        'temperature' => (float) env('AI_CHATBOT_TEMPERATURE', 0.4),
        'timeout' => (int) env('AI_CHATBOT_TIMEOUT', 45),
        'rate_limit' => (int) env('AI_CHATBOT_RATE_LIMIT', 20),
        'rate_decay' => (int) env('AI_CHATBOT_RATE_DECAY', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Editable Image Targets
    |--------------------------------------------------------------------------
    |
    | Registry of models/fields that the generic "Edit with AI" component and
    | its backing endpoints are allowed to read from and write to. Add an
    | entry here for every module that wants to expose AI image editing on
    | one of its image fields.
    |
    | - model: fully-qualified Eloquent model class
    | - fields: image column names that may be edited
    | - disk: filesystem disk the current image is stored/read on
    | - permission: Spatie permission the acting user must have
    |
    */
    'editable_targets' => [
        'product' => [
            'model' => Product::class,
            'fields' => ['main_image', 'seo_meta_img'],
            'disk' => 'public',
            'permission' => 'product.catalog.edit',
        ],
        'cms_page' => [
            'model' => Page::class,
            'fields' => ['image'],
            'disk' => 'public',
            'permission' => 'cms.pages.edit',
        ],
        'cms_blog' => [
            'model' => Blog::class,
            'fields' => ['image'],
            'disk' => 'public',
            'permission' => 'cms.blogs.edit',
        ],
        'use_case' => [
            'model' => ProjectUseCase::class,
            'fields' => ['image'],
            'disk' => 'public',
            'permission' => 'project.use_cases.edit',
        ],
    ],

];
