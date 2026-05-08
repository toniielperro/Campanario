<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try{
    $max = Illuminate\Support\Facades\DB::table('migrations')->max('batch') ?: 0;
    $batch = $max + 1;
    Illuminate\Support\Facades\DB::table('migrations')->insert([
        'migration' => '2026_05_07_000003_create_schedule_exceptions_table',
        'batch' => $batch
    ]);
    echo "inserted migration record\n";
}catch(\Throwable $e){
    echo 'error: '.$e->getMessage()."\n";
}
