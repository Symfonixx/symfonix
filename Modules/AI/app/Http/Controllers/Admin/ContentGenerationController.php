<?php

namespace Modules\AI\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\AI\Http\Requests\Admin\GenerateEditorContentRequest;
use Modules\AI\Http\Requests\Admin\GenerateFormContentRequest;
use Modules\AI\Http\Requests\Admin\GenerateLeadFollowUpRequest;
use Modules\AI\Http\Requests\Admin\GenerateMarketingEmailRequest;
use Modules\AI\Http\Requests\Admin\GenerateQuoteRequest;
use Modules\AI\Services\ContentGenerationService;
use Modules\AI\Services\LeadFollowUpService;
use Modules\AI\Services\MarketingEmailContentService;
use Modules\AI\Services\QuoteGenerationService;
use Modules\CRM\Models\Lead;

class ContentGenerationController extends Controller
{
    public function __construct(
        private readonly ContentGenerationService $contentGenerationService,
        private readonly LeadFollowUpService $leadFollowUpService,
        private readonly QuoteGenerationService $quoteGenerationService,
        private readonly MarketingEmailContentService $marketingEmailContentService,
    ) {}

    public function generate(GenerateEditorContentRequest $request): JsonResponse
    {
        return $this->generationResponse(
            $this->contentGenerationService->generate($request->prompt(), $request->context()),
            'html',
        );
    }

    public function generateForm(GenerateFormContentRequest $request): JsonResponse
    {
        return $this->generationResponse(
            $this->contentGenerationService->generateForm(
                $request->formType(),
                $request->prompt(),
                $request->locale(),
                $request->existing(),
                $request->mode(),
            ),
        );
    }

    public function generateFollowUp(GenerateLeadFollowUpRequest $request, Lead $lead): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $this->generationResponse(
            $this->leadFollowUpService->generate(
                $lead,
                $user,
                $request->prompt(),
                $request->locale(),
            ),
        );
    }

    public function generateQuote(GenerateQuoteRequest $request): JsonResponse
    {
        return $this->generationResponse(
            $this->quoteGenerationService->generate(
                $request->companyId(),
                $request->dealId(),
                $request->prompt(),
                $request->locale(),
            ),
        );
    }

    public function generateMarketingEmail(GenerateMarketingEmailRequest $request): JsonResponse
    {
        return $this->generationResponse(
            $this->marketingEmailContentService->generate(
                $request->marketingGroup(),
                $request->title(),
                $request->goal(),
                $request->prompt(),
                $request->locale(),
            ),
        );
    }

    /**
     * @param  array{success: bool, error?: ?string, provider?: ?string, html?: mixed, fields?: mixed}  $result
     */
    private function generationResponse(array $result, string $payloadKey = 'fields'): JsonResponse
    {
        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? __('ai::content_generation.messages.request_failed'),
            ], 422);
        }

        return response()->json([
            'success' => true,
            $payloadKey => $result[$payloadKey] ?? null,
            'provider' => $result['provider'] ?? null,
        ]);
    }
}
