@php
  use Illuminate\Support\Facades\Route;
  $adminHome = Route::has('admin') ? route('admin') : url('/admin/dashboard');
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>รายชื่อนักเรียน (แยกตามห้อง)</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">

  <a href="{{ $adminHome }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm mb-4">
    ← กลับหน้า Admin
  </a>

  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      @foreach($classLevels as $room)
        <div class="bg-white rounded-xl border p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-lg">{{ $room }}</h2>
            <a href="{{ route('admin.students.create') }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm">+ เพิ่มนักเรียน</a>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="border-b text-gray-500">
                  <th class="py-2 pr-4 text-left">เลขที่</th>
                  <th class="py-2 pr-4 text-left">ชื่อ-สกุล</th>
                  <th class="py-2 pr-4 text-left">รหัสนักเรียน</th>
                  <th class="py-2 pr-0 text-right">จัดการ</th>
                </tr>
              </thead>
              <tbody>
              @forelse(($studentsByRoom[$room] ?? collect()) as $st)
                <tr class="border-b last:border-0">
                  <td class="py-2 pr-4">{{ $st->no }}</td>
                  <td class="py-2 pr-4">{{ $st->first_name }} {{ $st->last_name }}</td>
                  <td class="py-2 pr-4">{{ $st->student_code }}</td>
                  <td class="py-2 pr-0 text-right">
                    <a href="{{ route('admin.students.edit', $st) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 mr-2">แก้ไข</a>
                    <form action="{{ route('admin.students.destroy', $st) }}" method="POST" class="inline"
                          onsubmit="return confirm('ยืนยันลบนักเรียนคนนี้?')">
                      @csrf @method('DELETE')
                      <button class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">ลบ</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="py-3 text-gray-500">ยังไม่มีนักเรียนในห้องนี้</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</body>
</html>
