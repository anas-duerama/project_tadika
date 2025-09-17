@extends('teacher.layout')
@section('title','จัดการข้อมูลนักเรียน — คุณครู')
@section('page_title','จัดการข้อมูลนักเรียน')

@php
  use Illuminate\Support\Facades\Route as R;

  // เตรียมลิงก์ที่อาจยังไม่สร้าง route ไว้ล่วงหน้า (กัน error)
  $routeIndex   = R::has('teacher.students.index')     ? route('teacher.students.index')     : url()->current();
  $routeExport  = R::has('teacher.students.export')    ? route('teacher.students.export')    : null;    // ตัวเลือก
  $routeBulk    = R::has('teacher.students.bulk')      ? route('teacher.students.bulk')      : null;    // ตัวเลือก
  $routeCreate  = R::has('teacher.students.create')    ? route('teacher.students.create')    : null;    // ตัวเลือก
  $routeShow    = R::has('teacher.students.show')      ? 'teacher.students.show'             : null;    // ตัวเลือก
  $routeEdit    = R::has('teacher.students.edit')      ? 'teacher.students.edit'             : null;    // ตัวเลือก
  $routeAttend  = R::has('teacher.attendance.index')   ? 'teacher.attendance.index'          : null;

  // ค่าจาก query
  $q       = request('q');
  $class   = request('class');
  $room    = request('room');
  $per     = (int) (request('per_page', 20));
  $sortBy  = request('sort_by', 'student_code');
  $order   = request('order', 'asc');

  // helper สำหรับทำลิงก์เรียงลำดับ
  function sort_link($col, $label) {
      $isActive = request('sort_by', 'student_code') === $col;
      $nextOrder = $isActive && request('order', 'asc') === 'asc' ? 'desc' : 'asc';
      $params = array_merge(request()->query(), ['sort_by'=>$col, 'order'=>$nextOrder]);
      $url = request()->url().'?'.http_build_query($params);
      $arrow = $isActive ? (request('order','asc')==='asc' ? '↑' : '↓') : '↕';
      return '<a href="'.$url.'" class="inline-flex items-center gap-1 hover:underline">'.$label.' <span class="text-gray-400 text-xs">'.$arrow.'</span></a>';
  }
@endphp

@push('scripts')
<script>
  // เลือกทั้งหมด / ยกเลิกทั้งหมด
  document.addEventListener('DOMContentLoaded', function() {
    const master = document.getElementById('checkAll');
    if (!master) return;
    master.addEventListener('change', () => {
      document.querySelectorAll('input[name="ids[]"]').forEach(ch => ch.checked = master.checked);
    });
  });
</script>
@endpush

