@extends('admin.layout')
@section('title','แก้ไขข่าวสาร')
@section('page_title','แก้ไขข่าวสาร')

@section('content')
@php
  use Illuminate\Support\Facades\Route;

  // ป้องกัน error กรณียังไม่ตั้งชื่อ route
  $updateUrl = Route::has('admin.news.update') ? route('admin.news.update', $news) : url("/admin/news/{$news->id}");
  $indexUrl  = Route::has('admin.news.index')  ? route('admin.news.index')        : url('/admin/news');

  // หมวดหมู่ fallback ถ้าไม่ได้ส่ง $categories มาจาก Controller
  $categories = ($categories ?? ['ประกาศ','กิจกรรม','รับสมัคร','ข่าวทั่วไป','ประชาสัมพันธ์']);
@endphp

<div class="max-w-6xl mx-auto bg-white rounded-2xl border border-gray-200 p-6">

  @if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">
      โปรดตรวจสอบข้อมูลที่กรอกให้ครบถ้วน
    </div>
  @endif

  <form id="newsForm" action="{{ $updateUrl }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- หัวข้อ --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">หัวข้อ <span class="text-pink-600">*</span></label>
        <input type="text" name="title" value="{{ old('title', $news->title) }}"
               class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
        @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- หมวดหมู่ --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">หมวดหมู่ <span class="text-pink-600">*</span></label>
        <select name="category"
                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
          @foreach($categories as $c)
            <option value="{{ $c }}" {{ old('category', $news->category)===$c ? 'selected':'' }}>{{ $c }}</option>
          @endforeach
        </select>
        @error('category') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- คำเกริ่น --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">คำเกริ่น (ไม่บังคับ)</label>
        <input type="text" name="excerpt" value="{{ old('excerpt', $news->excerpt) }}"
               class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="สรุปสั้น ๆ">
        @error('excerpt') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- เนื้อหา --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">เนื้อหา <span class="text-pink-600">*</span></label>
        <textarea name="body" rows="8"
                  class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('body', $news->body) }}</textarea>
        @error('body') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- รูปหน้าปก --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">รูปหน้าปก</label>
        @if($news->cover_path)
          <img src="{{ asset('storage/'.$news->cover_path) }}" alt="cover" class="mt-1 h-28 rounded border object-cover">
        @endif
        <input type="file" name="cover" accept="image/*"
               class="mt-2 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        <p class="text-xs text-gray-500 mt-1">รองรับ jpg, jpeg, png, webp ขนาดไม่เกิน 2MB</p>
        @error('cover') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- สถานะ --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">สถานะ <span class="text-pink-600">*</span></label>
        <select name="status"
                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
          <option value="draft"     {{ old('status', $news->status)==='draft' ? 'selected':'' }}>ฉบับร่าง</option>
          <option value="published" {{ old('status', $news->status)==='published' ? 'selected':'' }}>เผยแพร่</option>
        </select>
        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- วันที่เผยแพร่ --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">วันที่เผยแพร่</label>
        <input type="datetime-local" name="published_at"
               value="{{ old('published_at', optional($news->published_at)->format('Y-m-d\TH:i')) }}"
               class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        @error('published_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- ปุ่มกดด้านล่าง --}}
    <div class="mt-6 flex justify-end gap-2">
      <a href="{{ $indexUrl }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ยกเลิก</a>
      <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">บันทึก</button>
    </div>
  </form>

</div>

@endsection
