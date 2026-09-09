<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$row = Modules\Base\Models\LogDb::find(27);
$ctx = $row->context;
if (!is_string($ctx)) $ctx = json_encode($ctx);
echo $ctx;
