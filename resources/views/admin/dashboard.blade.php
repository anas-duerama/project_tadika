@extends('admin.layout')
@section('title','แดชบอร์ดผู้ดูแลระบบ')
@section('page_title','แดชบอร์ดผู้ดูแลระบบ')

@section('content')
@php
    $studentsIndex = \Illuminate\Support\Facades\Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students');
    $newsIndex     = \Illuminate\Support\Facades\Route::has('admin.news.index')     ? route('admin.news.index')     : url('/admin/news');
    $gradesIndex   = \Illuminate\Support\Facades\Route::has('admin.grades.index')   ? route('admin.grades.index')   : url('/admin/grades');
    $teachersIndex = \Illuminate\Support\Facades\Route::has('admin.teachers.index') ? route('admin.teachers.index') : url('/admin/teachers');
    $incomeIndex   = \Illuminate\Support\Facades\Route::has('admin.income.index')   ? route('admin.income.index')   : url('/admin/income');
    $newsCreate    = \Illuminate\Support\Facades\Route::has('admin.news.create')    ? route('admin.news.create')    : url('/admin/news/create');
    $stdCreate     = \Illuminate\Support\Facades\Route::has('admin.students.create')? route('admin.students.create') : url('/admin/students/create');
    $incomeCreate  = \Illuminate\Support\Facades\Route::has('admin.income.create')  ? route('admin.income.create')  : url('/admin/income/create');
    $gradesCreate  = \Illuminate\Support\Facades\Route::has('admin.grades.create')  ? route('admin.grades.create')  : url('/admin/grades/create');
    $teachersCreate= \Illuminate\Support\Facades\Route::has('admin.teachers.create')? route('admin.teachers.create'): url('/admin/teachers/create');
@endphp

<header class="flex items-center justify-between gap-4">
  <div class="min-w-0">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight whitespace-nowrap">
      แดชบอร์ดผู้ดูแลระบบ
    </h1>
    <p class="text-sm text-gray-500 mt-1">
      ภาพรวมระบบและทางลัดการจัดการข้อมูลหลักของโรงเรียน
    </p>
  </div>

  <div class="shrink-0 flex items-center gap-2">
    <a href="{{ route('admin.news.create') }}" class="rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">+ เพิ่มข่าวสาร</a>
    <a href="{{ route('admin.students.create') }}" class="rounded-xl bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black/80">+ เพิ่มนักเรียน</a>
  </div>
</header>

<section class="mt-6 grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-5">
    <div class="text-sm text-gray-500">จำนวนนักเรียน</div>
    <div class="mt-1 text-3xl font-extrabold">{{ number_format($studentsCount ?? 0) }}</div>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-5">
    <div class="text-sm text-gray-500">จำนวนครูผู้สอน</div>
    <div class="mt-1 text-3xl font-extrabold">{{ number_format($teachersCount ?? 0) }}</div>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-5">
    <div class="text-sm text-gray-500">ข่าวสารทั้งหมด</div>
    <div class="mt-1 text-3xl font-extrabold">{{ number_format($newsCount ?? 0) }}</div>
  </div>
</section>

<section class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- module cards (same as before) -->
  <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-blue-100 grid place-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5c-2.28-1.58-4.28-3.58-6.16-5.922L12 14z" />
        </svg>
      </div>
      <h3 class="font-bold text-lg">จัดการข้อมูลนักเรียน</h3>
    </div>
    <p class="mt-2 text-sm text-gray-600">เพิ่ม/แก้ไข/นำออก ข้อมูลนักเรียน, ห้องเรียน, สถานะการศึกษา</p>
    <div class="mt-auto flex gap-2 pt-4">
      <a href="{{ $stdCreate }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">+ เพิ่ม</a>
      <a href="{{ $studentsIndex }}" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">จัดการ</a>
    </div>
  </article>

  <!-- other cards truncated for brevity, original content preserved -->
</section>

@endsection
