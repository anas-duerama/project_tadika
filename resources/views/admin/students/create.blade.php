@extends('admin.layout')
@section('title','เพิ่มนักเรียน')
@section('page_title','เพิ่มข้อมูลนักเรียน')

@section('content')
@php
  use Illuminate\Support\Facades\Route;

  $indexUrl  = Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students');
  $storeUrl  = Route::has('admin.students.store') ? route('admin.students.store') : url('/admin/students');
  $adminHome = Route::has('admin.dashboard')      ? route('admin.dashboard')      : url('/admin/dashboard');

  // รายการห้อง
  $levels = $classLevels ?? ['ห้อง 1','ห้อง 2','ห้อง 3','ห้อง 4','ห้อง 5','ห้อง 6'];

  // ✨ Utility classes
  $label  = 'block text-sm font-medium text-gray-700';
  // ✅ ทำให้เห็นกรอบชัด (border) และโฟกัสสวยขึ้น
  $input  = 'mt-1 w-full bg-white border border-gray-300 rounded-xl px-3 py-2 shadow-sm
             focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-600
             placeholder-gray-400';
  $select = $input;
  $hint   = 'text-xs text-gray-400 mt-1';
@endphp

<div class="max-w-6xl mx-auto space-y-6">

  @if ($errors->any())
    <div class="rounded-xl bg-red-50 text-red-700 px-4 py-3 text-sm border border-red-100">
      โปรดตรวจสอบข้อมูลให้ครบถ้วน (มีฟิลด์ที่ยังไม่ถูกต้อง)
    </div>
  @endif

  <!-- การ์ดฟอร์ม -->
  <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
    <div class="px-5 py-4 border-b">
      <h2 class="font-bold">ข้อมูลนักเรียน</h2>
      <p class="text-sm text-gray-500">กรอกข้อมูลพื้นฐานและชั้นเรียน</p>
    </div>

    <form id="studentForm" action="{{ $storeUrl }}" method="POST" class="p-5">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- เลขที่ --}}
        <div>
          <label class="{{ $label }}">เลขที่ <span class="text-pink-600">*</span></label>
          <input type="number" name="no" value="{{ old('no') }}" min="1"
                 class="{{ $input }} @error('no') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="เช่น 1" required>
          @error('no')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- ห้อง --}}
        <div>
          <label class="{{ $label }}">ชั้นเรียน <span class="text-pink-600">*</span></label>
          <select name="class_level"
                  class="{{ $select }} @error('class_level') border-red-400 ring-2 ring-red-200 @enderror" required>
            <option value="" disabled {{ old('class_level') ? '' : 'selected' }}>เลือกชั้นเรียน</option>
            @foreach($levels as $level)
              <option value="{{ $level }}" @selected(old('class_level')===$level)>{{ $level }}</option>
            @endforeach
          </select>
          <p class="{{ $hint }}">เลือกห้อง 1–6</p>
          @error('class_level')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- ชื่อ --}}
        <div>
          <label class="{{ $label }}">ชื่อ <span class="text-pink-600">*</span></label>
          <input type="text" name="first_name" value="{{ old('first_name') }}"
                 class="{{ $input }} @error('first_name') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="ชื่อนักเรียน" required>
          @error('first_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- นามสกุล --}}
        <div>
          <label class="{{ $label }}">นามสกุล <span class="text-pink-600">*</span></label>
          <input type="text" name="last_name" value="{{ old('last_name') }}"
                 class="{{ $input }} @error('last_name') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="นามสกุล" required>
          @error('last_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- เลขประจำตัวนักเรียน --}}
        <div>
          <label class="{{ $label }}">เลขประจำตัวนักเรียน <span class="text-pink-600">*</span></label>
          <input type="text" name="student_code" value="{{ old('student_code') }}"
                 class="{{ $input }} @error('student_code') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="กรอกเลขประจำตัว" required>
          @error('student_code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- เลขบัตรประชาชน --}}
        <div>
          <label class="{{ $label }}">เลขบัตรประชาชน <span class="text-pink-600">*</span></label>
          <input type="text" name="citizen_id" value="{{ old('citizen_id') }}" maxlength="13"
                 class="{{ $input }} @error('citizen_id') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="กรอก 13 หลัก" required>
          <p class="{{ $hint }}">กรอก 13 หลัก ไม่ต้องใส่ขีด</p>
          @error('citizen_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- วันเกิด --}}
        <div>
          <label class="{{ $label }}">วันเกิด</label>
          <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                 class="{{ $input }} @error('birth_date') border-red-400 ring-2 ring-red-200 @enderror">
          @error('birth_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="my-6 border-t"></div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="{{ $label }}">ข้อมูลบิดา</label>
          <input type="text" name="father_name" value="{{ old('father_name') }}"
                 class="{{ $input }} @error('father_name') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="ชื่อบิดา">
          @error('father_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="{{ $label }}">ข้อมูลมารดา</label>
          <input type="text" name="mother_name" value="{{ old('mother_name') }}"
                 class="{{ $input }} @error('mother_name') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="ชื่อมารดา">
          @error('mother_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="my-6 border-t"></div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="{{ $label }}">เบอร์โทร</label>
          <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="10"
                 class="{{ $input }} @error('phone') border-red-400 ring-2 ring-red-200 @enderror"
                 placeholder="081xxxxxxx">
          @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </form>
  </div>
</div>

<!-- STICKY ACTION BAR -->
<div class="sticky bottom-0 bg-white/90 backdrop-blur border-t mt-6">
  <div class="max-w-6xl mx-auto px-4 md:px-6 py-3 flex items-center justify-end gap-2">
    <a href="{{ $indexUrl }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ยกเลิก</a>
    <button form="studentForm" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">บันทึก</button>
  </div>
</div>

@endsection
