<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Settings;
use Modules\Base\Support\AskSymfonixConfig;
use Modules\Base\Support\GeminiConfig;
use Modules\Base\Support\MailConfig;
use Modules\Base\Support\OpenAIConfig;
use Modules\Base\Support\WhatsAppConfig;

class IntegrationsController extends Controller
{
    public function __construct()
    {
        $this->setActive('settings');
    }

    public function index()
    {
        $this->setActive('integrations');

        $settings = Settings::pluck('value', 'key');
        $mailStored = MailConfig::stored();
        $whatsappStored = WhatsAppConfig::stored();
        $geminiStored = GeminiConfig::stored();
        $openaiStored = OpenAIConfig::stored();
        $assistantStored = AskSymfonixConfig::stored();
        $mailResolved = MailConfig::resolved();
        $whatsappResolved = WhatsAppConfig::resolved();
        $geminiResolved = GeminiConfig::resolved();
        $openaiResolved = OpenAIConfig::resolved();
        $assistantResolved = AskSymfonixConfig::resolved();

        $mailConfigured = MailConfig::isConfigured();
        $whatsappConfigured = WhatsAppConfig::isConfigured();
        $geminiConfigured = GeminiConfig::isConfigured();
        $openaiConfigured = OpenAIConfig::isConfigured();

        $mailEnvPlaceholders = [];
        foreach (MailConfig::ENV_MAP as $key => $envKey) {
            $mailEnvPlaceholders[$key] = (string) (env($envKey) ?: '');
        }

        $whatsappEnvPlaceholders = [];
        foreach (WhatsAppConfig::ENV_MAP as $key => $envKey) {
            $whatsappEnvPlaceholders[$key] = (string) (env($envKey) ?: '');
        }

        $geminiEnvPlaceholders = [];
        foreach (GeminiConfig::ENV_MAP as $key => $envKey) {
            $geminiEnvPlaceholders[$key] = (string) (env($envKey) ?: '');
        }

        $openaiEnvPlaceholders = [];
        foreach (OpenAIConfig::ENV_MAP as $key => $envKey) {
            $openaiEnvPlaceholders[$key] = (string) (env($envKey) ?: '');
        }

        return view('base::admin.integrations.index', compact(
            'settings',
            'mailStored',
            'whatsappStored',
            'geminiStored',
            'openaiStored',
            'assistantStored',
            'mailResolved',
            'whatsappResolved',
            'geminiResolved',
            'openaiResolved',
            'assistantResolved',
            'mailConfigured',
            'whatsappConfigured',
            'geminiConfigured',
            'openaiConfigured',
            'mailEnvPlaceholders',
            'whatsappEnvPlaceholders',
            'geminiEnvPlaceholders',
            'openaiEnvPlaceholders',
        ));
    }

    public function store(Request $request)
    {
        $data = $request->input('data', []);
        if (! is_array($data)) {
            $data = [];
        }

        $allowed = array_merge(
            MailConfig::KEYS,
            WhatsAppConfig::KEYS,
            GeminiConfig::KEYS,
            OpenAIConfig::KEYS,
            AskSymfonixConfig::KEYS,
        );
        $secretKeys = [
            'mail_password',
            'whatsapp_api_token',
            'whatsapp_webhook_verify_token',
            'gemini_api_key',
            'openai_api_key',
        ];

        foreach ($allowed as $key) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];
            if ($value === null) {
                $value = '';
            }

            $value = is_string($value) ? trim($value) : (string) $value;

            // Keep existing secret when the password field is left blank.
            if (in_array($key, $secretKeys, true) && $value === '') {
                continue;
            }

            if ($key === 'mail_port' && $value !== '') {
                $value = (string) max(1, min(65535, (int) $value));
            }

            if ($key === 'mail_mailer' && $value !== '') {
                $allowedMailers = ['smtp', 'log', 'array', 'sendmail', 'ses', 'postmark', 'resend'];
                if (! in_array($value, $allowedMailers, true)) {
                    $value = 'smtp';
                }
            }

            if ($key === 'mail_encryption' && $value !== '') {
                $value = in_array($value, ['tls', 'ssl', 'null'], true)
                    ? ($value === 'null' ? '' : $value)
                    : 'tls';
            }

            if ($key === 'ai_assistant_provider' && $value !== '') {
                $value = in_array($value, AskSymfonixConfig::AVAILABLE_PROVIDERS, true)
                    ? $value
                    : AskSymfonixConfig::DEFAULT_PROVIDER;
            }

            Settings::set($key, $value);
        }

        cache()->forget('settings');

        MailConfig::mergeIntoConfig();
        WhatsAppConfig::mergeIntoConfig();
        GeminiConfig::mergeIntoConfig();
        OpenAIConfig::mergeIntoConfig();
        AskSymfonixConfig::mergeIntoConfig();

        session()->flushMessage(true);

        return back();
    }
}
