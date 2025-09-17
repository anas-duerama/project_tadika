@extends('teacher.layout')
@section('title','แดชบอร์ดคุณครู')
@section('page_title','แดชบอร์ด — คุณครู')

@section('content')
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
      <div class="text-sm text-gray-500">ห้องที่รับผิดชอบ</div>
      <div class="mt-1 text-3xl font-extrabold">{{ $stats['classes'] ?? 0 }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
      <div class="text-sm text-gray-500">นักเรียนทั้งหมด</div>
      <div class="mt-1 text-3xl font-extrabold">{{ $stats['students'] ?? 0 }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
      <div class="text-sm text-gray-500">เช็คชื่อวันนี้แล้ว</div>
      <div class="mt-1 text-3xl font-extrabold">{{ $stats['attendanceToday'] ?? 0 }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
      <div class="text-sm text-gray-500">งานที่ต้องตรวจ</div>
      <div class="mt-1 text-3xl font-extrabold">{{ $stats['pendingGrades'] ?? 0 }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border lg:col-span-1">
      <div class="text-base font-semibold mb-3">เริ่มต้นเร็ว</div>
      <div class="space-y-3">
        <a href="{{ route('teacher.students.index') }}" class="block rounded-xl border px-4 py-3 hover:bg-gray-50">🔎 รายชื่อนักเรียน</a>
        <a href="{{ route('teacher.grades.index') }}" class="block rounded-xl border px-4 py-3 hover:bg-gray-50">📝 บันทึกเกรด</a>
        <a href="{{ route('teacher.attendance.index') }}" class="block rounded-xl border px-4 py-3 hover:bg-gray-50">✅ เช็คชื่อมาเรียน</a>
      </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border lg:col-span-2">
      <div class="text-base font-semibold mb-3">สรุปชั้นที่สอน</div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="text-left px-4 py-3">ชั้น/ห้อง</th>
              <th class="text-left px-4 py-3">นักเรียน</th>
              <th class="text-left px-4 py-3">มาเรียนวันนี้</th>
              <th class="text-left px-4 py-3">งานค้าง</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($classes ?? []) as $c)
              <tr class="border-t">
                <td class="px-4 py-3">{{ $c['name'] }}</td>
                <td class="px-4 py-3">{{ $c['students'] ?? '-' }}</td>
                <td class="px-4 py-3">{{ $c['presentToday'] ?? '-' }}</td>
                <td class="px-4 py-3">{{ $c['pending'] ?? '-' }}</td>
              </tr>
            @empty
              <tr><td class="px-4 py-3 text-gray-400" colspan="4">ไม่มีข้อมูลชั้นเรียน</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
