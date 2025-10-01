@extends('teacher.layout')
@section('title','ไวกรอกคะแนน')
@section('page_title','ไวกรอกคะแนน (Quick Entry)')

@section('page_actions')
  @if(session('ok'))
    <div class="text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5">
      {{ session('ok') }}
    </div>
  @endif
@endsection

@section('content')
  {{-- ฟอร์มตัวกรอง ชั้น/ห้อง/ภาคเรียน/วิชา --}}
  <form method="GET" class="bg-white border rounded-2xl p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
      {{-- ชั้น --}}
      <div>
        <label class="text-xs text-gray-600">ชั้น</label>
        @if($hasClass)
          <select name="class" class="w-full rounded-xl border px-3 py-2">
            <option value="">— ทั้งหมด —</option>
            @foreach($classes as $c)
              <option value="{{ $c }}" @selected($class===(string)$c)>{{ $c }}</option>
            @endforeach
          </select>
        @else
          <input name="class" value="{{ $class }}" placeholder="เช่น ป.6" class="w-full rounded-xl border px-3 py-2">
        @endif
      </div>

      {{-- ห้อง --}}
      <div>
        <label class="text-xs text-gray-600">ห้อง</label>
        @if($hasRoom && $class !== '')
          <select name="room" class="w-full rounded-xl border px-3 py-2">
            <option value="">— ทั้งหมด —</option>
            @foreach($rooms as $r)
              <option value="{{ $r }}" @selected($room===(string)$r)>{{ $r }}</option>
            @endforeach
          </select>
        @else
          <input name="room" value="{{ $room }}" placeholder="เช่น 1" class="w-full rounded-xl border px-3 py-2">
        @endif
      </div>

      {{-- ภาคเรียน --}}
      <div>
        <label class="text-xs text-gray-600">ภาคเรียน <span class="text-red-500">*</span></label>
        <input name="term" value="{{ $term }}" placeholder="เช่น 1/2568" class="w-full rounded-xl border px-3 py-2" required>
      </div>

      {{-- วิชา --}}
      <div>
        <label class="text-xs text-gray-600">วิชา <span class="text-red-500">*</span></label>
        <input name="subject" value="{{ $subject }}" placeholder="เช่น คณิตศาสตร์" class="w-full rounded-xl border px-3 py-2" required>
      </div>

      <div class="flex items-end">
        <button class="w-full rounded-xl bg-indigo-600 text-white px-5 py-2 font-medium hover:bg-indigo-700">
          แสดงรายชื่อนักเรียน
        </button>
      </div>
    </div>
    @error('term')<div class="text-red-600 text-xs mt-2">{{ $message }}</div>@enderror
    @error('subject')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
  </form>

  {{-- ตารางกรอกคะแนน --}}
  @if($term !== '' && $subject !== '')

    @if($students->isEmpty())
      <div class="bg-white rounded-2xl border p-6 text-gray-500">
        ไม่พบนักเรียนตามเงื่อนไข
      </div>
    @else
      <form method="POST" action="{{ route('teacher.grades.quick.store') }}"
            class="bg-white rounded-2xl border p-4">
        @csrf
        <input type="hidden" name="class" value="{{ $class }}">
        <input type="hidden" name="room" value="{{ $room }}">
        <input type="hidden" name="term" value="{{ $term }}">
        <input type="hidden" name="subject" value="{{ $subject }}">

        <div class="mb-3 flex items-center justify-between">
          <div class="text-sm text-gray-600">
            ภาคเรียน: <b>{{ $term }}</b> — วิชา: <b>{{ $subject }}</b>
            @if($class || $room)
              — ชั้น/ห้อง: <b>{{ $class ?: '-' }}/{{ $room ?: '-' }}</b>
            @endif
          </div>
          <div class="text-xs text-gray-500">
            เคล็ดลับ: กด <kbd class="px-1 py-0.5 border rounded">Tab</kbd> เพื่อเลื่อนไปช่องถัดไปได้เร็ว
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="border-b text-gray-500">
                <th class="py-2 pr-4 text-left">รหัส</th>
                <th class="py-2 pr-4 text-left">ชื่อ-สกุล</th>
                <th class="py-2 pr-4 text-left">คะแนน</th>
              </tr>
            </thead>
            <tbody>
            @foreach($students as $st)
              @php
                $oldRow = $existingByStudent->get($st->id);
                $scoreVal = old("scores.{$st->id}", optional($oldRow)->score);
              @endphp
              <tr class="border-b last:border-0">
                <td class="py-2 pr-4 font-mono">{{ $st->student_code }}</td>
                <td class="py-2 pr-4">{{ $st->_name }}</td>
                <td class="py-2 pr-4">
                  <input
                      name="scores[{{ $st->id }}]"
                      value="{{ $scoreVal }}"
                      placeholder="0-100 หรือ A/B/C"
                      class="rounded-lg border px-3 py-1.5 w-40"
                      inputmode="decimal">
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-4 flex gap-2">
          <button class="rounded-xl bg-emerald-600 text-white px-5 py-2 hover:bg-emerald-700">
            บันทึกคะแนนทั้งหมด
          </button>
          <a href="{{ route('teacher.grades.index') }}"
             class="rounded-xl border px-5 py-2 hover:bg-gray-100">
            กลับหน้าคะแนน
          </a>
        </div>
      </form>
    @endif

  @else
    <div class="text-sm text-gray-500">
      โปรดระบุ <b>ภาคเรียน</b> และ <b>วิชา</b> แล้วกด “แสดงรายชื่อนักเรียน”
    </div>
  @endif
@endsection
