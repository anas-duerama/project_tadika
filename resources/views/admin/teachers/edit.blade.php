@extends('admin.layout')
@section('title','แก้ไขผู้สอน')
@section('page_title','แก้ไขผู้สอน')

@section('content')
@php
  use Illuminate\Support\Facades\Route;
  $indexUrl = Route::has('admin.teachers.index') ? route('admin.teachers.index') : url('/admin/teachers');
  $updateUrl= Route::has('admin.teachers.update') ? route('admin.teachers.update',$teacher) : url('/admin/teachers/'.$teacher->id);
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
  <form id="teacherForm" action="{{ $updateUrl }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
   <div><label class="{{ $label }}">ชื่อ</label><input name="first_name" value="{{ old('first_name',$teacher->first_name) }}" class="{{ $input }}"></div>
  <div><label class="{{ $label }}">นามสกุล</label><input name="last_name"  value="{{ old('last_name',$teacher->last_name)   }}" class="{{ $input }}"></div>
      <div><label class="{{ $label }}">อีเมล</label><input type="email" name="email" value="{{ old('email',$teacher->email) }}" class="{{ $input }}"></div>
      <div><label class="{{ $label }}">เบอร์โทร</label><input name="phone" value="{{ old('phone',$teacher->phone) }}" class="{{ $input }}"></div> 
      <div>
        <label class="{{ $label }}">วิชาหลัก</label>
        @php $subjects = ['ไทย','มาลายู','นิด','มา']; @endphp
        <select name="primary_subject" class="{{ $select }}">
          <option value="">- เลือกวิชา -</option>
          @foreach($subjects as $s)
            <option value="{{ $s }}" @selected(old('primary_subject',$teacher->primary_subject)===$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>
      {{-- <div><label class="{{ $label }}">กลุ่มสาระ/แผนก</label><input name="department" value="{{ old('department',$teacher->department) }}" class="{{ $input }}"></div> --}}
      <div><label class="{{ $label }}">ห้องประจำ</label>
        <select name="homeroom" class="{{ $select }}"><option value="">- ไม่มี -</option>@foreach($rooms as $r)<option value="{{ $r }}" @selected(old('homeroom',$teacher->homeroom)===$r)>{{ $r }}</option>@endforeach</select>
      </div>
       <div><label class="{{ $label }}">วันที่เริ่มงาน</label><input type="date" name="hire_date" value="{{ old('hire_date', optional($teacher->hire_date)->format('Y-m-d')) }}" class="{{ $input }}"></div>
      <div class="md:col-span-2"><label class="{{ $label }}">ประวัติ/คำแนะนำ</label><textarea name="bio" rows="4" class="{{ $input }}">{{ old('bio',$teacher->bio) }}</textarea></div> 
      <div class="md:col-span-2">
        <label class="{{ $label }}">ห้องที่สอน (เลือกได้มากกว่า 1)</label>
        <div class="grid grid-cols-2 gap-2 mt-2">
            @php
              $rawSelected = old('teaching_rooms', $teacher->teaching_rooms ?? []);
              $selectedRooms = [];
              if (is_array($rawSelected)) {
                $selectedRooms = $rawSelected;
              } else {
                $raw = (string) $rawSelected;
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                  $selectedRooms = $decoded;
                } elseif ($raw !== '') {
                  $selectedRooms = preg_split('/\s*,\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
                }
              }
              $selectedRooms = array_map('trim', $selectedRooms);
            @endphp
            @foreach($rooms as $r)
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="teaching_rooms[]" value="{{ $r }}" @checked(in_array($r, $selectedRooms, true))>
                <span class="text-sm">{{ $r }}</span>
              </label>
            @endforeach
        </div>
      </div>
      <div>
        <label class="{{ $label }}">รูปโปรไฟล์</label>
        @if($teacher->photo_path)<img src="{{ asset('storage/'.$teacher->photo_path) }}" class="mt-1 h-24 rounded border object-cover">@endif
        <input type="file" name="photo" accept="image/*" class="{{ $input }} mt-2" onchange="previewPhoto(this)">
        <p class="{{ $hint }}">รองรับ jpg, jpeg, png, webp ขนาดไม่เกิน 2MB</p>
        <div id="photoPreviewWrap" class="mt-3 hidden"><img id="photoPreview" class="h-32 rounded-lg border object-cover" alt="Preview"></div>
      </div>
      <div><label class="{{ $label }}">สถานะ *</label>
        <select name="status" class="{{ $select }}"><option value="active" @selected(old('status',$teacher->status)==='active')>ปฏิบัติงาน</option><option value="inactive" @selected(old('status',$teacher->status)==='inactive')>ไม่ปฏิบัติงาน</option></select>
      </div>
    </div>
  </form>
</div>

<div class="mt-3 flex justify-end gap-2">
  <a href="{{ $indexUrl }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ยกเลิก</a>
  <button form="teacherForm" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">บันทึก</button>
</div>

<script>
function previewPhoto(input){
  const f=input.files&&input.files[0],wrap=document.getElementById('photoPreviewWrap'),img=document.getElementById('photoPreview');
  if(!f){wrap.classList.add('hidden');img.src='';return;}
  const r=new FileReader(); r.onload=e=>{img.src=e.target.result;wrap.classList.remove('hidden');}; r.readAsDataURL(f);
}
</script>

@endsection
 