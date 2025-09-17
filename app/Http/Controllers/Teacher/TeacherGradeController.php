<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Grade;

class TeacherGradeController extends Controller
{
    private string $classCol = 'class_level';
    private string $roomCol  = 'room';

    public function index(Request $request)
    {
        $term = $request->get('term','1/2568');
        $subject = $request->get('subject','');
        $class = $request->get('class','ป.6');
        $room  = $request->get('room','1');

        $students = Student::query()
            ->when($class, fn($q)=>$q->where($this->classCol,$class))
            ->when($room,  fn($q)=>$q->where($this->roomCol,$room))
            ->orderBy('student_code')
            ->get();

        $grades = Grade::where('term',$term)->where('subject',$subject)->get()->keyBy('student_id');

        return view('teacher.grades.index', compact('term','subject','class','room','students','grades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'term'=>['required','string','max:50'],
            'subject'=>['required','string','max:100'],
            'class'=>['required','string','max:50'],
            'room'=>['required','string','max:50'],
            'scores'=>['array'],
        ]);

        $term = $validated['term'];
        $subject = $validated['subject'];
        $scores = $validated['scores'] ?? [];

        foreach ($scores as $studentId => $score) {
            Grade::updateOrCreate(
                ['student_id'=>$studentId,'term'=>$term,'subject'=>$subject],
                ['score'=>$score]
            );
        }

        return back()->with('ok','บันทึกเกรดสำเร็จ');
    }
}
