{{-- resources/views/admin/students/edit.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;
  $adminHome = Route::has('admin') ? route('admin') : url('/admin');
  $updateUrl = route('admin.students.update', $student);
  $indexUrl  = route('admin.students.index');
  $levels    = $classLevels ?? ['ห้อง 1','ห้อง 2','ห้อง 3','ห้อง 4','ห้อง 5','ห้อง 6'];
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>แก้ไขข้อมูลนักเรียน</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6">
  <h1 class="font-bold">แก้ไขข้อมูลนักเรียน</h1>
  
</header>

<main class="px-4 md:px-6 py-6">
  <div class="max-w-6xl mx-auto bg-white rounded-2xl border border-gray-200 p-6">
    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">โปรดตรวจสอบข้อมูลที่กรอกให้ครบถ้วน</div>
    @endif

    <form id="studentForm" action="{{ $updateUrl }}" method="POST" novalidate>
      @csrf @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- เลขที่ --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">เลขที่ <span class="text-pink-600">*</span></label>
          <input type="number" name="no" value="{{ old('no', $student->no) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
          @error('no') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- ห้อง --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">ห้อง <span class="text-pink-600">*</span></label>
          <select name="class_level" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="" disabled>เลือกห้อง</option>
            @foreach($levels as $lv)
              <option value="{{ $lv }}" {{ old('class_level', $student->class_level)===$lv ? 'selected':'' }}>{{ $lv }}</option>
            @endforeach
          </select>
          @error('class_level') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- ชื่อ / นามสกุล --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">ชื่อ <span class="text-pink-600">*</span></label>
          <input type="text" name="first_name" value="{{ old('first_name',$student->first_name) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
          @error('first_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">นามสกุล <span class="text-pink-600">*</span></label>
          <input type="text" name="last_name" value="{{ old('last_name',$student->last_name) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
          @error('last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- รหัสนักเรียน / บัตรประชาชน --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">เลขประจำตัวนักเรียน <span class="text-pink-600">*</span></label>
          <input type="text" name="student_code" value="{{ old('student_code',$student->student_code) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
          @error('student_code') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">เลขบัตรประชาชน <span class="text-pink-600">*</span></label>
          <input type="text" name="citizen_id" value="{{ old('citizen_id',$student->citizen_id) }}"
                 inputmode="numeric" maxlength="13" pattern="\d{13}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
          @error('citizen_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- อื่น ๆ --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">วันเกิด</label>
          <input type="date" name="birthdate" value="{{ old('birthdate',$student->birthdate) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">ข้อมูลบิดา</label>
          <input type="text" name="father_name" value="{{ old('father_name',$student->father_name) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="ชื่อบิดา">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">อาชีพบิดา</label>
          <input type="text" name="father_job" value="{{ old('father_job',$student->father_job) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">ข้อมูลมารดา</label>
          <input type="text" name="mother_name" value="{{ old('mother_name',$student->mother_name) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="ชื่อมารดา">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">อาชีพมารดา</label>
          <input type="text" name="mother_job" value="{{ old('mother_job',$student->mother_job) }}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">เบอร์โทร</label>
          <input type="text" name="phone" value="{{ old('phone',$student->phone) }}"
                 inputmode="tel" maxlength="10" pattern="0\d{8,9}"
                 class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="081xxxxxxxx">
        </div>
        <div class="flex gap-2">
  <a href="{{ $adminHome }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">← กลับหน้า Admin</a>

</div>

      </div>

      <div class="mt-6 flex justify-end gap-2">
        <a href="{{ $indexUrl }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ยกเลิก</a>
        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">บันทึก</button>
      </div>
    </form>
  </div>
</main>
</body>
</html>
