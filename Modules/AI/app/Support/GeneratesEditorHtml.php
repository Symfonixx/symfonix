<?php

namespace Modules\AI\Support;

trait GeneratesEditorHtml
{
    protected function systemPrompt(): string
    {
        $prompt = 'You are an expert content writer embedded inside a rich text editor (TinyMCE). '
            .'Write clear, well-structured content for the given request. '
            .'Match the company voice, services, and positioning from the company profile below. '
            .'Do not invent awards, clients, or claims that contradict that profile. '
            .'Respond with a single HTML fragment using semantic tags such as <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em> where appropriate. '
            .'Do not wrap the response in <html>, <head>, or <body> tags. '
            .'Do not use markdown syntax or code fences. Return only the HTML fragment, nothing else.';

        return CompanyContentProfile::appendTo($prompt);
    }

    protected function userMessage(string $prompt, ?string $context): string
    {
        if (! filled($context)) {
            return $prompt;
        }

        return $prompt."\n\nSelected content to consider:\n".$context;
    }

    protected function normalizeHtml(string $content): string
    {
        return FormContentSchema::stripFences($content, 'html');
    }

    /**
     * @return array{success: bool, html: ?string, error: ?string}
     */
    protected function successResult(string $html): array
    {
        return [
            'success' => true,
            'html' => $this->normalizeHtml($html),
            'error' => null,
        ];
    }

    /**
     * @return array{success: bool, html: ?string, error: ?string}
     */
    protected function failureResult(string $error): array
    {
        return [
            'success' => false,
            'html' => null,
            'error' => $error,
        ];
    }
}
