<?php
// One-off script to inspect AttendanceRecord data via the app container
chdir(__DIR__ . '/..');
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$model = App\Models\AttendanceRecord::class;
$count = $model::count();
$samples = $model::with('student')->latest('date')->take(5)->get()->map(function($r){
    return [
        'id' => $r->id,
        'date' => $r->date ? $r->date->toDateString() : null,
        'student_id' => $r->student_id,
        'student_code' => $r->student ? $r->student->student_code : null,
        'student_name' => $r->student ? $r->student->fullname : null,
        'present' => (bool) $r->present,
        'status' => $r->status,
        'remark' => $r->remark,
    ];
});
$dates = $model::selectRaw('date')->distinct()->orderBy('date','desc')->limit(10)->pluck('date');
echo json_encode(['count' => $count, 'samples' => $samples, 'dates' => $dates], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n";
