<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\AdminDashboardController;


// ====== Teacher Controllers ======
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherStudentController;
use App\Http\Controllers\Teacher\TeacherGradeController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
 use App\Http\Controllers\Teacher\QuickGradeController;


Route::get('/', function () {
    return view('welcome');
});

// Convenience route: /index → redirect to teacher dashboard (if exists) or home
if (Route::has('teacher.dashboard')) {
    Route::get('/index', function () { return redirect()->route('teacher.dashboard'); })->name('index');
} else {
    Route::get('/index', function () { return redirect('/'); })->name('index');
}

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ----- กลุ่ม ADMIN (ตัวอย่าง; คุณมีอยู่แล้ว) -----
Route::middleware(['auth','role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
       Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
        Route::resource('students', AdminStudentController::class);
        Route::resource('news', AdminNewsController::class);
        Route::resource('teachers', AdminTeacherController::class);
    });

// ----- กลุ่ม TEACHER -----
Route::middleware(['auth','role:teacher'])
    ->prefix('teacher')->name('teacher.')
    ->group(function () {
        Route::get('/', [TeacherDashboardController::class,'index'])->name('dashboard');

        Route::get('/students', [TeacherStudentController::class,'index'])->name('students.index');

        Route::get('/grades', [TeacherGradeController::class,'index'])->name('grades.index');
        Route::post('/grades', [TeacherGradeController::class,'store'])->name('grades.store');

        Route::get('/attendance', [TeacherAttendanceController::class,'index'])->name('attendance.index');
        Route::post('/attendance', [TeacherAttendanceController::class,'store'])->name('attendance.store');

        Route::get('/grades/quick',  [QuickGradeController::class, 'quick'])->name('grades.quick');
        Route::post('/grades/quick', [QuickGradeController::class, 'storeQuick'])->name('grades.quick.store');
    // CSV export removed: Route no longer provided
    // หน้าไวกรอกคะแนน (เราเรียก redirect ไปอันนี้)
        Route::get('/grades/quick',  [QuickGradeController::class, 'quick'])->name('grades.quick');
        Route::post('/grades/quick', [QuickGradeController::class, 'storeQuick'])->name('grades.quick.store');

    Route::get('/history', [TeacherAttendanceController::class,'history'])->name('history.index');
        
    });

// Simple teacher profile route to show teacher index/profile page
Route::middleware(['auth','role:teacher'])
    ->prefix('teacher')->name('teacher.')
    ->get('/profile', function () { return view('teacher.index'); })->name('profile');

// Temporary debug route - return attendance counts and sample records
// NOTE: remove this in production
Route::get('/_debug/attendance-records', function () {
    $model = \App\Models\AttendanceRecord::class;
    $count = $model::count();
    $samples = $model::with('student')->latest('date')->take(5)->get()->map(function($r){
        return [
            'id' => $r->id,
            'date' => $r->date ? $r->date->toDateString() : null,
            'student_id' => $r->student_id,
            'student_code' => $r->student ? $r->student->student_code : null,
            'student_name' => $r->student ? $r->student->fullname : null,
            'present' => (bool) $r->present,
            'status' => $r->status,
            'remark' => $r->remark,
        ];
    });
    $dates = $model::selectRaw('date')->distinct()->orderBy('date','desc')->limit(10)->pluck('date');
    return response()->json(['count' => $count, 'samples' => $samples, 'dates' => $dates]);
})->name('_debug.attendance');
