<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ziggy route blacklist
    |--------------------------------------------------------------------------
    |
    | Keep the public SPA route map small. Admin/API/tooling routes are not
    | needed in the frontend Ziggy payload (and should not be dumped in HTML).
    |
    */

    'except' => [
        'admin.*',
        'api.*',
        'crm.*',
        'finance.*',
        'storage.*',
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
