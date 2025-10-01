<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\Teacher;

echo "Students: " . Student::count() . PHP_EOL;
$hasRoom = Student::whereNotNull('room')->where('room','<>','')->exists();
echo "Has non-empty room values? " . ($hasRoom ? 'yes' : 'no') . PHP_EOL;
$distinct = Student::query()->select('room')->distinct()->limit(100)->get()->pluck('room')->map(fn($r)=> (string)$r)->toArray();
echo "Distinct room values (up to 100):\n";
foreach ($distinct as $d) {
    echo "- " . ($d === '' ? '(empty string)' : ($d === null ? '(null)' : $d)) . PHP_EOL;
}

echo PHP_EOL;
echo "Sample students:\n";
foreach (Student::limit(15)->get(['id','student_code','room']) as $s) {
    echo "id={$s->id}, code={$s->student_code}, room=" . ($s->room ?? '(null)') . PHP_EOL;
}

echo PHP_EOL;
echo "Teachers with teaching_rooms:\n";
foreach (Teacher::limit(20)->get(['id','user_id','teaching_rooms','homeroom']) as $t) {
    echo "#{$t->id}, user={$t->user_id}, teaching_rooms=" . ($t->teaching_rooms ?? '(null)') . ", homeroom=" . ($t->homeroom ?? '(null)') . PHP_EOL;
}

echo "Done." . PHP_EOL;
