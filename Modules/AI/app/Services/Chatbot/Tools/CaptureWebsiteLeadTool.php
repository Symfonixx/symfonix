<?php

namespace Modules\AI\Services\Chatbot\Tools;

use App\Notifications\NewLeadNotification;
use Illuminate\Support\Facades\Notification;
use Modules\AI\Contracts\PublicChatTool;
use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\ToolResult;
use Modules\Base\Support\AdminEmail;
use Modules\CRM\Models\Lead;
use Modules\Services\Models\Service;

class CaptureWebsiteLeadTool implements PublicChatTool
{
    public function name(): string
    {
        return 'capture_website_lead';
    }

    public function description(): string
    {
        return 'Save a website chat lead after you have the visitor full name and a valid email. Optional: company, phone, budget, problem, and service_id.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'Visitor full name',
                ],
                'email' => [
                    'type' => 'string',
                    'description' => 'Visitor email address',
                ],
                'company_name' => [
                    'type' => 'string',
                    'description' => 'Company name if shared',
                ],
                'phone' => [
                    'type' => 'string',
                    'description' => 'Phone number if shared',
                ],
                'budget' => [
                    'type' => 'string',
                    'description' => 'Estimated project budget if shared',
                ],
                'problem_statement' => [
                    'type' => 'string',
                    'description' => 'What the visitor wants to solve',
                ],
                'service_id' => [
                    'type' => 'integer',
                    'description' => 'Published service id that fits the request',
                ],
            ],
            'required' => ['name', 'email'],
        ];
    }

    public function handle(array $arguments, PublicChatContext $context): ToolResult
    {
        $name = trim((string) ($arguments['name'] ?? ''));
        $email = trim((string) ($arguments['email'] ?? ''));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ToolResult::denied('A full name and a valid email are required before saving a lead.');
        }

        $company = $this->optionalString($arguments['company_name'] ?? null);
        $phone = $this->optionalString($arguments['phone'] ?? null);
        $budget = $this->optionalString($arguments['budget'] ?? null);
        $problem = $this->optionalString($arguments['problem_statement'] ?? null) ?? $context->problemStatement;
        $service = $this->publishedService(isset($arguments['service_id']) ? (int) $arguments['service_id'] : $context->serviceId);

        if ($problem) {
            $context->problemStatement = $problem;
        }

        $payload = [
            'name' => $name,
            'email' => $email,
            'company_name' => $company,
            'phone' => $phone,
            'source' => Lead::SOURCE_WEBSITE,
            'status' => Lead::STATUS_NEW,
            'project_budget' => $budget,
            'service_interest' => $service?->getTranslation('title', $context->locale) ?? $service?->title,
            'service_id' => $service?->id,
            'problem_statement' => $problem,
            'chat_transcript' => $context->transcript,
            'meta' => [
                'provider' => 'public_chat',
            ],
            'botman_user_id' => $context->botmanUserId,
            'botman_driver' => $context->botmanDriver,
            'locale' => $context->locale,
            'ip_address' => $context->ipAddress,
        ];

        if ($context->leadAlreadyCaptured()) {
            $lead = Lead::query()->find($context->leadId);
            if ($lead !== null) {
                $lead->fill($payload);
                $lead->save();
                $this->syncService($lead, $service?->id);
                $context->serviceId = $service?->id;

                return ToolResult::success([
                    'captured' => true,
                    'updated' => true,
                    'lead_id' => $lead->id,
                    'message' => 'Lead details were updated. Thank the visitor; the team already has their information.',
                ], ['Leads']);
            }
        }

        $lead = Lead::query()->create($payload);
        $this->syncService($lead, $service?->id);
        $context->leadId = $lead->id;
        $context->serviceId = $service?->id;
        $this->notifyAdmins($lead);

        return ToolResult::success([
            'captured' => true,
            'updated' => false,
            'lead_id' => $lead->id,
            'message' => 'Lead saved. Thank the visitor and confirm the team will follow up. Do not create another lead.',
        ], ['Leads']);
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    private function optionalString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }

    private function publishedService(?int $id): ?Service
    {
        if ($id === null || $id < 1) {
            return null;
        }

        return Service::query()->published()->find($id);
    }

    private function syncService(Lead $lead, ?int $serviceId): void
    {
        if ($serviceId) {
            $lead->services()->sync([$serviceId]);
        }
    }

    private function notifyAdmins(Lead $lead): void
    {
        try {
            foreach (AdminEmail::addresses() as $email) {
                Notification::route('mail', $email)->notify(new NewLeadNotification($lead));
            }
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
