@extends('teacher.layout')
@section('title','จัดการการมาเรียน — คุณครู')
@section('page_title','จัดการการมาเรียน')

@section('content')
  <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <input type="date" name="date" value="{{ $date }}" class="rounded-xl border px-3 py-2">
    <input type="text" name="class" value="{{ $class }}" placeholder="ชั้น เช่น ป.6" class="rounded-xl border px-3 py-2">
    <input type="text" name="room" value="{{ $room }}" placeholder="ห้อง เช่น 1" class="rounded-xl border px-3 py-2">
    <button class="rounded-xl bg-indigo-600 text-white px-5 py-2 font-medium hover:bg-indigo-700 md:col-span-4">โหลดรายชื่อ</button>
  </form>

  <form method="POST" action="{{ route('teacher.attendance.store') }}">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="class" value="{{ $class }}">
    <input type="hidden" name="room" value="{{ $room }}">

    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left px-4 py-3">รหัส</th>
            <th class="text-left px-4 py-3">ชื่อนักเรียน</th>
            <th class="text-left px-4 py-3">มาเรียน</th>
            <th class="text-left px-4 py-3">หมายเหตุ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $s)
            @php $rec = $records[$s->id] ?? null; @endphp
            <tr class="border-t">
              <td class="px-4 py-3 font-mono">{{ $s->student_code }}</td>
              <td class="px-4 py-3">{{ $s->fullname }}</td>
              <td class="px-4 py-3">
                <input type="checkbox" name="present[{{ $s->id }}]" {{ ($rec?->present ?? true) ? 'checked' : '' }}>
              </td>
              <td class="px-4 py-3">
                <input type="text" name="remark[{{ $s->id }}]" value="{{ $rec?->remark }}" placeholder="เช่น ป่วย/กิจกรรม" class="w-full rounded-lg border px-3 py-1.5">
              </td>
            </tr>
          @empty
            <tr><td class="px-4 py-3 text-gray-400" colspan="4">ไม่มีรายชื่อ</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-end">
      <button class="rounded-xl bg-indigo-600 text-white px-5 py-2 font-medium hover:bg-indigo-700">บันทึกการมาเรียน</button>
    </div>
  </form>
@endsection
