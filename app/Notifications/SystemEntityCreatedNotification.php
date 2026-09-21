<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SystemEntityCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $eventConfig
     */
    public function __construct(
        public Model $model,
        public array $eventConfig = [],
    ) {
        $this->onQueue((string) config('notifications.queue', 'default'));
        $this->afterCommit();
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        $channels = $this->eventConfig['channels'] ?? config('notifications.default_channels', ['mail', 'database']);

        return array_values(array_filter(
            is_array($channels) ? $channels : ['database', 'mail'],
            fn (mixed $channel): bool => is_string($channel) && $channel !== '',
        ));
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entity = $this->entityLabel();
        $title = $this->title();
        $url = $this->url();

        $mail = (new MailMessage)
            ->subject(__('base::notifications.subject_with_title', [
                'entity' => $entity,
                'title' => $title,
            ]))
            ->greeting(__('base::notifications.greeting', [
                'name' => $notifiable->name ?? '',
            ]))
            ->line(__('base::notifications.line_with_title', [
                'entity' => Str::lower($entity),
                'title' => $title,
            ]));

        foreach ($this->extraLines() as $line) {
            $mail->line($line);
        }

        if ($url !== null) {
            $mail->action(__('base::notifications.view', ['entity' => $entity]), $url);
        }

        return $mail;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $entity = $this->entityLabel();
        $title = $this->title();

        return [
            'type' => 'entity_created',
            'entity' => $this->entityKey(),
            'entity_type' => $this->model::class,
            'entity_id' => $this->model->getKey(),
            'message' => __('base::notifications.line_with_title', [
                'entity' => Str::lower($entity),
                'title' => $title,
            ]),
            'url' => $this->url(),
        ];
    }

    private function entityKey(): string
    {
        return (string) ($this->eventConfig['entity'] ?? 'record');
    }

    private function entityLabel(): string
    {
        $key = $this->entityKey();
        $translated = __('base::notifications.entities.'.$key);

        if ($translated === 'base::notifications.entities.'.$key) {
            return Str::headline($key);
        }

        return $translated;
    }

    private function title(): string
    {
        $attribute = $this->eventConfig['title_attribute'] ?? null;
        $candidates = is_array($attribute) ? $attribute : array_filter([(string) $attribute]);

        if ($candidates === []) {
            $candidates = ['name', 'title', 'subject', 'invoice_number', 'quote_number', 'ticket_number'];
        }

        foreach ($candidates as $key) {
            if (! is_string($key) || $key === '') {
                continue;
            }

            $value = $this->stringAttribute($key);

            if ($value !== null) {
                return $value;
            }
        }

        return '#'.$this->model->getKey();
    }

    /**
     * @return list<string>
     */
    private function extraLines(): array
    {
        $attributes = $this->eventConfig['extra_attributes'] ?? [];
        if (! is_array($attributes)) {
            return [];
        }

        $lines = [];
        foreach ($attributes as $attribute) {
            if (! is_string($attribute) || $attribute === '') {
                continue;
            }

            $value = $this->stringAttribute($attribute);
            if ($value === null) {
                continue;
            }

            $lines[] = Str::headline($attribute).': '.$value;
        }

        return $lines;
    }

    private function stringAttribute(string $key): ?string
    {
        if (str_contains($key, '.')) {
            $this->model->loadMissing(Str::before($key, '.'));
        }

        $value = data_get($this->model, $key);

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_array($value)) {
            $value = $value['en'] ?? reset($value) ?: null;
        }

        if (filled($value) && is_scalar($value)) {
            return (string) $value;
        }

        return null;
    }

    private function url(): ?string
    {
        $name = $this->eventConfig['route'] ?? null;
        if (! is_string($name) || $name === '' || ! Route::has($name)) {
            return null;
        }

        try {
            return route($name, $this->model);
        } catch (\Throwable) {
            try {
                return route($name);
            } catch (\Throwable) {
                return null;
            }
        }
    }
}
