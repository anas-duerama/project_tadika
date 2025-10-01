<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

$roomTests = ['ห้อง 1', '1', 'ห้อง 2', '2', null];
$roomCol = 'room';

foreach ($roomTests as $sel) {
    echo "=== Testing selectedRoom: " . ($sel===null ? '(null)' : $sel) . "===\n";
    $studentHasRoomValues = Student::query()->whereNotNull($roomCol)->where($roomCol, '<>', '')->exists();
    echo "studentHasRoomValues: " . ($studentHasRoomValues ? 'yes' : 'no') . "\n";

    $variants = [];
    if ($sel !== null) {
        $r = trim((string)$sel);
        $variants = [$r];
        $digits = preg_replace('/\D+/', '', $r);
        if ($digits !== '') $variants[] = $digits;
        $noPrefix = preg_replace('/^(ห้อง|room|r)\s*/iu', '', $r);
        if ($noPrefix !== '' && $noPrefix !== $r) $variants[] = $noPrefix;
        $variants = array_values(array_filter(array_unique(array_map('trim', $variants))));
    }
    echo "variants: ".json_encode($variants, JSON_UNESCAPED_UNICODE)."\n";

    $q = Student::query();
    if ($sel !== null && $studentHasRoomValues) {
        $q->where(function($w) use ($variants, $roomCol) {
            foreach ($variants as $v) {
                $w->orWhere($roomCol, $v);
            }
        });
    }
    $rows = $q->orderBy('student_code')->get(['id','student_code','room']);
    echo "Found: " . $rows->count() . "\n";
    foreach ($rows as $r) {
        echo "id={$r->id}, code={$r->student_code}, room=" . ($r->room ?? '(null)') . "\n";
    }
    echo "\n";
}
