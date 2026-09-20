<?php

namespace Modules\AI\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\AI\Http\Requests\Admin\GenerateLeadFollowUpRequest;
use Modules\AI\Http\Requests\Admin\GenerateMarketingEmailRequest;
use Modules\AI\Http\Requests\Admin\GenerateQuoteRequest;
use Modules\AI\Services\ContentGenerationService;
use Modules\AI\Services\LeadFollowUpService;
use Modules\AI\Services\MarketingEmailContentService;
use Modules\AI\Services\QuoteGenerationService;
use Modules\AI\Support\FormContentSchema;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\MarketingGroup;

class ContentGenerationController extends Controller
{
    public function __construct(
        private readonly ContentGenerationService $contentGenerationService,
        private readonly LeadFollowUpService $leadFollowUpService,
        private readonly QuoteGenerationService $quoteGenerationService,
        private readonly MarketingEmailContentService $marketingEmailContentService,
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'context' => ['nullable', 'string', 'max:8000'],
        ]);

        $result = $this->contentGenerationService->generate($validated['prompt'], $validated['context'] ?? null);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'html' => $result['html'],
            'provider' => $result['provider'],
        ]);
    }

    public function generateForm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'form_type' => ['required', 'string', Rule::in(FormContentSchema::types())],
            'locale' => ['nullable', 'string', 'max:12'],
            'existing' => ['nullable', 'array'],
            'existing.*' => ['nullable', 'string', 'max:2000'],
        ]);

        $result = $this->contentGenerationService->generateForm(
            $validated['form_type'],
            $validated['prompt'],
            $validated['locale'] ?? app()->getLocale(),
            $validated['existing'] ?? null,
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'fields' => $result['fields'],
            'provider' => $result['provider'],
        ]);
    }

    public function generateFollowUp(GenerateLeadFollowUpRequest $request, Lead $lead): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $validated = $request->validated();

        $result = $this->leadFollowUpService->generate(
            $lead,
            $user,
            $validated['prompt'] ?? null,
            $validated['locale'] ?? app()->getLocale(),
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'fields' => $result['fields'],
            'provider' => $result['provider'],
        ]);
    }

    public function generateQuote(GenerateQuoteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->quoteGenerationService->generate(
            (int) $validated['company_id'],
            (int) $validated['deal_id'],
            $validated['prompt'] ?? null,
            $validated['locale'] ?? app()->getLocale(),
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'fields' => $result['fields'],
            'provider' => $result['provider'],
        ]);
    }

    public function generateMarketingEmail(GenerateMarketingEmailRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $group = isset($validated['marketing_group_id'])
            ? MarketingGroup::query()->find($validated['marketing_group_id'])
            : null;

        $result = $this->marketingEmailContentService->generate(
            $group,
            $validated['title'] ?? null,
            $validated['goal'] ?? null,
            $validated['prompt'] ?? null,
            $validated['locale'] ?? app()->getLocale(),
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'fields' => $result['fields'],
            'provider' => $result['provider'],
        ]);
    }
}
