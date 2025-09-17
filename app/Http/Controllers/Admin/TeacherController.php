<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    private array $rooms = ['ห้อง 1','ห้อง 2','ห้อง 3','ห้อง 4','ห้อง 5','ห้อง 6'];

    public function index(Request $request)
    {
        $q    = trim((string)$request->get('q',''));
        $room = $request->get('room','');

        $teachers = Teacher::query()
            ->when($q, fn($w) => $w->where(function($x) use ($q) {
                $x->where('first_name','like',"%$q%")
                  ->orWhere('last_name','like',"%$q%")
                  ->orWhere('primary_subject','like',"%$q%");
            }))
            ->when($room, fn($w) => $w->where('homeroom', $room))
            ->orderBy('first_name')->orderBy('last_name')
            ->paginate(12)->withQueryString();

        $rooms = $this->rooms;
        return view('admin.teachers.index', compact('teachers','rooms','q','room'));
    }

    public function create()
    {
        $rooms = $this->rooms;
        return view('admin.teachers.create', compact('rooms'));
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('teacher_photos','public');
        }
        Teacher::create($data);
        return redirect()->route('admin.teachers.index')->with('success','เพิ่มผู้สอนเรียบร้อย');
    }

    public function edit(Teacher $teacher)
    {
        $rooms = $this->rooms;
        return view('admin.teachers.edit', compact('teacher','rooms'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            if ($teacher->photo_path) Storage::disk('public')->delete($teacher->photo_path);
            $data['photo_path'] = $request->file('photo')->store('teacher_photos','public');
        }
        $teacher->update($data);
        return redirect()->route('admin.teachers.index')->with('success','แก้ไขข้อมูลผู้สอนเรียบร้อย');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo_path) Storage::disk('public')->delete($teacher->photo_path);
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success','ลบผู้สอนเรียบร้อย');
    }
}
