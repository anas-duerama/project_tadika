@extends('admin.layout')
@section('title','รายชื่อนักเรียน')
@section('page_title','รายชื่อนักเรียน (แยกตามห้อง)')

@section('content')
@php
    use Illuminate\Support\Facades\Route as R;

    // URLs หลัก (กัน error ถ้า route บางตัวไม่มี)
    $dashboardUrl     = R::has('admin.dashboard')       ? route('admin.dashboard')       : url('/admin/dashboard');
    $studentsIndexUrl = R::has('admin.students.index')  ? route('admin.students.index')  : url('/admin/students');
    $newsIndexUrl     = R::has('admin.news.index')      ? route('admin.news.index')      : url('/admin/news');
    $gradesIndexUrl   = R::has('admin.grades.index')    ? route('admin.grades.index')    : (R::has('teacher.grades.index') ? route('teacher.grades.index') : url('/teacher/grades'));
    $teachersIndexUrl = R::has('admin.teachers.index')  ? route('admin.teachers.index')  : url('/admin/teachers');
    $incomeIndexUrl   = R::has('admin.income.index')    ? route('admin.income.index')    : url('/admin/income');

    $studentCreateUrl = R::has('admin.students.create') ? route('admin.students.create') : url('/admin/students/create');
@endphp

<div class="max-w-6xl">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach(($classLevels ?? []) as $room)
      <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-bold text-lg">{{ $room }}</h2>
          <a href="{{ $studentCreateUrl }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700">+ เพิ่มนักเรียน</a>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="border-b text-gray-500">
                <th class="py-2 pr-4 text-left">เลขที่</th>
                <th class="py-2 pr-4 text-left">ชื่อ-สกุล</th>
                <th class="py-2 pr-4 text-left">รหัสนักเรียน</th>
                <th class="py-2 pr-0 text-right">จัดการ</th>
              </tr>
            </thead>
            <tbody>
            @forelse(($studentsByRoom[$room] ?? collect()) as $st)
              <tr class="border-b last:border-0">
                <td class="py-2 pr-4">{{ $st->no }}</td>
                <td class="py-2 pr-4">{{ $st->first_name }} {{ $st->last_name }}</td>
                <td class="py-2 pr-4">{{ $st->student_code }}</td>
                <td class="py-2 pr-0 text-right">
                  @if(R::has('admin.students.edit'))
                    <a href="{{ route('admin.students.edit', $st) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 mr-2">แก้ไข</a>
                  @endif
                  @if(R::has('admin.students.destroy'))
                    <form action="{{ route('admin.students.destroy', $st) }}" method="POST" class="inline"
                          onsubmit="return confirm('ยืนยันลบนักเรียนคนนี้?')">
                      @csrf @method('DELETE')
                      <button class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">ลบ</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="py-3 text-gray-500">ยังไม่มีนักเรียนในห้องนี้</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  </div>
</div>

@endsection
