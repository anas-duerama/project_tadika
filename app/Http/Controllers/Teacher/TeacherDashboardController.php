<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use App\Models\AttendanceRecord;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'classes' => 2, // ปรับเป็นการนับจริงได้
            'students' => Student::count(),
            'attendanceToday' => AttendanceRecord::whereDate('date', today())->count(),
            'pendingGrades' => Grade::whereNull('score')->count(),
        ];

        // ตัวอย่างข้อมูลสรุปห้อง (ปรับเป็น query จริงได้)
        $classes = [
            ['name' => 'ป.6/1', 'students' => 30, 'presentToday' => 28, 'pending' => 3],
            ['name' => 'ป.6/2', 'students' => 28, 'presentToday' => 27, 'pending' => 1],
        ];

        return view('teacher.dashboard', compact('stats','classes'));
    }
}
