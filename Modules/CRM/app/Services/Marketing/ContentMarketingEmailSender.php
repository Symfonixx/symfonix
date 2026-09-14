<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\CRM\Actions\Marketing\SendMarketingEmailAction;
use Modules\CRM\Models\MarketingCampaign;

class ContentMarketingEmailSender
{
    public function __construct(
        private readonly SendMarketingEmailAction $sendMarketingEmailAction,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function validationRules(): array
    {
        return [
            'send_as_marketing' => ['sometimes', 'boolean'],
            'marketing_audience' => ['required_if:send_as_marketing,1', 'nullable', 'in:subscribers,contacts,inquiries,all'],
        ];
    }

    public function shouldSend(Request $request): bool
    {
        return $request->boolean('send_as_marketing')
            && $request->user()?->can('marketing.email.send');
    }

    /**
     * @throws ValidationException
     */
    public function validate(Request $request): void
    {
        if (! $request->boolean('send_as_marketing')) {
            return;
        }

        if (! $request->user()?->can('marketing.email.send')) {
            throw ValidationException::withMessages([
                'send_as_marketing' => __('crm::marketing.validation.permission_required'),
            ]);
        }

        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function send(Request $request, string $subject, string $body): MarketingCampaign
    {
        $this->validate($request);

        return $this->sendMarketingEmailAction->execute(
            [
                'subject' => $subject,
                'body' => $body,
                ...$this->recipientDataFromAudience((string) $request->input('marketing_audience')),
            ],
            (int) $request->user()->id,
        );
    }

    public function buildBody(?string $summary, ?string $content): string
    {
        $parts = [];

        if (filled($summary)) {
            $parts[] = '<p><em>'.e($summary).'</em></p>';
        }

        if (filled($content)) {
            $parts[] = $content;
        }

        return implode("\n", $parts);
    }

    /**
     * @return array<string, bool>
     */
    private function recipientDataFromAudience(string $audience): array
    {
        return match ($audience) {
            'subscribers' => ['all_subscribers' => true],
            'contacts' => ['all_contacts' => true],
            'inquiries' => ['all_contact_forms' => true],
            'all' => [
                'all_subscribers' => true,
                'all_contacts' => true,
                'all_contact_forms' => true,
            ],
            default => throw new \InvalidArgumentException(__('crm::marketing.validation.select_recipients')),
        };
    }
}
