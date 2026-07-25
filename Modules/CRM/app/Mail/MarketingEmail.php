<?php

namespace Modules\CRM\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarketingEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $emailBody,
        ?string $locale = null,
    ) {
        $this->locale($locale ?: app()->getLocale());
    }

    public function build(): self
    {
        return $this->subject($this->plainSubject())
            ->markdown('crm::emails.marketing', [
                'subject' => $this->emailSubject,
                'body' => $this->emailBody,
            ]);
    }

    private function plainSubject(): string
    {
        return trim(html_entity_decode(strip_tags($this->emailSubject), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
