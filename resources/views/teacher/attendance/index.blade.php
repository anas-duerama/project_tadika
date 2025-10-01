@extends('teacher.layout')
@section('title','จัดการการมาเรียน — คุณครู')
@section('page_title','จัดการการมาเรียน')

@section('content')
  <form method="GET" class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-4">
    <input type="date" name="date" value="{{ $date }}" class="rounded-xl border px-3 py-2">
    <div>
      <label class="text-sm text-gray-600">วิชา</label>
      @php $subjects = ['ไทย','มาลายู','นิด','มา']; @endphp
      <select name="class" class="rounded-xl border px-3 py-2">
        <option value="">- เลือกวิชา -</option>
        @foreach($subjects as $s)
          <option value="{{ $s }}" @selected(($primarySubject ?? '')===$s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="text-sm text-gray-600">ช่วงจาก</label>
      <input type="date" name="from" value="{{ $rangeFrom ?? '' }}" class="rounded-xl border px-3 py-2">
    </div>
    <div>
      <label class="text-sm text-gray-600">ถึง</label>
      <input type="date" name="to" value="{{ $rangeTo ?? '' }}" class="rounded-xl border px-3 py-2">
    </div>
    <div>
      <label class="text-sm text-gray-600">ช่วงสำเร็จรูป</label>
      <select name="period" class="rounded-xl border px-3 py-2">
        <option value="day"   @selected(($period ?? '')==='day')>วัน</option>
        <option value="week"  @selected(($period ?? '')==='week')>สัปดาห์</option>
        <option value="month" @selected(($period ?? '')==='month')>เดือน</option>
      </select>
    </div>
    <div>
      <label class="text-sm text-gray-600">ห้อง</label>
      @if(!empty($teachingRooms) && count($teachingRooms))
        <select name="room" class="rounded-xl border px-3 py-2">
          @foreach($teachingRooms as $r)
            <option value="{{ $r }}" @selected(($selectedRoom ?? '')===$r)>{{ $r }}</option>
          @endforeach
        </select>
      @else
        <input type="text" name="room" value="{{ $selectedRoom ?? '' }}" placeholder="ห้อง เช่น 1" class="rounded-xl border px-3 py-2">
      @endif
    </div>
    <div class="flex gap-2 items-end">
      <button class="rounded-xl bg-indigo-600 text-white px-5 py-2 font-medium hover:bg-indigo-700">โหลดรายชื่อ</button>
    </div>
  </form>

  <form method="POST" action="{{ route('teacher.attendance.store') }}">
    @csrf
  <input type="hidden" name="date" value="{{ $date }}">
  <input type="hidden" name="class" value="{{ $primarySubject ?? '' }}">
  <input type="hidden" name="room" value="{{ $selectedRoom ?? '' }}">

    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      {{-- Debug summary to help diagnose why students might be empty --}}
      <div class="p-3 border-b bg-yellow-50 text-sm text-gray-700">
        <div>Debug: students loaded = {{ $students->count() ?? 0 }}; selectedRoom = {{ $selectedRoom ?? '(null)' }}; teachingRooms = {{ !empty($teachingRooms) ? implode(', ', $teachingRooms) : '(none)' }};</div>
        <div class="text-xs text-gray-500">student.room populated? {{ $students->pluck('room')->filter()->isNotEmpty() ? 'yes' : 'no' }}</div>
      </div>
      <div class="p-4 border-b bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <div class="flex gap-4">
          <div>มา: <strong class="text-emerald-600">{{ $presentCount ?? 0 }}</strong></div>
          <div>ขาด: <strong class="text-red-600">{{ $absentCount ?? 0 }}</strong></div>
          <div>ลา: <strong class="text-yellow-600">{{ $leaveCount ?? 0 }}</strong></div>
        </div>
        <div class="text-xs text-gray-500">บันทึกล่าสุด: {{ optional($lastSavedAt)->diffForHumans() ?? '-' }}</div>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left px-4 py-3">รหัส</th>
            <th class="text-left px-4 py-3">ชื่อนักเรียน</th>
            <th class="text-left px-4 py-3">สถานะ (ช่วง)</th>
            <th class="text-left px-4 py-3">สรุป (มา/ขาด/ลา)</th>
            <th class="text-left px-4 py-3">หมายเหตุ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $s)
            @php
              $rec = $records[$s->id] ?? null;
              // safe name fallback
              $name = $s->fullname ?? (isset($s->first_name) || isset($s->last_name) ? trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? '')) : null);
              if (empty($name)) $name = $s->student_code;
            @endphp
            <tr class="border-t">
              <td class="px-4 py-3 font-mono">{{ $s->student_code }}</td>
              <td class="px-4 py-3">{{ $name }}</td>
              <td class="px-4 py-3">
                @php
                  // determine default status: prefer explicit status, else use present flag (default true)
                  if ($rec?->status ?? false) {
                    $st = $rec->status;
                  } else {
                    $st = ($rec?->present ?? true) ? 'present' : 'absent';
                  }
                @endphp
                <select name="status[{{ $s->id }}]" class="rounded-lg border px-2 py-1">
                  <option value="present" @selected($st==='present')>มาเรียน</option>
                  <option value="absent"  @selected($st==='absent')>ขาดเรียน</option>
                  <option value="leave"   @selected($st==='leave')>ลา</option>
                </select>
              </td>
              <td class="px-4 py-3">
                @php $sum = $studentSummaries[$s->id] ?? ['p'=>0,'a'=>0,'l'=>0]; @endphp
                มา: <strong class="text-emerald-600">{{ $sum['p'] }}</strong>
                / ขาด: <strong class="text-red-600">{{ $sum['a'] }}</strong>
                / ลา: <strong class="text-yellow-600">{{ $sum['l'] }}</strong>
              </td>
              <td class="px-4 py-3">
                <input type="text" name="remark[{{ $s->id }}]" value="{{ $rec?->remark }}" placeholder="เช่น ป่วย/กิจกรรม" class="w-full rounded-lg border px-3 py-1.5">
              </td>
            </tr>
          @empty
            <tr><td class="px-4 py-3 text-gray-400" colspan="5">ไม่มีรายชื่อ</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-end">
      <button class="rounded-xl bg-indigo-600 text-white px-5 py-2 font-medium hover:bg-indigo-700">บันทึกการมาเรียน</button>
    </div>
  </form>
@endsection
