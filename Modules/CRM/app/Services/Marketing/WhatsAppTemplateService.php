<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Support\Collection;
use Modules\CRM\Models\WhatsAppTemplate;

class WhatsAppTemplateService
{
    /**
     * @return array<int, int>
     */
    public function parseBodyVariables(string $body): array
    {
        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);

        if (empty($matches[1])) {
            return [];
        }

        return collect($matches[1])
            ->map(fn (string $num) => (int) $num)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<int|string, string>  $parameters
     */
    public function renderPreview(WhatsAppTemplate $template, array $parameters): string
    {
        $preview = '';

        if ($template->header_type === WhatsAppTemplate::HEADER_TEXT && filled($template->header_content)) {
            $header = $template->header_content;
            foreach ($parameters as $index => $value) {
                $header = str_replace('{{'.$index.'}}', (string) $value, $header);
            }
            $preview .= $header."\n\n";
        } elseif ($template->header_type !== WhatsAppTemplate::HEADER_NONE) {
            $preview .= '['.strtoupper($template->header_type).": {$template->header_content}]\n\n";
        }

        $body = $template->body;
        foreach ($parameters as $index => $value) {
            $body = str_replace('{{'.$index.'}}', (string) $value, $body);
        }
        $preview .= $body;

        if (filled($template->footer)) {
            $preview .= "\n\n".$template->footer;
        }

        if (is_array($template->buttons) && ! empty($template->buttons)) {
            $preview .= "\n\n---\n";
            foreach ($template->buttons as $button) {
                $label = is_array($button) ? ($button['text'] ?? $button['label'] ?? '') : (string) $button;
                if ($label !== '') {
                    $preview .= "[{$label}]\n";
                }
            }
        }

        return trim($preview);
    }

    /**
     * @return Collection<int, WhatsAppTemplate>
     */
    public function listSendable(): Collection
    {
        return WhatsAppTemplate::query()
            ->where('is_active', true)
            ->where('status', WhatsAppTemplate::STATUS_APPROVED)
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?int $userId = null): WhatsAppTemplate
    {
        $data['user_id'] = $userId;

        return WhatsAppTemplate::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(WhatsAppTemplate $template, array $data): WhatsAppTemplate
    {
        $template->update($data);

        return $template->fresh();
    }

    public function delete(WhatsAppTemplate $template): void
    {
        $template->delete();
    }
}