@section('content')

  {{-- แถบสรุปสั้น ๆ (ถ้ามีตัวเลขจาก controller ใส่มาใน $summary) --}}
  @if(!empty($summary))
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
      <div class="text-sm text-gray-500">นักเรียนทั้งหมด (ตรงตามเงื่อนไข)</div>
      <div class="mt-1 text-3xl font-extrabold">{{ $summary['total'] ?? '-' }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
      <div class="text-sm text-gray-500">ชั้นเรียน</div>
      <div class="mt-1 text-3xl font-extrabold">{{ $summary['class_count'] ?? '-' }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
      <div class="text-sm text-gray-500">ห้อง</div>
      <div class="mt-1 text-3xl font-extrabold">{{ $summary['room_count'] ?? '-' }}</div>
    </div>
  </div>
  @endif

  {{-- แถบเครื่องมือด้านบน --}}
  <div class="bg-white rounded-2xl p-4 border shadow-sm mb-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div class="font-semibold">ค้นหาและกรองข้อมูล</div>
      <div class="flex items-center gap-2">
        @if($routeCreate)
          <a href="{{ $routeCreate }}" class="rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">+ เพิ่มนักเรียน</a>
        @endif
        @if($routeExport)
          <a href="{{ $routeExport.'?'.http_build_query(request()->query()) }}" class="rounded-xl border px-4 py-2 text-sm hover:bg-gray-100">Export CSV</a>
        @endif
      </div>
    </div>

    <form method="GET" action="{{ $routeIndex }}" class="mt-3 grid grid-cols-1 md:grid-cols-6 gap-3">
      <input type="text" name="q" value="{{ $q }}" placeholder="ค้นหา รหัส/ชื่อ" class="rounded-xl border px-4 py-2 md:col-span-2">
      <input type="text" name="class" value="{{ $class }}" placeholder="ชั้น เช่น ป.6" class="rounded-xl border px-4 py-2">
      <input type="text" name="room" value="{{ $room }}" placeholder="ห้อง เช่น 1" class="rounded-xl border px-4 py-2">

      <select name="per_page" class="rounded-xl border px-3 py-2">
        @foreach([10,20,50,100] as $n)
          <option value="{{ $n }}" {{ $per==$n?'selected':'' }}>{{ $n }}/หน้า</option>
        @endforeach
      </select>

      <div class="flex items-center gap-2">
        <select name="sort_by" class="rounded-xl border px-3 py-2">
          <option value="student_code" {{ $sortBy==='student_code'?'selected':'' }}>เรียงโดย รหัส</option>
          <option value="fullname"     {{ $sortBy==='fullname'?'selected':'' }}>เรียงโดย ชื่อ</option>
          <option value="class_level"  {{ $sortBy==='class_level'?'selected':'' }}>เรียงโดย ชั้น</option>
          <option value="room"         {{ $sortBy==='room'?'selected':'' }}>เรียงโดย ห้อง</option>
        </select>
        <select name="order" class="rounded-xl border px-3 py-2">
          <option value="asc"  {{ $order==='asc'?'selected':'' }}>น้อย→มาก</option>
          <option value="desc" {{ $order==='desc'?'selected':'' }}>มาก→น้อย</option>
        </select>
      </div>

      <div class="flex gap-2 md:justify-end">
        <button class="rounded-xl bg-indigo-600 text-white px-5 py-2 font-medium hover:bg-indigo-700">ค้นหา</button>
        <a href="{{ $routeIndex }}" class="rounded-xl border px-5 py-2 text-sm hover:bg-gray-100">ล้างเงื่อนไข</a>
      </div>
    </form>
  </div>

  {{-- Bulk actions (เผื่อไว้ ถ้าคุณทำ route ไว้แล้วให้ใส่ $routeBulk) --}}
  <form method="POST" action="{{ $routeBulk ?? '' }}" @if(!$routeBulk) onsubmit="return false" @endif>
    @csrf
    @if(!$routeBulk)
      <div class="mb-2 text-xs text-gray-400">* ยังไม่พบ route สำหรับ bulk action (teacher.students.bulk) — ปุ่มจะถูกปิดไว้</div>
    @endif

    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <div class="px-4 py-3 flex items-center gap-3 border-b bg-gray-50">
        <label class="inline-flex items-center gap-2">
          <input id="checkAll" type="checkbox" class="rounded border-gray-300">
          <span class="text-sm">เลือกทั้งหมด</span>
        </label>

        <div class="ml-auto flex items-center gap-2">
          <select name="action" class="rounded-xl border px-3 py-1.5 text-sm" @if(!$routeBulk) disabled @endif>
            <option value="">— เลือกการทำงาน —</option>
            <option value="export">Export ที่เลือก</option>
            <option value="delete">ลบที่เลือก</option>
          </select>
          <button class="rounded-xl border px-3 py-1.5 text-sm hover:bg-gray-100" @if(!$routeBulk) disabled @endif>ทำงาน</button>
        </div>
      </div>

      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-3 py-3 w-10">
              {{-- checkbox master อยู่บน tool bar แล้ว --}}
            </th>
            <th class="text-left px-3 py-3 w-16">#</th>
            <th class="text-left px-3 py-3">{!! sort_link('student_code','รหัส') !!}</th>
            <th class="text-left px-3 py-3">{!! sort_link('fullname','ชื่อนักเรียน') !!}</th>
            <th class="text-left px-3 py-3">{!! sort_link('class_level','ชั้น') !!} / {!! sort_link('room','ห้อง') !!}</th>
            <th class="text-left px-3 py-3 w-56">การทำงาน</th>
          </tr>
        </thead>
        <tbody>
          @php
            $isPaginator = is_object($rows ?? null) && method_exists($rows,'firstItem');
            $startNo = $isPaginator ? (int)$rows->firstItem() : 1;
          @endphp

          @forelse($rows as $i => $r)
            @php
              $rowNo = $startNo + $i;
              $attendUrl = $routeAttend
                ? route($routeAttend, ['class'=>$r->class_level, 'room'=>$r->room, 'date'=>now()->toDateString()])
                : 'javascript:void(0)';
            @endphp
            <tr class="border-t">
              <td class="px-3 py-3 align-top">
                <input type="checkbox" name="ids[]" value="{{ $r->id }}" class="rounded border-gray-300">
              </td>
              <td class="px-3 py-3 align-top text-gray-500">{{ $rowNo }}</td>
              <td class="px-3 py-3 align-top font-mono">{{ $r->student_code }}</td>
              <td class="px-3 py-3 align-top">{{ $r->fullname }}</td>
              <td class="px-3 py-3 align-top">
                <span class="inline-flex items-center gap-2">
                  <span class="rounded-full bg-indigo-50 text-indigo-700 px-2 py-0.5 text-xs">{{ $r->class_level }}</span>
                  <span class="rounded-full bg-gray-100 text-gray-700 px-2 py-0.5 text-xs">ห้อง {{ $r->room }}</span>
                </span>
              </td>
              <td class="px-3 py-3 align-top">
                <div class="flex flex-wrap gap-2">
                  @if($routeShow)
                    <a href="{{ route($routeShow, $r->id) }}" class="rounded-xl border px-3 py-1.5 text-xs hover:bg-gray-100">ดูข้อมูล</a>
                  @endif
                  @if($routeEdit)
                    <a href="{{ route($routeEdit, $r->id) }}" class="rounded-xl border px-3 py-1.5 text-xs hover:bg-gray-100">แก้ไข</a>
                  @endif
                  @if($routeAttend)
                    <a href="{{ $attendUrl }}" class="rounded-xl border px-3 py-1.5 text-xs hover:bg-gray-100">เช็คชื่อวันนี้</a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                ไม่พบข้อมูลตรงตามเงื่อนไข
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </form>

  {{-- Pagination --}}
  <div class="mt-4">
    @if(is_object($rows ?? null) && method_exists($rows,'links'))
      {{ $rows->appends(request()->query())->links() }}
    @endif
  </div>
@endsection
