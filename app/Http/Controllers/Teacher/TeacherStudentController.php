<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class TeacherStudentController extends Controller
{
    private string $classCol = 'class_level'; // เปลี่ยนชื่อคอลัมน์ได้ที่นี่
    private string $roomCol  = 'room';        // เปลี่ยนชื่อคอลัมน์ได้ที่นี่

   // app/Http/Controllers/Teacher/TeacherStudentController.php
public function index(Request $request)
{
    $classCol = 'class_level';
    $roomCol  = 'room';

    $q      = (string) $request->get('q');
    $class  = (string) $request->get('class');
    $room   = (string) $request->get('room');
    $per    = (int) $request->get('per_page', 20);
    $sortBy = $request->get('sort_by', 'student_code');
    $order  = $request->get('order', 'asc');

    $allowedSort = ['student_code','fullname','class_level','room'];
    if (!in_array($sortBy, $allowedSort, true)) $sortBy = 'student_code';
    $order = strtolower($order)==='desc' ? 'desc' : 'asc';

    $rows = Student::query()
      ->when($q, fn($qr)=>$qr->where(fn($w)=>$w
          ->where('student_code','like',"%$q%")
          ->orWhere('fullname','like',"%$q%")))
      ->when($class, fn($qr)=>$qr->where($classCol,$class))
      ->when($room,  fn($qr)=>$qr->where($roomCol,$room))
      ->orderBy($sortBy, $order)
      ->paginate($per)
      ->withQueryString();

    // ถ้าต้องการแสดงสรุปด้านบน
    $summary = [
        'total'       => (clone $rows)->total(), // ทั้งหมดตรงตามกรอง
        'class_count' => Student::when($q, fn($qr)=>$qr->where(fn($w)=>$w
                                ->where('student_code','like',"%$q%")
                                ->orWhere('fullname','like',"%$q%")))
                          ->when($class, fn($qr)=>$qr->where($classCol,$class))
                          ->when($room,  fn($qr)=>$qr->where($roomCol,$room))
                          ->distinct()->count($classCol),
        'room_count'  => Student::when($q, fn($qr)=>$qr->where(fn($w)=>$w
                                ->where('student_code','like',"%$q%")
                                ->orWhere('fullname','like',"%$q%")))
                          ->when($class, fn($qr)=>$qr->where($classCol,$class))
                          ->when($room,  fn($qr)=>$qr->where($roomCol,$room))
                          ->distinct()->count($roomCol),
    ];

    return view('teacher.students.index', compact('rows','summary'));
}

}
