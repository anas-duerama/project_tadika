{{-- resources/views/admin/news/create.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;

  $indexUrl  = Route::has('admin.news.index') ? route('admin.news.index') : url('/admin/news');
  $storeUrl  = Route::has('admin.news.store') ? route('admin.news.store') : url('/admin/news');
  $adminHome = Route::has('admin')  ? route('admin')  : url('/admin/dashboard');

  // หมวดหมู่ fallback หาก controller ไม่ได้ส่งมา
  $categories = $categories ?? ['ประกาศ','กิจกรรม','รับสมัคร','ข่าวทั่วไป','ประชาสัมพันธ์'];

  // utility classes (อินพุตมีกรอบชัด + โฟกัสสวย)
  $label  = 'block text-sm font-medium text-gray-700';
  $input  = 'mt-1 w-full bg-white border border-gray-300 rounded-xl px-3 py-2 shadow-sm
             focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-600
             placeholder-gray-400';
  $select = $input;
  $hint   = 'text-xs text-gray-400 mt-1';
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>เพิ่มข่าวสาร</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>body{font-family: ui-sans-serif, system-ui, -apple-system,"Segoe UI",Roboto}</style>
</head>
<body class="bg-gray-100 text-gray-800">

  <!-- HEADER: ฟ้าเข้มขึ้นนิดหน่อย -->
  <header class="bg-sky-300 border-b border-sky-400 shadow-sm">
    <div class="max-w-6xl mx-auto h-16 px-4 md:px-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ $adminHome }}"
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-sky-400 hover:bg-sky-500 text-sky-950">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          กลับหน้า Admin
        </a>
        <h1 class="text-lg md:text-xl font-extrabold text-sky-950">เพิ่มข่าวสาร</h1>
      </div>
      <a href="{{ $indexUrl }}" class="px-3 py-1.5 rounded-lg bg-white/70 hover:bg-white text-sm text-gray-800">← กลับรายการข่าว</a>
    </div>
  </header>

  <main class="px-4 md:px-6 py-6">
    <div class="max-w-6xl mx-auto space-y-6">

      @if ($errors->any())
        <div class="rounded-xl bg-red-50 text-red-700 px-4 py-3 text-sm border border-red-100">
          โปรดตรวจสอบข้อมูลให้ครบถ้วน (มีฟิลด์ที่ยังไม่ถูกต้อง)
        </div>
      @endif

      <!-- การ์ดฟอร์ม -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b">
          <h2 class="font-bold">รายละเอียดข่าว</h2>
          <p class="text-sm text-gray-500">กรอกหัวข้อ เนื้อหา หมวดหมู่ และการเผยแพร่</p>
        </div>

        <form id="newsForm" action="{{ $storeUrl }}" method="POST" enctype="multipart/form-data" class="p-5" novalidate>
          @csrf

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- หัวข้อ --}}
            <div class="md:col-span-2">
              <label class="{{ $label }}">หัวข้อ <span class="text-pink-600">*</span></label>
              <input name="title" value="{{ old('title') }}"
                     class="{{ $input }} @error('title') border-red-400 ring-2 ring-red-200 @enderror"
                     placeholder="เช่น ประกาศปิดภาคเรียน 1/2568" required>
              @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- หมวดหมู่ --}}
            <div>
              <label class="{{ $label }}">หมวดหมู่ <span class="text-pink-600">*</span></label>
              <select name="category"
                      class="{{ $select }} @error('category') border-red-400 ring-2 ring-red-200 @enderror" required>
                <option value="" disabled {{ old('category') ? '' : 'selected' }}>เลือกหมวดหมู่</option>
                @foreach($categories as $c)
                  <option value="{{ $c }}" @selected(old('category')===$c)>{{ $c }}</option>
                @endforeach
              </select>
              @error('category')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- สถานะ --}}
            <div>
              <label class="{{ $label }}">สถานะ <span class="text-pink-600">*</span></label>
              <select name="status"
                      class="{{ $select }} @error('status') border-red-400 ring-2 ring-red-200 @enderror" required>
                <option value="draft" @selected(old('status','draft')==='draft')>ฉบับร่าง</option>
                <option value="published" @selected(old('status')==='published')>เผยแพร่</option>
              </select>
              @error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- วันที่เผยแพร่ --}}
            <div>
              <label class="{{ $label }}">วันที่เผยแพร่</label>
              <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                     class="{{ $input }} @error('published_at') border-red-400 ring-2 ring-red-200 @enderror">
              <p class="{{ $hint }}">ว่างไว้ได้ ถ้าจะกำหนดภายหลัง</p>
              @error('published_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- คำเกริ่น --}}
            <div class="md:col-span-2">
              <label class="{{ $label }}">คำเกริ่น (ไม่บังคับ)</label>
              <input name="excerpt" value="{{ old('excerpt') }}"
                     class="{{ $input }} @error('excerpt') border-red-400 ring-2 ring-red-200 @enderror"
                     placeholder="สรุปสั้น ๆ ของข่าวเพื่อใช้เป็นคำอธิบาย">
              @error('excerpt')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- เนื้อหา --}}
            <div class="md:col-span-2">
              <label class="{{ $label }}">เนื้อหา <span class="text-pink-600">*</span></label>
              <textarea name="body" rows="8"
                        class="{{ $input }} @error('body') border-red-400 ring-2 ring-red-200 @enderror"
                        placeholder="พิมพ์รายละเอียดข่าว..." required>{{ old('body') }}</textarea>
              @error('body')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- รูปหน้าปก --}}
            <div class="md:col-span-2">
              <label class="{{ $label }}">รูปหน้าปก</label>
              <input type="file" name="cover" accept="image/*"
                     class="{{ $input }} @error('cover') border-red-400 ring-2 ring-red-200 @enderror"
                     onchange="previewCover(this)">
              <p class="{{ $hint }}">รองรับ jpg, jpeg, png, webp ขนาดไม่เกิน 2MB</p>
              @error('cover')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

              <div id="coverPreviewWrap" class="mt-3 hidden">
                <img id="coverPreview" class="h-32 rounded-lg border object-cover" alt="Preview">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <!-- STICKY ACTION BAR -->
  <footer class="sticky bottom-0 bg-white/90 backdrop-blur border-t">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-3 flex items-center justify-end gap-2">
      <a href="{{ $indexUrl }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ยกเลิก</a>
      <button form="newsForm" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">บันทึก</button>
    </div>
  </footer>

  <script>
    function previewCover(input){
      const file = input.files && input.files[0];
      const wrap = document.getElementById('coverPreviewWrap');
      const img  = document.getElementById('coverPreview');
      if(!file){ wrap.classList.add('hidden'); img.src=''; return; }
      const reader = new FileReader();
      reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); };
      reader.readAsDataURL(file);
    }
  </script>

</body>
</html>
