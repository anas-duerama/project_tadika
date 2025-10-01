<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    private array $rooms = ['ห้อง 1','ห้อง 2','ห้อง 3','ห้อง 4','ห้อง 5','ห้อง 6'];

    public function index(Request $request)
    {
        $q = (string) $request->get('q', '');

        // รองรับหลายรูปแบบที่อาจบันทึกไว้ เช่น teacher / teachers / Teacher / TEACHER
        $rows = User::query()
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('email', 'like', "%{$q}%")
                      ->orWhere('name',  'like', "%{$q}%");
                });
            })
            ->where(function ($w) {
                $w->where('role', 'teacher')
                  ->orWhere('role', 'teachers')
                  ->orWhere('role', 'Teacher')
                  ->orWhere('role', 'TEACHER');
            })
            ->with('teacher')                 // ถ้าคุณมีตาราง teachers (โปรไฟล์การสอน) จะดึงมาด้วย
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.teachers.index', compact('rows', 'q'));
    }

    public function create(Request $request)
    {
        $rooms = $this->rooms;
        $userId = $request->get('user');
        $candidates = [];
        if (empty($userId)) {
            // list users with teacher role who don't yet have a teacher profile
            $candidates = User::teachers()->whereDoesntHave('teacher')->orderBy('name')->get();
        }
        return view('admin.teachers.create', compact('rooms','userId','candidates'));
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('teacher_photos','public');
        }
        // ensure teaching_rooms is stored as array (model casts to array)
        if (isset($data['teaching_rooms']) && !is_array($data['teaching_rooms'])) {
            $data['teaching_rooms'] = (array) $data['teaching_rooms'];
        }
        // default status if not provided
        $data['status'] = $data['status'] ?? 'active';
        // if user_id was included in the form, ensure it's set
        if (isset($data['user_id']) && $data['user_id'] === '') unset($data['user_id']);
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
        \Log::info('Teacher update payload', $request->all());
        if ($request->hasFile('photo')) {
            if ($teacher->photo_path) Storage::disk('public')->delete($teacher->photo_path);
            $data['photo_path'] = $request->file('photo')->store('teacher_photos','public');
        }
        if (isset($data['teaching_rooms']) && !is_array($data['teaching_rooms'])) {
            $data['teaching_rooms'] = (array) $data['teaching_rooms'];
        }
        // ensure status remains set
        $data['status'] = $data['status'] ?? $teacher->status ?? 'active';
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
