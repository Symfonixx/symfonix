<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Support\Collection;
use Modules\CRM\Enums\WhatsAppButtonType;
use Modules\CRM\Models\WhatsAppTemplate;

class WhatsAppTemplateService
{
    /**
     * @return array<int, int>
     */
    public function parseVariables(string $text): array
    {
        preg_match_all('/\{\{(\d+)\}\}/', $text, $matches);

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
     * @return array<int, int>
     */
    public function parseBodyVariables(string $body): array
    {
        return $this->parseVariables($body);
    }

    /**
     * @return array<int, array{
     *     component: string,
     *     index: int,
     *     placeholder: string,
     *     label: string,
     *     button_index?: int,
     *     button_text?: string
     * }>
     */
    public function collectVariables(WhatsAppTemplate $template): array
    {
        $variables = [];

        if ($template->header_type === WhatsAppTemplate::HEADER_TEXT) {
            foreach ($this->parseVariables((string) $template->header_content) as $index) {
                $placeholder = '{{'.$index.'}}';
                $variables[] = [
                    'component' => 'header',
                    'index' => $index,
                    'placeholder' => $placeholder,
                    'label' => __('crm::whatsapp.fields.header_variable', ['placeholder' => $placeholder]),
                ];
            }
        }

        foreach ($this->parseVariables((string) $template->body) as $index) {
            $placeholder = '{{'.$index.'}}';
            $variables[] = [
                'component' => 'body',
                'index' => $index,
                'placeholder' => $placeholder,
                'label' => __('crm::whatsapp.fields.body_variable', ['placeholder' => $placeholder]),
            ];
        }

        foreach ($this->urlButtons($template) as $buttonIndex => $button) {
            foreach ($this->parseVariables((string) ($button['url'] ?? '')) as $index) {
                $placeholder = '{{'.$index.'}}';
                $buttonText = trim((string) ($button['text'] ?? ''));
                $variables[] = [
                    'component' => 'buttons',
                    'button_index' => $buttonIndex,
                    'button_text' => $buttonText,
                    'index' => $index,
                    'placeholder' => $placeholder,
                    'label' => __('crm::whatsapp.fields.url_variable', [
                        'button' => $buttonText !== '' ? $buttonText : (string) ($buttonIndex + 1),
                        'placeholder' => $placeholder,
                    ]),
                ];
            }
        }

        return $variables;
    }

    /**
     * @param  array<int|string, mixed>  $parameters
     * @return array{header: array<int, string>, body: array<int, string>, buttons: array<int, array<int, string>>}
     */
    public function normalizeParameters(array $parameters, WhatsAppTemplate $template): array
    {
        if ($this->isLegacyParameters($parameters)) {
            $parameters = ['body' => $parameters];
        }

        $normalized = [
            'header' => [],
            'body' => [],
            'buttons' => [],
        ];

        if ($template->header_type === WhatsAppTemplate::HEADER_TEXT) {
            foreach ($this->parseVariables((string) $template->header_content) as $index) {
                $normalized['header'][$index] = $this->parameterValue($parameters['header'] ?? [], $index);
            }
        }

        foreach ($this->parseVariables((string) $template->body) as $index) {
            $normalized['body'][$index] = $this->parameterValue($parameters['body'] ?? [], $index);
        }

        foreach ($this->urlButtons($template) as $buttonIndex => $button) {
            $buttonParameters = $parameters['buttons'][$buttonIndex]
                ?? $parameters['buttons'][(string) $buttonIndex]
                ?? [];

            foreach ($this->parseVariables((string) ($button['url'] ?? '')) as $index) {
                $normalized['buttons'][$buttonIndex][$index] = $this->parameterValue(
                    is_array($buttonParameters) ? $buttonParameters : [],
                    $index,
                );
            }
        }

        return $normalized;
    }

    /**
     * @param  array<int|string, mixed>  $parameters
     * @return array<int, array{label: string, value: string}>
     */
    public function describeParameters(array $parameters, WhatsAppTemplate $template): array
    {
        $normalized = $this->normalizeParameters($parameters, $template);
        $items = [];

        foreach ($this->collectVariables($template) as $variable) {
            $value = match ($variable['component']) {
                'header' => $normalized['header'][$variable['index']] ?? '',
                'body' => $normalized['body'][$variable['index']] ?? '',
                'buttons' => $normalized['buttons'][$variable['button_index']][$variable['index']] ?? '',
                default => '',
            };

            $items[] = [
                'label' => $variable['label'],
                'value' => $value,
            ];
        }

        return $items;
    }

    /**
     * @param  array<int|string, mixed>  $parameters
     */
    public function renderPreview(WhatsAppTemplate $template, array $parameters): string
    {
        $normalized = $this->normalizeParameters($parameters, $template);
        $preview = '';

        if ($template->header_type === WhatsAppTemplate::HEADER_TEXT && filled($template->header_content)) {
            $preview .= $this->substitute((string) $template->header_content, $normalized['header'])."\n\n";
        } elseif ($template->header_type !== WhatsAppTemplate::HEADER_NONE && filled($template->header_content)) {
            $preview .= '['.strtoupper($template->header_type).": {$template->header_content}]\n\n";
        }

        $preview .= $this->substitute((string) $template->body, $normalized['body']);

        if (filled($template->footer)) {
            $preview .= "\n\n".$template->footer;
        }

        if (is_array($template->buttons) && ! empty($template->buttons)) {
            $preview .= "\n\n---\n";
            foreach ($template->buttons as $buttonIndex => $button) {
                if (! is_array($button)) {
                    $preview .= '['.$button."]\n";

                    continue;
                }

                $label = trim((string) ($button['text'] ?? $button['label'] ?? ''));
                $url = (string) ($button['url'] ?? '');
                $line = $label !== '' ? "[{$label}]" : '';

                if ($this->isUrlButton($button) && $url !== '') {
                    $substitutedUrl = $this->substitute(
                        $url,
                        $normalized['buttons'][$buttonIndex] ?? [],
                    );
                    $line = trim($line.' '.$substitutedUrl);
                }

                if ($line !== '') {
                    $preview .= $line."\n";
                }
            }
        }

        return trim($preview);
    }

    /**
     * @param  array<int|string, string>  $parameters
     */
    public function substitute(string $text, array $parameters): string
    {
        foreach ($parameters as $index => $value) {
            $text = str_replace('{{'.$index.'}}', (string) $value, $text);
        }

        return $text;
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

    /**
     * @param  array<int|string, mixed>  $parameters
     */
    private function isLegacyParameters(array $parameters): bool
    {
        if ($parameters === []) {
            return false;
        }

        foreach (array_keys($parameters) as $key) {
            if (in_array((string) $key, ['header', 'body', 'buttons'], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int|string, mixed>  $parameters
     */
    private function parameterValue(array $parameters, int $index): string
    {
        return trim((string) ($parameters[$index] ?? $parameters[(string) $index] ?? ''));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function urlButtons(WhatsAppTemplate $template): array
    {
        $buttons = [];

        foreach ($template->buttons ?? [] as $index => $button) {
            if (is_array($button) && $this->isUrlButton($button)) {
                $buttons[(int) $index] = $button;
            }
        }

        return $buttons;
    }

    /**
     * @param  array<string, mixed>  $button
     */
    private function isUrlButton(array $button): bool
    {
        return strtoupper((string) ($button['type'] ?? '')) === WhatsAppButtonType::URL->value;
    }
}
