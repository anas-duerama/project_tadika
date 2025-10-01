<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;

$t = Teacher::first();
if (!$t) { echo "No teacher found\n"; exit; }
$old = $t->teaching_rooms;
echo "Before: "; var_dump($old);
$t->update(['teaching_rooms' => ['ห้อง 1','ห้อง 2']]);
$t = $t->fresh();
echo "After: "; var_dump($t->teaching_rooms);
