@extends('admin.layout')
@section('title','ผู้สอน')

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl md:text-3xl font-extrabold">ผู้สอน</h1>
    <a href="{{ route('admin.teachers.create') }}"
       class="rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">
       + กำหนดรายวิชา
    </a>
  </div>

  <form method="GET" class="mb-4">
    <div class="flex gap-2">
      <input type="text" name="q" value="{{ $q }}" placeholder="ค้นหา ชื่อ/อีเมล"
             class="rounded-xl border px-4 py-2 w-full md:w-80">
      <button class="rounded-xl border px-4 py-2 hover:bg-gray-100">ค้นหา</button>
      <a href="{{ route('admin.teachers.index') }}" class="rounded-xl border px-4 py-2 hover:bg-gray-100">ล้าง</a>
    </div>
  </form>

  <div class="bg-white rounded-2xl border shadow-sm overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="text-left px-4 py-3">ชื่อ</th>
          <th class="text-left px-4 py-3">อีเมล</th>
          <th class="text-left px-4 py-3">วิชาหลัก</th>
          <th class="text-left px-4 py-3">ห้องที่สอน</th>
          <th class="text-left px-4 py-3">สถานะโปรไฟล์</th>
          <th class="text-right px-4 py-3 w-64">การทำงาน</th>
        </tr>
      </thead>
      <tbody>
      @forelse($rows as $u)
        @php $t = $u->teacher; @endphp
        <tr class="border-t">
          <td class="px-4 py-3">{{ $u->name ?? '-' }}</td>
          <td class="px-4 py-3 font-mono">{{ $u->email }}</td>
          <td class="px-4 py-3">{{ optional($t)->primary_subject ?? '-' }}</td>
          <td class="px-4 py-3">
            @php
              $rawRooms = optional($t)->teaching_rooms ?? null;
              $rooms = [];
              if (!empty($rawRooms)) {
                if (is_array($rawRooms)) {
                  $rooms = $rawRooms;
                } else {
                  // try json decode
                  $decoded = json_decode((string)$rawRooms, true);
                  if (is_array($decoded)) {
                    $rooms = $decoded;
                  } else {
                    // comma-separated fallback
                    $rooms = preg_split('/\s*,\s*/u', (string)$rawRooms, -1, PREG_SPLIT_NO_EMPTY);
                  }
                }
              }

              // normalize each room to a short label (prefer digits or stripped prefix)
              $norm = array_values(array_filter(array_map(function($r) {
                $r = trim((string)$r);
                if ($r === '') return null;
                // digits-only preferred
                $digits = preg_replace('/\D+/', '', $r);
                if ($digits !== '') return $digits;
                // strip common prefixes like 'ห้อง', 'room'
                $noPrefix = preg_replace('/^(ห้อง|room|r)\s*/iu', '', $r);
                return $noPrefix !== '' ? $noPrefix : $r;
              }, $rooms), fn($v) => $v !== null));

              $norm = array_values(array_unique($norm));
            @endphp
            {{ !empty($norm) ? implode(', ', $norm) : '-' }}
          </td>
          <td class="px-4 py-3">
            @if($t)
              <span class="rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 text-xs">มีข้อมูลการสอน</span>
            @else
              <span class="rounded-full bg-gray-100 text-gray-700 px-2 py-0.5 text-xs">ยังไม่มี</span>
            @endif
          </td>
          <td class="px-4 py-3 text-right">
            <div class="inline-flex flex-wrap gap-2">
              @if(!$t)
                {{-- ยังไม่มีโปรไฟล์ → ปุ่มเพิ่มข้อมูลการสอน พร้อมส่ง user id ไป --}}
                <a href="{{ route('admin.teachers.create',['user'=>$u->id]) }}"
                   class="rounded-xl bg-indigo-600 text-white px-3 py-1.5 text-xs hover:bg-indigo-700">
                   + กำหนดรายวิชา
                </a>
              @else
                {{-- มีโปรไฟล์แล้ว → ดู/แก้ไข/ลบ --}}
                <a href="{{ route('admin.teachers.show', $t) }}"
                   class="rounded-xl border px-3 py-1.5 text-xs hover:bg-gray-100">ดูรายละเอียด</a>
                <a href="{{ route('admin.teachers.edit', $t) }}"
                   class="rounded-xl border px-3 py-1.5 text-xs hover:bg-gray-100">แก้ไข</a>
                <form action="{{ route('admin.teachers.destroy', $t) }}" method="POST"
                      onsubmit="return confirm('ยืนยันลบข้อมูลการสอนของ {{ $u->name }} ?')">
                  @csrf @method('DELETE')
                  <button class="rounded-xl bg-red-100 text-red-700 px-3 py-1.5 text-xs hover:bg-red-200">ลบ</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="px-4 py-6 text-gray-500">ยังไม่มีผู้สอน</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
