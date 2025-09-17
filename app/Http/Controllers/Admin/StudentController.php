<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;


class StudentController extends Controller
{
    public function index()
    {
        // รายการห้อง 1..6
    $classLevels = collect(range(1, 6))
        ->map(fn ($i) => "ห้อง {$i}")
        ->toArray();

    // ดึงนักเรียนทั้งหมดแล้ว group ตามห้อง
    $studentsByRoom = \App\Models\Student::select('id','no','first_name','last_name','student_code','class_level')
        ->orderBy('no')                 // เรียงเลขที่ในห้อง
        ->get()
        ->groupBy('class_level');       // ได้เป็นคอลเลกชัน keyed ด้วย 'ห้อง X'

    return view('admin.students.index', compact('classLevels','studentsByRoom'));
    }

    public function create()
    {
        $classLevels = collect(range(1,6))->map(fn($i)=>"ห้อง {$i}")->toArray();
        return view('admin.students.create', compact('classLevels'));
    }

    public function store(StoreStudentRequest $request)
    {
        Student::create($request->validated());
        return redirect()->route('admin.students.index')->with('success','เพิ่มนักเรียนเรียบร้อย');
    }

    public function edit(Student $student)
    {
        $classLevels = collect(range(1,6))->map(fn($i)=>"ห้อง {$i}")->toArray();
        return view('admin.students.edit', compact('student','classLevels'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        return redirect()->route('admin.students.index')->with('success','แก้ไขข้อมูลเรียบร้อย');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success','ลบนักเรียนเรียบร้อย');
    }
}
