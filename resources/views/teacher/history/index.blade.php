@extends('teacher.layout')
@section('title','ประวัติการมาเรียน — คุณครู')
@section('page_title','ประวัติการมาเรียน')

@section('content')
    <form method="GET" class="flex gap-2 mb-4">
        <input type="date" name="from" value="{{ $from }}" class="rounded-xl border px-3 py-2">
        <input type="date" name="to" value="{{ $to }}" class="rounded-xl border px-3 py-2">
        @if(!empty($teachingRooms))
            <select name="room" class="rounded-xl border px-3 py-2">
                <option value="">-- ห้อง --</option>
                @foreach($teachingRooms as $r)
                    <option value="{{ $r }}" @selected(($selectedRoom ?? '')===$r)>{{ $r }}</option>
                @endforeach
            </select>
        @else
            <input type="text" name="room" value="{{ $selectedRoom ?? '' }}" placeholder="ห้อง" class="rounded-xl border px-3 py-2">
        @endif
        <button class="rounded-xl bg-indigo-600 text-white px-4 py-2">โหลด</button>
    </form>

    {{-- Debug: show counts so we can confirm controller returned records --}}
    <div class="text-sm text-gray-600 mb-3">
        วันที่กลุ่ม: {{ $records->count() }}; รวมรายการ: {{ $records->flatten()->count() }}
    </div>

    {{-- Show which rooms the controller is using and the active room filter --}}
    <div class="text-sm text-gray-500 mb-3">
        ห้องที่สอน: @if(!empty($teachingRooms)) {{ implode(', ', $teachingRooms) }} @else (ไม่ได้ระบุ) @endif
        — ห้องที่กรอง: {{ $selectedRoom ?? '(ทุกห้อง)' }}
        &nbsp;|&nbsp;
        {{-- <a href="/_debug/attendance-records" target="_blank" class="text-indigo-600">ตรวจ DB (debug JSON)</a> --}}
    </div>

    @if($records->isEmpty())
        <div class="text-gray-500">ไม่มีข้อมูลในช่วงที่เลือก</div>
    @else
        @foreach($records as $date => $group)
            <div class="bg-white rounded-2xl border shadow-sm mb-4">
                <div class="p-3 border-b font-medium">วันที่: {{ $date }} ({{ $group->count() }} รายการ)</div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-4 py-3">รหัส</th>
                            <th class="text-left px-4 py-3">ชื่อนักเรียน</th>
                            <th class="text-left px-4 py-3">สถานะ</th>
                            <th class="text-left px-4 py-3">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $r)
                            <tr class="border-t">
                                <td class="px-4 py-3 font-mono">{{ $r->student->student_code ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $r->student->fullname ?? '-' }}</td>
                                    @php
                                        $raw = $r->status ?? ($r->present ? 'present' : 'absent');
                                        $statusLabel = match($raw) {
                                            'present' => 'มาเรียน',
                                            'absent' => 'ขาดเรียน',
                                            'leave' => 'ลา',
                                            default => $raw,
                                        };
                                    @endphp
                                    <td class="px-4 py-3">{{ $statusLabel }}</td>
                                <td class="px-4 py-3">{{ $r->remark ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

@endsection