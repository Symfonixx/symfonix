<?php

namespace App\Support\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Modules\Base\Support\AdminEmail;

class EntityCreatedNotificationDispatcher
{
    public function __construct(
        private readonly NotificationRecipientResolver $recipients,
    ) {}

    public function handleCreated(Model $model): void
    {
        if (! $this->shouldDispatch()) {
            return;
        }

        $eventConfig = $this->eventConfig($model, 'created');
        if ($eventConfig === null) {
            return;
        }

        if (! $this->matches($model, $eventConfig['match'] ?? [])) {
            return;
        }

        $permission = (string) ($eventConfig['permission'] ?? '');
        if ($permission === '') {
            return;
        }

        try {
            $exceptUserId = config('notifications.exclude_actor')
                ? auth()->id()
                : null;

            $users = $this->recipients->forPermission($permission, $exceptUserId ? (int) $exceptUserId : null);
            $notificationClass = $eventConfig['notification']
                ?? config('notifications.default_notification');
            $payload = $this->serializableConfig($eventConfig);

            if ($users->isNotEmpty()) {
                $this->send($users, new $notificationClass($model, $payload));

                return;
            }

            $fallbackEmails = AdminEmail::addresses();
            if ($fallbackEmails === []) {
                Log::info('Entity created notification skipped: no recipients.', [
                    'model' => $model::class,
                    'id' => $model->getKey(),
                    'permission' => $permission,
                ]);

                return;
            }

            $this->send(
                Notification::route('mail', $fallbackEmails),
                new $notificationClass($model, [...$payload, 'channels' => ['mail']]),
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    public function shouldDispatch(): bool
    {
        if (! config('notifications.enabled', true)) {
            return false;
        }

        if (! Schema::hasTable('permissions')) {
            return false;
        }

        return ! $this->isQuietConsoleCommand();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function eventConfig(Model $model, string $event): ?array
    {
        $events = config('notifications.events', []);
        $config = is_array($events) ? ($events[get_class($model)][$event] ?? null) : null;

        return is_array($config) ? $config : null;
    }

    /**
     * @param  array<string, mixed>  $match
     */
    public function matches(Model $model, array $match): bool
    {
        foreach ($match as $attribute => $expected) {
            if ($model->getAttribute($attribute) !== $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $eventConfig
     * @return array<string, mixed>
     */
    private function serializableConfig(array $eventConfig): array
    {
        return [
            'permission' => $eventConfig['permission'] ?? null,
            'entity' => $eventConfig['entity'] ?? 'record',
            'route' => $eventConfig['route'] ?? null,
            'title_attribute' => $eventConfig['title_attribute'] ?? null,
            'channels' => $eventConfig['channels'] ?? config('notifications.default_channels', ['database', 'mail']),
            'extra_attributes' => $eventConfig['extra_attributes'] ?? [],
        ];
    }

    private function send(mixed $notifiables, object $notification): void
    {
        $channels = $notification->eventConfig['channels']
            ?? config('notifications.default_channels', ['database', 'mail']);

        if (! is_array($channels) || $channels === []) {
            $channels = ['database', 'mail'];
        }

        foreach ($channels as $channel) {
            if (! is_string($channel) || $channel === '') {
                continue;
            }

            try {
                if (config('notifications.send_now')) {
                    Notification::sendNow($notifiables, $notification, [$channel]);
                } else {
                    Notification::send($notifiables, $notification);
                    break;
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
    }

    private function isQuietConsoleCommand(): bool
    {
        if (! app()->runningInConsole() || app()->runningUnitTests()) {
            return false;
        }

        $argv = $_SERVER['argv'] ?? [];
        $quietCommands = config('notifications.quiet_commands', []);

        foreach ($argv as $argument) {
            foreach ($quietCommands as $command) {
                if ($argument === $command || str_ends_with((string) $argument, (string) $command)) {
                    return true;
                }
            }
        }

        return false;
    }
}
