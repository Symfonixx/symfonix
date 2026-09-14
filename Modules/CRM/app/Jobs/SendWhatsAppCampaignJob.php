<?php

namespace Modules\CRM\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\CRM\Models\WhatsAppMessageLog;
use Modules\CRM\Services\Marketing\WhatsAppApiService;
use Throwable;

class SendWhatsAppCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(
        public int $campaignId,
        public string $locale = 'en',
    ) {}

    public function handle(WhatsAppApiService $apiService): void
    {
        App::setLocale($this->locale);

        $campaign = WhatsAppCampaign::query()
            ->with('template')
            ->findOrFail($this->campaignId);

        $campaign->markAsSending();

        $template = $campaign->template;
        $parameters = $campaign->template_parameters ?? [];

        $logs = WhatsAppMessageLog::query()
            ->where('whatsapp_campaign_id', $campaign->id)
            ->where('status', WhatsAppMessageLog::STATUS_PENDING)
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($logs as $log) {
            $result = $apiService->sendTemplateMessage(
                $log->phone,
                $template,
                $parameters,
            );

            if ($result['success']) {
                $log->markAsSent($result['message_id']);
                $sentCount++;
            } else {
                $log->markAsFailed($result['error'] ?? __('crm::whatsapp.messages.send_failed'));
                $failedCount++;
            }

            usleep(100_000);
        }

        if ($sentCount === 0 && $failedCount > 0) {
            $campaign->markAsFailed();
        } else {
            $campaign->markAsFinished();
        }
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        WhatsAppCampaign::query()
            ->whereKey($this->campaignId)
            ->update(['status' => WhatsAppCampaign::STATUS_FAILED]);
    }
}
