<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AttendanceRecord;

class TeacherAttendanceController extends Controller
{
    private string $classCol = 'class_level';
    private string $roomCol  = 'room';

    public function index(Request $request)
    {
        $date  = $request->get('date', now()->toDateString());
        $class = $request->get('class','ป.6');
        $room  = $request->get('room','1');

        $students = Student::query()
            ->when($class, fn($q)=>$q->where($this->classCol,$class))
            ->when($room,  fn($q)=>$q->where($this->roomCol,$room))
            ->orderBy('student_code')
            ->get();

        $records = AttendanceRecord::whereDate('date',$date)->get()->keyBy('student_id');

        return view('teacher.attendance.index', compact('date','class','room','students','records'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'date'    => ['required','date'],
            'class'   => ['required','string','max:50'],
            'room'    => ['required','string','max:50'],
            'present' => ['array'],
            'remark'  => ['array'],
        ]);

        $students = Student::query()
            ->where($this->classCol, $v['class'])
            ->where($this->roomCol,  $v['room'])
            ->get(['id']);

        foreach ($students as $s) {
            $isPresent = array_key_exists($s->id, $v['present'] ?? []);
            $note = ($v['remark'] ?? [])[$s->id] ?? null;

            AttendanceRecord::updateOrCreate(
                ['student_id'=>$s->id,'date'=>$v['date']],
                ['present'=>$isPresent,'remark'=>$note]
            );
        }

        return back()->with('ok','บันทึกการมาเรียนสำเร็จ');
    }
}
