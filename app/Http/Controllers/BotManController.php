<?php

namespace App\Http\Controllers;

use App\Conversations\ProjectInquiryConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Web\WebDriver;
use Illuminate\Http\Request;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        // BotMan's WebDriver sends its own JSON response via ->send(); Debugbar would
        // otherwise inject its widget markup after that JSON and corrupt the payload.
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        // Ensure the bot uses the same locale as the site / widget
        if ($request->has('locale')) {
            app()->setLocale($request->get('locale'));
        }

        // Load Web Driver
        DriverManager::loadDriver(WebDriver::class);

        $config = [
            'web' => [
                'matchingData' => [
                    'driver' => 'web',
                ],
            ],
        ];

        // Create BotMan instance with proper cache and current request
        $botman = BotManFactory::create($config, new LaravelCache, $request);

        // Start lead qualification flow on any message
        $botman->hears('{message}', function (BotMan $bot) {
            $initialMessage = $bot->getMessage()->getText();
            $bot->startConversation(new ProjectInquiryConversation($initialMessage));
        });

        $botman->listen();
    }

    public function widget()
    {
        return view('chatbot');
    }
}
