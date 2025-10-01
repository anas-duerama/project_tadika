@extends('admin.layout')
@section('title','เพิ่มผู้สอน')
@section('page_title','เพิ่มผู้สอน')

@section('content')
@php
  use Illuminate\Support\Facades\Route;
  $indexUrl = Route::has('admin.teachers.index') ? route('admin.teachers.index') : url('/admin/teachers');
  $storeUrl = Route::has('admin.teachers.store') ? route('admin.teachers.store') : url('/admin/teachers');
  $adminHome= Route::has('admin.dashboard')      ? route('admin.dashboard')      : url('/admin/dashboard');
  $rooms = $rooms ?? ['ห้อง 1','ห้อง 2','ห้อง 3','ห้อง 4','ห้อง 5','ห้อง 6'];
  $label='block text-sm font-medium text-gray-700';
  $input='mt-1 w-full bg-white border border-gray-300 rounded-xl px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-600 placeholder-gray-400';
  $select=$input; $hint='text-xs text-gray-400 mt-1';
@endphp

@if ($errors->any())
  <div class="rounded-xl bg-red-50 text-red-700 px-4 py-3 text-sm border border-red-100">โปรดตรวจสอบข้อมูลให้ครบถ้วน</div>
@endif
<div class="bg-white rounded-2xl border p-5">
  <form id="teacherForm" action="{{ $storeUrl }}" method="POST">@csrf
    @if(!empty($userId))
      <input type="hidden" name="user_id" value="{{ $userId }}">
    @elseif(!empty($candidates) && $candidates->count())
      <div class="mb-4">
        <label class="{{ $label }}">เลือกบัญชีผู้ใช้ (ผูกกับโปรไฟล์)</label>
        <select name="user_id" class="{{ $select }}">
          <option value="">- เลือกผู้ใช้ (ไม่จำเป็น) -</option>
          @foreach($candidates as $cand)
            <option value="{{ $cand->id }}" @selected(old('user_id')==$cand->id)>{{ $cand->name }} — {{ $cand->email }}</option>
          @endforeach
        </select>
      </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @php $subjects = ['ไทย','มาลายู','นิด','มา']; @endphp
      <div>
        <label class="{{ $label }}">วิชาหลัก</label>
        <select name="primary_subject" class="{{ $select }}">
          <option value="">- เลือกวิชา -</option>
          @foreach($subjects as $s)
            <option value="{{ $s }}" @selected(old('primary_subject')===$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="{{ $label }}">ห้องประจำ</label>
        <select name="homeroom" class="{{ $select }}">
          <option value="">- ไม่มี -</option>
          @foreach($rooms as $r)
            <option value="{{ $r }}" @selected(old('homeroom')===$r)>{{ $r }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="{{ $label }}">ห้องที่สอน (เลือกได้มากกว่า 1)</label>
        <div class="grid grid-cols-2 gap-2 mt-2">
          @foreach($rooms as $r)
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="teaching_rooms[]" value="{{ $r }}" @checked(in_array($r, old('teaching_rooms',[])))>
              <span class="text-sm">{{ $r }}</span>
            </label>
          @endforeach
        </div>
      </div>
    </div>
  </form>
</div>

<div class="mt-3 flex justify-end gap-2">
  <a href="{{ $indexUrl }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ยกเลิก</a>
  <button form="teacherForm" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">บันทึก</button>
</div>

{{-- preview script removed (no photo upload on this simplified form) --}}

@endsection
