{{-- resources/views/admin/teachers/create.blade.php --}}
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
<!DOCTYPE html><html lang="th"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>เพิ่มผู้สอน</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100 text-gray-800">
<header class="bg-sky-300 border-b border-sky-400 shadow-sm">
  <div class="max-w-6xl mx-auto h-16 px-4 md:px-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ $adminHome }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-sky-400 hover:bg-sky-500 text-sky-950">← กลับหน้า Admin</a>
      <h1 class="text-lg md:text-xl font-extrabold text-sky-950">เพิ่มผู้สอน</h1>
    </div>
    <a href="{{ $indexUrl }}" class="px-3 py-1.5 rounded-lg bg-white/70 hover:bg-white text-sm text-gray-800">← กลับรายการ</a>
  </div>
</header>

<main class="px-4 md:px-6 py-6">
  <div class="max-w-6xl mx-auto space-y-6">
    @if ($errors->any())
      <div class="rounded-xl bg-red-50 text-red-700 px-4 py-3 text-sm border border-red-100">โปรดตรวจสอบข้อมูลให้ครบถ้วน</div>
    @endif
    <div class="bg-white rounded-2xl border p-5">
      <form id="teacherForm" action="{{ $storeUrl }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div><label class="{{ $label }}">ชื่อ *</label><input name="first_name" value="{{ old('first_name') }}" class="{{ $input }}" required></div>
          <div><label class="{{ $label }}">นามสกุล *</label><input name="last_name"  value="{{ old('last_name')  }}" class="{{ $input }}" required></div>
          <div><label class="{{ $label }}">อีเมล</label><input type="email" name="email" value="{{ old('email') }}" class="{{ $input }}"></div>
          <div><label class="{{ $label }}">เบอร์โทร</label><input name="phone" value="{{ old('phone') }}" class="{{ $input }}" placeholder="081xxxxxxx"></div>
          <div><label class="{{ $label }}">วิชาหลัก</label><input name="primary_subject" value="{{ old('primary_subject') }}" class="{{ $input }}" placeholder="เช่น คณิตศาสตร์"></div>
          <div><label class="{{ $label }}">กลุ่มสาระ/แผนก</label><input name="department" value="{{ old('department') }}" class="{{ $input }}" placeholder="เช่น วิทยาศาสตร์"></div>
          <div><label class="{{ $label }}">ห้องประจำ</label>
            <select name="homeroom" class="{{ $select }}"><option value="">- ไม่มี -</option>@foreach($rooms as $r)<option value="{{ $r }}" @selected(old('homeroom')===$r)>{{ $r }}</option>@endforeach</select>
          </div>
          <div><label class="{{ $label }}">วันที่เริ่มงาน</label><input type="date" name="hire_date" value="{{ old('hire_date') }}" class="{{ $input }}"></div>
          <div class="md:col-span-2"><label class="{{ $label }}">ประวัติ/คำแนะนำ</label><textarea name="bio" rows="4" class="{{ $input }}">{{ old('bio') }}</textarea></div>
          <div><label class="{{ $label }}">รูปโปรไฟล์</label>
            <input type="file" name="photo" accept="image/*" class="{{ $input }}" onchange="previewPhoto(this)">
            <p class="{{ $hint }}">รองรับ jpg, jpeg, png, webp ขนาดไม่เกิน 2MB</p>
            <div id="photoPreviewWrap" class="mt-3 hidden"><img id="photoPreview" class="h-32 rounded-lg border object-cover" alt="Preview"></div>
          </div>
          <div><label class="{{ $label }}">สถานะ *</label>
            <select name="status" class="{{ $select }}"><option value="active" @selected(old('status','active')==='active')>ปฏิบัติงาน</option><option value="inactive" @selected(old('status')==='inactive')>ไม่ปฏิบัติงาน</option></select>
          </div>
        </div>
      </form>
    </div>
  </div>
</main>

<footer class="sticky bottom-0 bg-white/90 backdrop-blur border-t">
  <div class="max-w-6xl mx-auto px-4 md:px-6 py-3 flex items-center justify-end gap-2">
    <a href="{{ $indexUrl }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ยกเลิก</a>
    <button form="teacherForm" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">บันทึก</button>
  </div>
</footer>

<script>
function previewPhoto(input){
  const f=input.files&&input.files[0],wrap=document.getElementById('photoPreviewWrap'),img=document.getElementById('photoPreview');
  if(!f){wrap.classList.add('hidden');img.src='';return;}
  const r=new FileReader(); r.onload=e=>{img.src=e.target.result;wrap.classList.remove('hidden');}; r.readAsDataURL(f);
}
</script>
</body></html>
