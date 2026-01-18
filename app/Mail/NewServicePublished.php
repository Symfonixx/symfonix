<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Services\Models\Service;

class NewServicePublished extends Mailable
{
    use Queueable, SerializesModels;

    public Service $service;
    public string $locale;
    public string $title;

    public function __construct(Service $service, ?string $locale = null)
    {
        $this->service = $service;
        $this->locale = $locale ?: app()->getLocale();
        $this->title = $service->getTranslation('title', $this->locale, true);
    }

    public function build(): self
    {
        return $this->subject("New service: {$this->title}")
            ->markdown('emails.subscribers.new-service', [
                'service' => $this->service,
                'title' => $this->title,
                'url' => route('services.show', $this->service->slug),
            ]);
    }
}
