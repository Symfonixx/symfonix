<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ziggy route blacklist
    |--------------------------------------------------------------------------
    |
    | Exclude only heavy tooling routes. Keep admin/portal/auth routes so the
    | header menu can call route() without throwing for logged-in users.
    |
    */

    'except' => [
        'debugbar.*',
        'telescope.*',
        'pulse',
        'pulse.*',
        'horizon.*',
        'sanctum.*',
        'livewire.*',
        'default-livewire.*',
        'ignition.*',
        'docs',
        'docs.*',
        'botman.handle',
        'chatbot.widget',
    ],

];
