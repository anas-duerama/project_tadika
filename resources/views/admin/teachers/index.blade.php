{{-- resources/views/admin/teachers/index.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;
  $adminHome = Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard');
  $label='block text-sm font-medium text-gray-700';
  $input='mt-1 w-full bg-white border border-gray-300 rounded-xl px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-600 placeholder-gray-400';
  $select=$input;
@endphp
<!DOCTYPE html><html lang="th"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ผู้สอน</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-800">
<header class="bg-sky-300 border-b border-sky-400 shadow-sm">
  <div class="max-w-7xl mx-auto h-16 px-4 md:px-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ $adminHome }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-sky-400 hover:bg-sky-500 text-sky-950">← กลับหน้า Admin</a>
      <h1 class="text-lg md:text-xl font-extrabold text-sky-950">ผู้สอน</h1>
    </div>
    <a href="{{ route('admin.teachers.create') }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm">+ เพิ่มผู้สอน</a>
  </div>
</header>

<main class="px-4 md:px-6 py-6 max-w-7xl mx-auto">
  @if(session('success'))
    <div class="mb-4 rounded-xl bg-emerald-50 text-emerald-700 px-4 py-3 text-sm border border-emerald-100">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-2xl border p-5">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div><label class="{{ $label }}">ค้นหา</label><input name="q" value="{{ $q ?? '' }}" class="{{ $input }}" placeholder="ชื่อ/นามสกุล/วิชา"></div>
      <div><label class="{{ $label }}">ห้องประจำ</label>
        <select name="room" class="{{ $select }}"><option value="">ทั้งหมด</option>
          @foreach($rooms as $r)<option value="{{ $r }}" @selected(($room ?? '')===$r)>{{ $r }}</option>@endforeach
        </select>
      </div>
      <div class="flex items-end"><button class="px-4 py-2 rounded-lg bg-gray-900 text-white">ค้นหา</button></div>
    </form>

    <div class="mt-5 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead><tr class="border-b text-gray-500">
          <th class="py-2 pr-4 text-left">ผู้สอน</th>
          <th class="py-2 pr-4 text-left">วิชาหลัก</th>
          <th class="py-2 pr-4 text-left">ห้องประจำ</th>
          <th class="py-2 pr-4 text-left">สถานะ</th>
          <th class="py-2 pr-0 text-right">จัดการ</th>
        </tr></thead>
        <tbody>
        @forelse($teachers as $t)
          <tr class="border-b last:border-0">
            <td class="py-2 pr-4">
              <div class="flex items-center gap-3">
                @if($t->photo_path)
                  <img src="{{ asset('storage/'.$t->photo_path) }}" class="w-10 h-10 rounded-full object-cover border">
                @else
                  <div class="w-10 h-10 rounded-full bg-gray-200 grid place-items-center text-gray-600">👤</div>
                @endif
                <div>
                  <div class="font-medium">{{ $t->full_name }}</div>
                  <div class="text-gray-500">{{ $t->email ?? '-' }} · {{ $t->phone ?? '-' }}</div>
                </div>
              </div>
            </td>
            <td class="py-2 pr-4">{{ $t->primary_subject ?? '-' }}</td>
            <td class="py-2 pr-4">{{ $t->homeroom ?? '-' }}</td>
            <td class="py-2 pr-4">
              <span class="px-2 py-1 rounded text-xs {{ $t->status==='active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                {{ $t->status==='active' ? 'ปฏิบัติงาน' : 'ไม่ปฏิบัติงาน' }}
              </span>
            </td>
            <td class="py-2 pr-0 text-right">
              <a href="{{ route('admin.teachers.edit',$t) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 mr-2">แก้ไข</a>
              <form action="{{ route('admin.teachers.destroy',$t) }}" method="POST" class="inline" onsubmit="return confirm('ยืนยันลบผู้สอนคนนี้?')">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">ลบ</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="py-4 text-gray-500">ยังไม่มีข้อมูลผู้สอน</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $teachers->links() }}</div>
  </div>
</main>
</body></html>
