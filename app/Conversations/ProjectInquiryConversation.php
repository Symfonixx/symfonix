<?php

namespace App\Conversations;

use App\Models\Lead;
use App\Notifications\NewLeadNotification;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Facades\Notification;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceCategory;

class ProjectInquiryConversation extends Conversation
{
    protected ?string $initialMessage;

    protected array $transcript = [];

    protected ?string $problemStatement = null;

    protected array $serviceMatches = [];

    protected ?string $primaryService = null;

    protected ?int $selectedServiceId = null;

    protected ?string $selectedServiceTitle = null;

    protected ?int $selectedCategoryId = null;

    protected ?string $name = null;

    protected ?string $email = null;

    protected ?string $companyName = null;

    protected ?string $budget = null;

    protected array $analysisMeta = [];

    public function __construct(?string $initialMessage = null)
    {
        $this->initialMessage = $initialMessage ? trim($initialMessage) : null;
    }

    public function run(): void
    {
        if ($this->initialMessage) {
            $this->addTranscript('user', $this->initialMessage, ['source' => 'initial']);
        }

        $this->reply(__('chat.lead.greeting'));
        $this->askProblem();
    }

    protected function askProblem(): void
    {
        $this->askWithLog(__('chat.lead.ask_problem'), function (Answer $answer) {
            $this->problemStatement = trim($answer->getText());
            $this->addTranscript('user', $this->problemStatement);

            $analysis = $this->analyzeIntent($this->problemStatement);
            $this->serviceMatches = $analysis['services'];
            $this->primaryService = $analysis['primary_service'];

            $recommended = $this->findRecommendedService($this->problemStatement);

            if (! $recommended) {
                $this->promptServiceSelection();

                return;
            }

            $this->confirmRecommendedService($recommended);
        });
    }

    protected function promptServiceSelection(): void
    {
        $categories = $this->getServiceCategories();
        if ($categories->isEmpty()) {
            $this->reply(__('chat.lead.no_services'));
            $this->leadCaptureIntro();

            return;
        }

        $question = Question::create(__('chat.lead.pick_category'))
            ->addButtons($categories->map(function (ServiceCategory $category) {
                return Button::create($category->getTranslation('title', app()->getLocale()))
                    ->value((string) $category->id);
            })->values()->all());

        $this->addTranscript('bot', __('chat.lead.pick_category'), ['type' => 'button']);

        $this->ask($question, function (Answer $answer) use ($categories) {
            $selectedId = null;

            if ($answer->isInteractiveMessageReply()) {
                $selectedId = (int) $answer->getValue();
                $this->addTranscript('user', $answer->getValue(), ['source' => 'button']);
            } else {
                $selectedId = $this->matchCategoryByName($answer->getText(), $categories);
                $this->addTranscript('user', $answer->getText());
            }

            $category = $categories->firstWhere('id', $selectedId);
            if (! $category) {
                $this->reply(__('chat.lead.service_unknown'));
                $this->promptServiceSelection();

                return;
            }

            $this->selectedCategoryId = $category->id;
            $this->listServicesInCategory($category);
        });
    }

    protected function confirmRecommendedService(Service $service): void
    {
        $title = $service->getTranslation('title', app()->getLocale());
        $this->selectedServiceId = $service->id;
        $this->selectedServiceTitle = $title;
        $this->selectedCategoryId = $service->service_category_id;

        $this->reply(__('chat.lead.recommended_service', ['service' => $title]));

        $question = Question::create(__('chat.lead.validate_service'))
            ->addButtons([
                Button::create(__('chat.lead.confirm'))->value('confirm'),
                Button::create(__('chat.lead.no'))->value('no'),
            ]);

        $this->addTranscript('bot', __('chat.lead.validate_service'), ['type' => 'button']);

        $this->ask($question, function (Answer $answer) {
            $value = $answer->isInteractiveMessageReply() ? $answer->getValue() : strtolower(trim($answer->getText()));
            $this->addTranscript('user', $answer->getText());

            if (in_array($value, ['confirm', 'yes', 'y'], true)) {
                $this->shareValueProposition();
                $this->askGetStarted();

                return;
            }

            $this->selectedServiceId = null;
            $this->selectedServiceTitle = null;
            $this->selectedCategoryId = null;
            $this->promptServiceSelection();
        });
    }

