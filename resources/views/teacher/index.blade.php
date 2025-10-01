@extends('teacher.layout')
@section('title','โปรไฟล์ผู้สอน')
@section('page_title','โปรไฟล์ผู้สอน')

@section('content')
<div class="max-w-4xl mx-auto py-8">
  <h1 class="text-2xl font-bold mb-4">ข้อมูลผู้สอน</h1>

  @php $teacher = auth()->user()?->teacher ?? null; @endphp

  @if(!$teacher)
    <div class="rounded-lg bg-yellow-50 border p-4">ไม่มีข้อมูลโปรไฟล์ผู้สอนของคุณ</div>
    @else
    <div class="bg-white border rounded-lg p-6">
      <div class="flex items-start gap-6">
        <div class="flex-1">
          <h2 class="text-xl font-semibold">{{ $teacher->full_name }}</h2>
          <p class="text-sm text-gray-600">อีเมล: {{ $teacher->email ?? '-' }} | โทร: {{ $teacher->phone ?? '-' }}</p>

          <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
              <div>
                <div class="text-xs text-gray-500">วิชาหลัก</div>
                <div class="font-medium">{{ $teacher->primary_subject ?? '-' }}</div>
              </div>

              {{-- <div>
                <div class="text-xs text-gray-500">แผนก / กลุ่มสาระ</div>
                <div class="font-medium">{{ $teacher->department ?? '-' }}</div>
              </div> --}}

              <div>
                <div class="text-xs text-gray-500">ห้องประจำ</div>
                <div class="font-medium">{{ $teacher->homeroom ?? '-' }}</div>
              </div>

              <div>
                <div class="text-xs text-gray-500">ห้องที่สอน</div>
                <div class="font-medium">
                  @php
                    $rooms = [];
                    if (!empty($teacher->teaching_rooms)) {
                      if (is_array($teacher->teaching_rooms)) {
                        $rooms = $teacher->teaching_rooms;
                      } else {
                        $raw = (string)$teacher->teaching_rooms;
                        $decoded = json_decode($raw, true);
                        if (is_array($decoded)) {
                          $rooms = $decoded;
                        } else {
                          // comma separated fallback
                          $rooms = preg_split('/\s*,\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
                        }
                      }
                      // normalize items
                      $rooms = array_values(array_unique(array_map('trim', array_filter($rooms, fn($v) => $v !== null && $v !== ''))));
                    }
                  @endphp
                  {{ !empty($rooms) ? implode(', ', $rooms) : '-' }}
                </div>
              </div>
            </div>

            <div class="space-y-3">
              <div>
                <div class="text-xs text-gray-500">วันที่เริ่มงาน</div>
                <div class="font-medium">{{ optional($teacher->hire_date)->format('Y-m-d') ?? '-' }}</div>
              </div>

              <div>
                <div class="text-xs text-gray-500">สถานะ</div>
                <div class="font-medium">{{ ($teacher->status ?? '') === 'active' ? 'ปฏิบัติงาน' : 'ไม่ปฏิบัติงาน' }}</div>
              </div>

              <div>
                <div class="text-xs text-gray-500">หมายเหตุ / ประวัติ</div>
                <div class="font-medium">{{ $teacher->bio ? nl2br(e($teacher->bio)) : '-' }}</div>
              </div>
            </div>
          </div>

          <div class="mt-6">
            <a href="{{ route('teacher.dashboard') }}" class="px-4 py-2 bg-gray-100 rounded">กลับ</a>
          </div>
        </div>

        <div class="w-36 flex-shrink-0">
          @if(!empty($teacher->photo_path))
            <img src="{{ asset('storage/'.$teacher->photo_path) }}" alt="รูปโปรไฟล์" class="w-32 h-32 rounded-lg object-cover border">
          @else
            <div class="w-32 h-32 rounded-lg bg-gray-100 border flex items-center justify-center text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
          @endif
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
