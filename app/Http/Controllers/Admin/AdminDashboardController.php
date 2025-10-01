<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\News;
use App\Models\Grade;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $studentsCount = class_exists(Student::class)
            ? Student::count()
            : (DB::getSchemaBuilder()->hasTable('students') ? DB::table('students')->count() : 0);

        // ถ้ามีตาราง/โมเดล Teacher ใช้ Teacher::count()
        // ถ้าเก็บครูใน users ให้นับ role = 'teacher'
        $teachersCount = class_exists(Teacher::class)
            ? Teacher::count()
            : (class_exists(User::class)
                ? User::where('role','teacher')->count()
                : (DB::getSchemaBuilder()->hasTable('users') ? DB::table('users')->where('role','teacher')->count() : 0));

        $newsCount = class_exists(News::class)
            ? News::count()
            : (DB::getSchemaBuilder()->hasTable('news') ? DB::table('news')->count() : 0);

        // มีการ์ด "รายการผลการเรียน" ในไฟล์ด้วย (บรรทัด 133–135)
        $gradesCount = class_exists(Grade::class)
            ? Grade::count()
            : (DB::getSchemaBuilder()->hasTable('grades') ? DB::table('grades')->count() : 0);

        return view('admin.dashboard', compact('studentsCount','teachersCount','newsCount','gradesCount'));
    }
}
