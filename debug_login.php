<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'hadi-hilal@hotmail.com')->first();
echo 'CURRENT_HASH:'.$user->password.PHP_EOL;
$user->password = Hash::make('DebugTemp123!');
$user->save();
echo 'UPDATED'.PHP_EOL;
