<?php

namespace Modules\AI\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\AI\Http\Requests\Admin\SendAssistantMessageRequest;
use Modules\AI\Models\AiConversation;
use Modules\AI\Services\AiProviderRouter;
use Modules\AI\Services\Assistant\AskSymfonixService;
use Modules\AI\Services\Assistant\ConversationService;
use Modules\AI\Services\Assistant\SuggestionService;

class AssistantController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversations,
        private readonly AskSymfonixService $assistant,
        private readonly SuggestionService $suggestions,
        private readonly AiProviderRouter $providers,
    ) {}

    public function bootstrap(): JsonResponse
    {
        $user = $this->user();

        return response()->json([
            'configured' => $this->providers->isConfigured(),
            'suggestions' => $this->suggestions->forUser($user),
            'conversations' => $this->conversations->listFor($user)->map(fn (AiConversation $conversation) => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'updated_at' => $conversation->updated_at?->toIso8601String(),
            ])->values(),
        ]);
    }

    public function index(): JsonResponse
    {
        $conversations = $this->conversations->listFor($this->user())->map(fn (AiConversation $conversation) => [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'updated_at' => $conversation->updated_at?->toIso8601String(),
        ])->values();

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    public function store(): JsonResponse
    {
        $conversation = $this->conversations->create($this->user());

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'messages' => [],
            ],
        ], 201);
    }

    public function show(int $conversation): JsonResponse
    {
        $owned = $this->ownedConversation($conversation);

        return response()->json([
            'conversation' => [
                'id' => $owned->id,
                'title' => $owned->title,
                'messages' => $this->conversations->visibleMessages($owned),
            ],
        ]);
    }

    public function destroy(int $conversation): JsonResponse
    {
        $owned = $this->ownedConversation($conversation);
        $this->conversations->deleteOwned($this->user(), $owned);

        return response()->json(['success' => true]);
    }

    public function messages(SendAssistantMessageRequest $request, int $conversation): JsonResponse
    {
        $owned = $this->ownedConversation($conversation);
        $result = $this->assistant->reply($this->user(), $owned, $request->validated('message'));

        return $this->messageResponse($owned->fresh(), $result);
    }

    public function regenerate(int $conversation): JsonResponse
    {
        $owned = $this->ownedConversation($conversation);
        $result = $this->assistant->regenerate($this->user(), $owned);

        return $this->messageResponse($owned->fresh(), $result);
    }

    /**
     * @param  array{success: bool, message: mixed, error: ?string}  $result
     */
    private function messageResponse(AiConversation $conversation, array $result): JsonResponse
    {
        $payload = [
            'success' => $result['success'],
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'messages' => $this->conversations->visibleMessages($conversation),
            ],
            'message' => $result['message']
                ? $this->conversations->serializeVisible($result['message'])
                : null,
        ];

        if (! $result['success']) {
            $payload['error'] = $result['error'] ?? __('ai::assistant.errors.connect');
        }

        return response()->json($payload, $result['success'] ? 200 : 422);
    }

    private function ownedConversation(int $id): AiConversation
    {
        $conversation = $this->conversations->findOwned($this->user(), $id);
        abort_if($conversation === null, 404);

        return $conversation;
    }

    private function user(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
