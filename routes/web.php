<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;

// ====== Teacher Controllers ======
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherStudentController;
use App\Http\Controllers\Teacher\TeacherGradeController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;

Route::get('/', function () {
    return view('welcome');
});

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
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
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
    });
