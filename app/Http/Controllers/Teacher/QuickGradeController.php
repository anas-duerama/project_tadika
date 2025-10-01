<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Student;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;

class QuickGradeController extends Controller
{
    /**
     * หน้าแบบฟอร์ม “ไวกรอกคะแนน”
     * GET /teacher/grades/quick?class=...&room=...&term=...&subject=...
     */
    public function quick(Request $request)
    {
        $class = (string) $request->get('class', '');
        $room  = (string) $request->get('room',  '');
        $term  = (string) $request->get('term',  '');
        $subject = (string) $request->get('subject', '');

        // เดาชื่อคอลัมน์ชั้น/ห้องให้รองรับฐานข้อมูลที่ต่างกัน
        $table = 'students';
        $classCandidates = ['class_level','class','level','grade_level','grade'];
        $roomCandidates  = ['room','room_no','section','classroom'];

        $classCol = collect($classCandidates)->first(fn($c) => Schema::hasColumn($table, $c)) ?? 'class_level';
        $roomCol  = collect($roomCandidates)->first(fn($c) => Schema::hasColumn($table, $c))  ?? 'room';

        $hasClass = Schema::hasColumn($table, $classCol);
        $hasRoom  = Schema::hasColumn($table, $roomCol);

        // รายการชั้น/ห้อง สำหรับตัวกรอง (distinct)
        $classes = $hasClass
            ? Student::query()->select($classCol)->whereNotNull($classCol)->distinct()->orderBy($classCol)->pluck($classCol)
            : collect();

        $rooms = ($hasClass && $hasRoom && $class !== '')
            ? Student::query()->select($roomCol)->where($classCol, $class)->whereNotNull($roomCol)->distinct()->orderBy($roomCol)->pluck($roomCol)
            : collect();

        // ดึงนักเรียนตามชั้น/ห้อง
        $students = Student::query()
            ->when($class !== '' && $hasClass, fn($q) => $q->where($classCol, $class))
            ->when($room  !== '' && $hasRoom,  fn($q) => $q->where($roomCol,  $room))
            ->orderBy($hasClass ? $classCol : 'id')
            ->orderBy($hasRoom  ? $roomCol  : 'id')
            ->orderBy('student_code')
            ->get(['id','student_code',
                   // บางโปรเจ็กต์ใช้ fullname; บางอัน first_name/last_name
                   DB::raw("COALESCE(fullname, CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,''))) as _name")
            ]);

        // ถ้ามี term+subject ให้เติมคะแนนเดิมลงในฟอร์มได้
        $existingByStudent = collect();
        if ($term !== '' && $subject !== '' && $students->isNotEmpty()) {
            $existingByStudent = Grade::query()
                ->whereIn('student_id', $students->pluck('id'))
                ->where('term', $term)
                ->where('subject', $subject)
                ->get()
                ->keyBy('student_id'); // [student_id] => Grade
        }

        return view('teacher.grades.quick', [
            'class' => $class,
            'room' => $room,
            'term' => $term,
            'subject' => $subject,
            'classCol' => $classCol,
            'roomCol' => $roomCol,
            'hasClass' => $hasClass,
            'hasRoom' => $hasRoom,
            'classes' => $classes,
            'rooms' => $rooms,
            'students' => $students,
            'existingByStudent' => $existingByStudent,
        ]);
    }

    /**
     * บันทึกคะแนนแบบหลายคนพร้อมกัน
     * POST /teacher/grades/quick
     */
    public function storeQuick(Request $request)
    {
        $data = $request->validate([
            'class'   => ['nullable','string','max:100'],
            'room'    => ['nullable','string','max:100'],
            'term'    => ['required','string','max:100'],
            'subject' => ['required','string','max:100'],
            'scores'  => ['required','array'], // scores[student_id] => value
            'scores.*'=> ['nullable','string','max:50'],
        ], [
            'term.required' => 'กรุณาระบุภาคเรียน',
            'subject.required' => 'กรุณาระบุวิชา',
            'scores.required' => 'กรุณากรอกคะแนนอย่างน้อย 1 รายการ',
        ]);

        $term = $data['term'];
        $subject = $data['subject'];
        $scores = $data['scores'];

        // ถ้ามีคอลัมน์ teacher_id ใน grades จะบันทึกผู้สอนด้วย
        $hasTeacherId = Schema::hasColumn('grades', 'teacher_id');

        DB::transaction(function () use ($scores, $term, $subject, $hasTeacherId) {
            foreach ($scores as $studentId => $score) {
                // ข้ามช่องที่เว้นว่าง
                if ($score === null || $score === '') continue;

                Grade::updateOrCreate(
                    [
                        'student_id' => (int) $studentId,
                        'term'       => $term,
                        'subject'    => $subject,
                    ],
                    array_filter([
                        'score'      => $score,
                        // ถ้ามี teacher_id ให้บันทึกเป็นผู้ที่ล็อกอิน
                        'teacher_id' => $hasTeacherId ? auth()->id() : null,
                    ], fn($v) => !is_null($v))
                );
            }
        });

        return redirect()
            ->route('teacher.grades.quick', [
                'class' => $data['class'],
                'room' => $data['room'],
                'term' => $term,
                'subject' => $subject,
            ])
            ->with('ok', 'บันทึกคะแนนเรียบร้อย');
    }
}
