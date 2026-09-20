<?php

namespace App\Http\Controllers;

use App\BotMan\CapturingWebDriver;
use App\Conversations\CustomerAiConversation;
use App\Conversations\ProjectInquiryConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Http\Request;
use Modules\AI\Services\Chatbot\PublicChatService;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        // BotMan's stock WebDriver echoes JSON via Response::send(). Debugbar and a
        // second Laravel JSON body would concatenate and break the widget parser.
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        if ($request->has('locale')) {
            app()->setLocale($request->get('locale'));
        }

        DriverManager::loadDriver(CapturingWebDriver::class);
        CapturingWebDriver::$lastPayload = [
            'status' => 200,
            'messages' => [],
        ];

        $config = [
            'web' => [
                'matchingData' => [
                    'driver' => 'web',
                ],
            ],
        ];

        $botman = BotManFactory::create($config, new LaravelCache, $request);

        $botman->hears('{message}', function (BotMan $bot) {
            $initialMessage = $bot->getMessage()->getText();

            if (app(PublicChatService::class)->isAvailable()) {
                $bot->startConversation(new CustomerAiConversation($initialMessage));

                return;
            }

            $bot->startConversation(new ProjectInquiryConversation($initialMessage));
        });

        $botman->listen();

        return response()->json(CapturingWebDriver::$lastPayload);
    }

    public function widget()
    {
        return view('chatbot');
    }
}