    protected function listServicesInCategory(ServiceCategory $category): void
    {
        $services = $category->services()->published()->get();

        if ($services->isEmpty()) {
            $this->reply(__('chat.lead.no_services'));
            $this->promptServiceSelection();

            return;
        }

        $question = Question::create(__('chat.lead.pick_service'))
            ->addButtons($services->map(function (Service $service) {
                return Button::create($service->getTranslation('title', app()->getLocale()))
                    ->value((string) $service->id);
            })->values()->all());

        $this->addTranscript('bot', __('chat.lead.pick_service'), ['type' => 'button']);

        $this->ask($question, function (Answer $answer) use ($services) {
            $selectedId = null;

            if ($answer->isInteractiveMessageReply()) {
                $selectedId = (int) $answer->getValue();
                $this->addTranscript('user', $answer->getValue(), ['source' => 'button']);
            } else {
                $selectedId = $this->matchServiceByName($answer->getText(), $services);
                $this->addTranscript('user', $answer->getText());
            }

            $service = $services->firstWhere('id', $selectedId);
            if (! $service) {
                $this->reply(__('chat.lead.service_unknown'));
                $this->listServicesInCategory($services->first()->category);

                return;
            }

            $this->selectedServiceId = $service->id;
            $this->selectedServiceTitle = $service->getTranslation('title', app()->getLocale());
            $this->selectedCategoryId = $service->service_category_id;

            $this->shareValueProposition();
            $this->askGetStarted();
        });
    }

    protected function askGetStarted(): void
    {
        $question = Question::create(__('chat.lead.get_started_prompt'))
            ->addButtons([
                Button::create(__('chat.lead.get_started'))->value('get_started'),
            ]);

        $this->addTranscript('bot', __('chat.lead.get_started_prompt'), ['type' => 'button']);

        $this->ask($question, function (Answer $answer) {
            $value = $answer->isInteractiveMessageReply() ? $answer->getValue() : strtolower(trim($answer->getText()));
            $this->addTranscript('user', $answer->getText());

            if (in_array($value, ['get_started', 'start', 'yes', 'y'], true)) {
                $this->leadCaptureIntro();

                return;
            }

            $this->leadCaptureIntro();
        });
    }

    protected function shareServiceMatch(): void
    {
        $services = $this->formatServiceList($this->serviceMatches);

        $this->reply(__('chat.lead.service_match', ['services' => $services]));
    }

    protected function shareValueProposition(): void
    {
        if ($this->primaryService) {
            $key = "chat.lead.value.{$this->primaryService}";
        } elseif ($this->selectedServiceTitle) {
            $key = 'chat.lead.value.general';
        } else {
            $key = 'chat.lead.value.general';
        }

        $this->reply(__($key));
    }

    protected function leadCaptureIntro(): void
    {
        $this->reply(__('chat.lead.lead_intro'));
        $this->askName();
    }

    protected function askName(): void
    {
        $this->askWithLog(__('chat.lead.ask_name'), function (Answer $answer) {
            $this->name = trim($answer->getText());
            $this->addTranscript('user', $this->name);

            $this->askEmail();
        });
    }

