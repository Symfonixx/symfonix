<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Settings;
use Modules\Base\Support\MailConfig;
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
        $mailResolved = MailConfig::resolved();
        $whatsappResolved = WhatsAppConfig::resolved();

        $mailConfigured = MailConfig::isConfigured();
        $whatsappConfigured = WhatsAppConfig::isConfigured();

        $mailEnvPlaceholders = [];
        foreach (MailConfig::ENV_MAP as $key => $envKey) {
            $mailEnvPlaceholders[$key] = (string) (env($envKey) ?: '');
        }

        $whatsappEnvPlaceholders = [];
        foreach (WhatsAppConfig::ENV_MAP as $key => $envKey) {
            $whatsappEnvPlaceholders[$key] = (string) (env($envKey) ?: '');
        }

        return view('base::admin.integrations.index', compact(
            'settings',
            'mailStored',
            'whatsappStored',
            'mailResolved',
            'whatsappResolved',
            'mailConfigured',
            'whatsappConfigured',
            'mailEnvPlaceholders',
            'whatsappEnvPlaceholders',
        ));
    }

    public function store(Request $request)
    {
        $data = $request->input('data', []);
        if (! is_array($data)) {
            $data = [];
        }

        $allowed = array_merge(MailConfig::KEYS, WhatsAppConfig::KEYS);
        $secretKeys = [
            'mail_password',
            'whatsapp_api_token',
            'whatsapp_webhook_verify_token',
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

            Settings::set($key, $value);
        }

        cache()->forget('settings');

        MailConfig::mergeIntoConfig();
        WhatsAppConfig::mergeIntoConfig();

        session()->flushMessage(true);

        return back();
    }
}
