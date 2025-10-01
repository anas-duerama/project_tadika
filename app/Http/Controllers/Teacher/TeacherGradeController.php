<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Grade;

class TeacherGradeController extends Controller
{
    /**
     * GET /teacher/grades
     * เราจะพาไปหน้า "ไวกรอกคะแนน" ที่สร้างไว้แล้ว
     */
    public function index(Request $request)
    {
        // ส่งพารามิเตอร์เดิม ๆ ต่อไป (class/room/term/subject)
        $params = $request->only(['class', 'room', 'term', 'subject']);
        return redirect()->route('teacher.grades.quick', $params);
    }

    /**
     * POST /teacher/grades
     * รองรับฟอร์มบันทึกคะแนนแบบหลายคนครั้งเดียว (รูปแบบเดียวกับ QuickGradeController@storeQuick)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'class'   => ['nullable','string','max:100'],
            'room'    => ['nullable','string','max:100'],
            'term'    => ['required','string','max:100'],
            'subject' => ['required','string','max:100'],
            'scores'  => ['required','array'],
            'scores.*'=> ['nullable','string','max:50'],
        ], [
            'term.required' => 'กรุณาระบุภาคเรียน',
            'subject.required' => 'กรุณาระบุวิชา',
            'scores.required' => 'กรุณากรอกคะแนนอย่างน้อย 1 รายการ',
        ]);

        $term = $data['term'];
        $subject = $data['subject'];
        $scores = $data['scores'];

        $hasTeacherId = Schema::hasColumn('grades', 'teacher_id');

        DB::transaction(function () use ($scores, $term, $subject, $hasTeacherId) {
            foreach ($scores as $studentId => $score) {
                if ($score === null || $score === '') continue;

                Grade::updateOrCreate(
                    [
                        'student_id' => (int) $studentId,
                        'term'       => $term,
                        'subject'    => $subject,
                    ],
                    array_filter([
                        'score'      => $score,
                        'teacher_id' => $hasTeacherId ? auth()->id() : null,
                    ], fn($v) => !is_null($v))
                );
            }
        });

        return redirect()
            ->route('teacher.grades.quick', [
                'class'   => $data['class'],
                'room'    => $data['room'],
                'term'    => $term,
                'subject' => $subject,
            ])
            ->with('ok', 'บันทึกคะแนนเรียบร้อย');
    }
}