    protected function askEmail(): void
    {
        $this->askWithLog(__('chat.lead.ask_email'), function (Answer $answer) {
            $email = trim($answer->getText());
            $this->addTranscript('user', $email);

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->reply(__('chat.lead.email_invalid'));

                $this->askEmail();

                return;
            }

            $this->email = $email;

            $this->askCompany();
        });
    }

    protected function askCompany(): void
    {
        $this->askWithLog(__('chat.lead.ask_company'), function (Answer $answer) {
            $this->companyName = trim($answer->getText());
            $this->addTranscript('user', $this->companyName);

            $this->askBudget();
        });
    }

    protected function askBudget(): void
    {
        $this->askWithLog(__('chat.lead.ask_budget'), function (Answer $answer) {
            $this->budget = trim($answer->getText());
            $this->addTranscript('user', $this->budget);

            $this->storeLead();
        });
    }

    protected function storeLead(): void
    {
        $lead = Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'company_name' => $this->companyName,
            'project_budget' => $this->budget,
            'service_interest' => $this->selectedServiceTitle ?? $this->primaryService,
            'service_id' => $this->selectedServiceId,
            'service_matches' => $this->serviceMatches,
            'problem_statement' => $this->problemStatement,
            'chat_transcript' => $this->transcript,
            'meta' => $this->analysisMeta,
            'botman_user_id' => optional($this->bot->getUser())->getId(),
            'botman_driver' => optional($this->bot->getDriver())->getName(),
            'locale' => app()->getLocale(),
            'ip_address' => request()->ip(),
        ]);

        $this->notifyAdmin($lead);
        $this->reply(__('chat.lead.thank_you'));
    }

    protected function notifyAdmin(Lead $lead): void
    {
        $emails = $this->getAdminEmails();
        foreach ($emails as $email) {
            Notification::route('mail', $email)->notify(new NewLeadNotification($lead));
        }

    }
    protected function getAdminEmails(): array
    {
        $emailSetting = config('services.admin_email')
            ?: config('services.leads.admin_email')
            ?: config('mail.from.address');
        if (! $emailSetting) {
            return [];
        }

        $emails = preg_split('/[,\s;]+/', $emailSetting);
        $emails = array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        return array_values(array_unique($emails));
    }

    protected function analyzeIntent(string $message): array
    {
        $fallback = $this->keywordMatch($message);

        try {
            $prompt = $this->buildIntentPrompt($message);
            $response = Ollama::agent($this->intentSystemPrompt())
                ->prompt($prompt)
                ->format('json')
                ->options([
                    'temperature' => 0.2,
                    'num_predict' => 200,
                ])
                ->ask();

            $payload = $this->parseOllamaResponse($response);
            $services = $this->normalizeServices($payload['services'] ?? []);
            $primary = $this->normalizeService($payload['primary_service'] ?? null);

            if (! $primary && count($services) === 1) {
                $primary = $services[0];
            }

            if ($primary && ! in_array($primary, $services, true)) {
                $services[] = $primary;
            }

            $this->analysisMeta = [
                'ollama' => [
                    'raw' => $response,
                    'parsed' => $payload,
                ],
            ];

            if ($primary || $services) {
                return [
                    'services' => $services,
                    'primary_service' => $primary,
                ];
            }
        } catch (\Throwable $exception) {
            $this->analysisMeta = [
                'ollama_error' => $exception->getMessage(),
            ];
        }

        $this->analysisMeta['fallback'] = $fallback;

        return $fallback;
    }

    protected function findRecommendedService(string $message): ?Service
    {
        $services = Service::published()->get();
        if ($services->isEmpty()) {
            return null;
        }

        $normalized = strtolower($message);
        $best = null;
        $bestScore = 0;

        foreach ($services as $service) {
            $title = strtolower($service->getTranslation('title', app()->getLocale()));
            $keywords = strtolower((string) $service->getTranslation('keywords', app()->getLocale()));
            $score = 0;

            foreach ([$title, $keywords] as $haystack) {
                if (! $haystack) {
                    continue;
                }
                foreach (preg_split('/\s+/', $haystack) as $word) {
                    $word = trim($word);
                    if ($word !== '' && str_contains($normalized, $word)) {
                        $score++;
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $service;
            }
        }

        if ($bestScore > 0) {
            return $best;
        }

        $category = $this->matchCategoryFromKeywords($message);
        if ($category) {
            return $category->services()->published()->first();
        }

        return null;
    }

    protected function buildIntentPrompt(string $message): string
    {
        return <<<PROMPT
User message: "{$message}"

Identify which services from this list match the user's intent:
- Web
- Mobile
- Cloud
- AI

Return JSON only in this format:
{"services":["Web"],"primary_service":"Web"}
PROMPT;
    }

    protected function intentSystemPrompt(): string
    {
        return 'You are a lead qualification assistant for a tech agency. Extract services only.';
    }

    protected function parseOllamaResponse(array $response): array
    {
        $raw = $response['response'] ?? '';
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function keywordMatch(string $message): array
    {
        $normalized = strtolower($message);
        $matches = [];

        $map = [
            'web' => ['web', 'website', 'frontend', 'backend', 'laravel', 'php'],
            'mobile' => ['mobile', 'ios', 'android', 'app', 'react native', 'flutter'],
            'cloud' => ['cloud', 'aws', 'azure', 'gcp', 'kubernetes', 'devops', 'infrastructure'],
            'ai' => ['ai', 'machine learning', 'ml', 'llm', 'chatbot', 'automation'],
        ];

        foreach ($map as $service => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    $matches[] = $service;
                    break;
                }
            }
        }

        $matches = array_values(array_unique($matches));

        return [
            'services' => $matches,
            'primary_service' => $matches[0] ?? null,
        ];
    }

    protected function getServiceCategories()
    {
        return ServiceCategory::with('services')->get();
    }

    protected function matchCategoryByName(string $text, $categories): ?int
    {
        $needle = strtolower(trim($text));
        foreach ($categories as $category) {
            $title = strtolower($category->getTranslation('title', app()->getLocale()));
            if ($title && str_contains($title, $needle)) {
                return $category->id;
            }
        }

        return null;
    }

    protected function matchServiceByName(string $text, $services): ?int
    {
        $needle = strtolower(trim($text));
        foreach ($services as $service) {
            $title = strtolower($service->getTranslation('title', app()->getLocale()));
            if ($title && str_contains($title, $needle)) {
                return $service->id;
            }
        }

        return null;
    }

    protected function matchCategoryFromKeywords(string $message): ?ServiceCategory
    {
        $map = [
            'web' => ['web', 'website', 'frontend', 'backend', 'laravel', 'php'],
            'mobile' => ['mobile', 'ios', 'android', 'app', 'react native', 'flutter'],
            'cloud' => ['cloud', 'aws', 'azure', 'gcp', 'kubernetes', 'devops', 'infrastructure'],
            'ai' => ['ai', 'machine learning', 'ml', 'llm', 'chatbot', 'automation'],
        ];

        $normalized = strtolower($message);
        foreach ($map as $service => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    return ServiceCategory::whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.en"))) LIKE ?', ['%'.$service.'%'])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.ar"))) LIKE ?', ['%'.$service.'%'])
                        ->first();
                }
            }
        }

        return null;
    }

    protected function normalizeServices(array $services): array
    {
        $normalized = [];

        foreach ($services as $service) {
            $value = $this->normalizeService((string) $service);
            if ($value && ! in_array($value, $normalized, true)) {
                $normalized[] = $value;
            }
        }

        return $normalized;
    }

    protected function normalizeService(?string $service): ?string
    {
        if (! $service) {
            return null;
        }

        $value = strtolower(trim($service));

        if (str_contains($value, 'web')) {
            return 'web';
        }
        if (str_contains($value, 'mobile') || str_contains($value, 'app') || str_contains($value, 'ios') || str_contains($value, 'android')) {
            return 'mobile';
        }
        if (str_contains($value, 'cloud') || str_contains($value, 'aws') || str_contains($value, 'azure') || str_contains($value, 'gcp')) {
            return 'cloud';
        }
        if (str_contains($value, 'ai') || str_contains($value, 'ml') || str_contains($value, 'machine learning')) {
            return 'ai';
        }

        return null;
    }

    protected function formatServiceList(array $services): string
    {
        $labels = array_map(fn ($service) => $this->serviceLabel($service), $services);

        return implode(', ', array_filter($labels));
    }

    protected function serviceLabel(string $service): string
    {
        return match ($service) {
            'web' => __('chat.lead.services.web'),
            'mobile' => __('chat.lead.services.mobile'),
            'cloud' => __('chat.lead.services.cloud'),
            'ai' => __('chat.lead.services.ai'),
            default => $service,
        };
    }

    protected function reply(string $message): void
    {
        $this->addTranscript('bot', $message);
        $this->say($message);
    }

    protected function askWithLog(string $message, callable $next): void
    {
        $this->addTranscript('bot', $message);
        $this->ask($message, $next);
    }

    protected function addTranscript(string $role, string $message, array $meta = []): void
    {
        $this->transcript[] = [
            'role' => $role,
            'message' => $message,
            'meta' => $meta,
            'at' => now()->toIso8601String(),
        ];
    }
}
