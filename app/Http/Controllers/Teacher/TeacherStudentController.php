<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Student;

class TeacherStudentController extends Controller
{
    /**
     * แสดงรายชื่อนักเรียน (จำกัดเฉพาะห้องที่ครูสอน)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $teacher = $user?->teacher;

        // ===== 1) ห้องที่ครูสอน (allowed) =====
        $allowed = [];
        if ($teacher) {
            if (($teacher->homeroom_class ?? null) || ($teacher->homeroom_room ?? null)) {
                $allowed[] = [trim((string)$teacher->homeroom_class), trim((string)$teacher->homeroom_room)];
            }
            if ($teacher->homeroom ?? null) {
                foreach (preg_split('/\s*,\s*/u', (string)$teacher->homeroom, -1, PREG_SPLIT_NO_EMPTY) as $pair) {
                    [$c, $r] = array_pad(preg_split('/\s*\/\s*/u', $pair, 2), 2, '');
                    $allowed[] = [trim($c), trim($r)];
                }
            }
        }

        if (empty($allowed)) {
            return view('teacher.students.index', [
                'summary'  => ['total' => 0, 'class_count' => 0, 'room_count' => 0],
                'grouped'  => collect(),
                'q'        => (string)$request->get('q',''),
                'class'    => '',
                'room'     => '',
                'classCol' => 'class_level',
                'roomCol'  => 'room',
                'hasClass' => Schema::hasColumn('students','class_level'),
                'hasRoom'  => Schema::hasColumn('students','room'),
            ])->with('warn', 'ยังไม่ได้กำหนดชั้น/ห้องของครู กรุณาเพิ่ม homeroom_class/room หรือ homeroom ในโปรไฟล์ครู');
        }

        // ===== 2) ระบุคอลัมน์ชั้น/ห้อง ในตาราง students =====
        $table = 'students';
        $classCandidates = ['class_level','class','level','grade_level','grade'];
        $roomCandidates  = ['room','room_no','section','classroom'];

        $classCol = collect($classCandidates)->first(fn($c) => Schema::hasColumn($table, $c)) ?? 'class_level';
        $roomCol  = collect($roomCandidates)->first(fn($c) => Schema::hasColumn($table, $c))  ?? 'room';

        $hasClass = Schema::hasColumn($table, $classCol);
        $hasRoom  = Schema::hasColumn($table, $roomCol);

        // ===== 3) ระบุคอลัมน์ชื่อให้ปลอดภัย (fullname | first_name/last_name | fallback) =====
        $hasFullname = Schema::hasColumn($table, 'fullname');
        $hasFirst    = Schema::hasColumn($table, 'first_name');
        $hasLast     = Schema::hasColumn($table, 'last_name');

        if ($hasFullname) {
            $nameSelect = DB::raw('fullname as _name');
        } elseif ($hasFirst || $hasLast) {
            $firstExpr = $hasFirst ? 'first_name' : "''";
            $lastExpr  = $hasLast  ? 'last_name'  : "''";
            $nameSelect = DB::raw("TRIM(CONCAT(COALESCE($firstExpr,''),' ',COALESCE($lastExpr,''))) as _name");
        } else {
            // ไม่มีชื่อจริง ๆ → ใช้รหัสแทนเพื่อไม่ให้ error
            $nameSelect = DB::raw('student_code as _name');
        }

        // ===== 4) รับตัวกรอง และบังคับให้อยู่ใน allowed =====
        $q     = (string)$request->get('q', '');
        $class = (string)$request->get('class', '');
        $room  = (string)$request->get('room',  '');

        $allowedClasses = collect($allowed)->pluck(0)->filter()->unique()->values();
        $allowedMap = collect($allowed)->groupBy(fn($p) => $p[0] ?: '-')
                                       ->map(fn($rows) => collect($rows)->pluck(1)->filter()->unique()->values());

        if ($class !== '' && !$allowedClasses->contains($class)) {
            $class = ''; $room = '';
        }
        if ($class !== '' && $room !== '') {
            $roomsAllowed = $allowedMap->get($class, collect());
            if ($roomsAllowed->isNotEmpty() && !$roomsAllowed->contains($room)) {
                $room = '';
            }
        }

        // ===== 5) Query นักเรียน เฉพาะ allowed + เงื่อนไขค้นหาแบบปลอดภัย =====
        $selects = [
            'id',
            DB::raw('student_code as _code'),
            $nameSelect,
            DB::raw("$classCol as _class"),
            DB::raw("$roomCol  as _room"),
        ];

        $base = Student::query()
            ->when($q !== '', function($qr) use ($q, $hasFullname, $hasFirst, $hasLast) {
                $qr->where(function($w) use ($q, $hasFullname, $hasFirst, $hasLast) {
                    $w->where('student_code','like',"%{$q}%");
                    if ($hasFullname) $w->orWhere('fullname','like',"%{$q}%");
                    if ($hasFirst)    $w->orWhere('first_name','like',"%{$q}%");
                    if ($hasLast)     $w->orWhere('last_name','like',"%{$q}%");
                });
            });

        // จำกัดเฉพาะห้องที่มีสิทธิ์
        $base->where(function($wrap) use ($allowed, $classCol, $roomCol, $hasClass, $hasRoom) {
            foreach ($allowed as [$c, $r]) {
                $wrap->orWhere(function($w) use ($c, $r, $classCol, $roomCol, $hasClass, $hasRoom) {
                    if ($hasClass && $c !== '') $w->where($classCol, $c);
                    if ($hasRoom  && $r !== '') $w->where($roomCol,  $r);
                });
            }
        });

        if ($hasClass && $class !== '') $base->where($classCol, $class);
        if ($hasRoom  && $room  !== '') $base->where($roomCol,  $room);

        $students = $base
            ->orderBy($hasClass ? $classCol : 'id')
            ->orderBy($hasRoom  ? $roomCol  : 'id')
            ->orderBy('student_code')
            ->get($selects);

        // ===== 6) สรุป & จัดกลุ่ม =====
        $summary = [
            'total'       => $students->count(),
            'class_count' => $students->pluck('_class')->filter()->unique()->count(),
            'room_count'  => $students->pluck('_room')->filter()->unique()->count(),
        ];

        $grouped = $students
            ->groupBy(fn ($s) => $s->_class ?: '-')
            ->map(fn ($byClass) => $byClass->groupBy(fn ($s) => $s->_room ?: '-'));

        return view('teacher.students.index', [
            'summary'  => $summary,
            'grouped'  => $grouped,
            'q'        => $q,
            'class'    => $class,
            'room'     => $room,
            'classCol' => $classCol,
            'roomCol'  => $roomCol,
            'hasClass' => $hasClass,
            'hasRoom'  => $hasRoom,
        ]);
    }
}
