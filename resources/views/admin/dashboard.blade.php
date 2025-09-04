{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>แดชบอร์ดผู้ดูแลระบบ</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style> body{font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto} </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- NAVBAR (Admin) -->
  <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white grid place-items-center font-bold">SM</div>
        <span class="font-extrabold text-lg tracking-wide">Admin Panel</span>
        <span class="hidden sm:inline-block text-sm text-gray-400">| ระบบบริหารโรงเรียน</span>
      </div>

      <div class="hidden md:flex items-center gap-6">
        <a href="{{ url('/admin/dashboard') }}" class="text-indigo-600 font-semibold">แดชบอร์ด</a>
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" class="hover:text-indigo-600">นักเรียน</a>
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.news.index') ? route('admin.news.index') : url('/admin/news') }}" class="hover:text-indigo-600">ข่าวสาร</a>
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.grades.index') ? route('admin.grades.index') : url('/admin/grades') }}" class="hover:text-indigo-600">ผลการเรียน</a>
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.teachers.index') ? route('admin.teachers.index') : url('/admin/teachers') }}" class="hover:text-indigo-600">ผู้สอน</a>
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.income.index') ? route('admin.income.index') : url('/admin/income') }}" class="hover:text-indigo-600">รายได้</a>
      </div>

      <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">ไปหน้าเว็บไซต์</a>
        @if (Route::has('login'))
          @auth
            <form method="POST" action="{{ route('logout') }}" class="inline">
              @csrf
              <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-sm">ออกระบบ</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">เข้าสู่ระบบ</a>
          @endauth
        @endif
      </div>
    </div>
  </nav>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold">แดชบอร์ดผู้ดูแลระบบ</h1>
        <p class="text-gray-500 mt-1">ภาพรวมระบบและทางลัดการจัดการข้อมูลหลักของโรงเรียน</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.news.create') ? route('admin.news.create') : url('/admin/news/create') }}"
           class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">+ เพิ่มข่าวสาร</a>
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.students.create') ? route('admin.students.create') : url('/admin/students/create') }}"
           class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-black text-sm">+ เพิ่มนักเรียน</a>
      </div>
    </div>

    <!-- STATS -->
    <section class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="text-sm text-gray-500">จำนวนนักเรียน</div>
        <div class="mt-1 text-3xl font-extrabold">{{ $studentsCount ?? '-' }}</div>
      </div>
      <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="text-sm text-gray-500">จำนวนครูผู้สอน</div>
        <div class="mt-1 text-3xl font-extrabold">{{ $teachersCount ?? '-' }}</div>
      </div>
      <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="text-sm text-gray-500">ข่าวสารทั้งหมด</div>
        <div class="mt-1 text-3xl font-extrabold">{{ $newsCount ?? '-' }}</div>
      </div>
      <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="text-sm text-gray-500">รายการผลการเรียน</div>
        <div class="mt-1 text-3xl font-extrabold">{{ $gradesCount ?? '-' }}</div>
      </div>
      <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="text-sm text-gray-500">รายได้เดือนนี้</div>
        <div class="mt-1 text-3xl font-extrabold">{{ $incomeThisMonth ?? '-' }}</div>
      </div>
    </section>

    <!-- MODULE CARDS -->
    <section class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Students -->
      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-100 grid place-items-center">
            <!-- icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5c-2.28-1.58-4.28-3.58-6.16-5.922L12 14z" />
            </svg>
          </div>
          <h3 class="font-bold text-lg">จัดการข้อมูลนักเรียน</h3>
        </div>
        <p class="mt-2 text-sm text-gray-600">เพิ่ม/แก้ไข/นำออก ข้อมูลนักเรียน, ห้องเรียน, สถานะการศึกษา</p>
        <div class="mt-auto flex gap-2 pt-4">
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.students.create') ? route('admin.students.create') : url('/admin/students/create') }}"
             class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">+ เพิ่ม</a>
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}"
             class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">จัดการ</a>
        </div>
      </article>

      <!-- News -->
      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-indigo-100 grid place-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V7h16v11a2 2 0 01-2 2zM7 9h10M7 13h10M7 17h6" />
            </svg>
          </div>
          <h3 class="font-bold text-lg">จัดการข่าวสาร</h3>
        </div>
        <p class="mt-2 text-sm text-gray-600">เผยแพร่ข่าว, ตั้งหมวดหมู่, แนบรูปหน้าปก, ตั้งเวลาการเผยแพร่</p>
        <div class="mt-auto flex gap-2 pt-4">
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.news.create') ? route('admin.news.create') : url('/admin/news/create') }}"
             class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">+ เพิ่ม</a>
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.news.index') ? route('admin.news.index') : url('/admin/news') }}"
             class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">จัดการ</a>
        </div>
      </article>

      <!-- Grades -->
      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 grid place-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6M9 7h6" />
            </svg>
          </div>
          <h3 class="font-bold text-lg">จัดการผลการเรียน</h3>
        </div>
        <p class="mt-2 text-sm text-gray-600">บันทึกคะแนน, ออกรายงานผล, ส่งออกไฟล์ให้ผู้ปกครอง</p>
        <div class="mt-auto flex gap-2 pt-4">
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.grades.create') ? route('admin.grades.create') : url('/admin/grades/create') }}"
             class="px-3 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm">+ เพิ่ม</a>
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.grades.index') ? route('admin.grades.index') : url('/admin/grades') }}"
             class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">จัดการ</a>
        </div>
      </article>

      <!-- Teachers -->
      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-100 grid place-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 018 17h8a4 4 0 012.879 1.096M15 11a3 3 0 10-6 0 3 3 0 006 0z" />
            </svg>
          </div>
          <h3 class="font-bold text-lg">จัดการข้อมูลผู้สอน</h3>
        </div>
        <p class="mt-2 text-sm text-gray-600">โปรไฟล์ครู, มอบหมายวิชา, ตารางสอน, สิทธิ์ผู้ใช้</p>
        <div class="mt-auto flex gap-2 pt-4">
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.teachers.create') ? route('admin.teachers.create') : url('/admin/teachers/create') }}"
             class="px-3 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 text-sm">+ เพิ่ม</a>
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.teachers.index') ? route('admin.teachers.index') : url('/admin/teachers') }}"
             class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">จัดการ</a>
        </div>
      </article>

      <!-- Income -->
      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-fuchsia-100 grid place-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-fuchsia-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10v10m8-5a8 8 0 11-16 0 8 8 0 0116 0z" />
            </svg>
          </div>
          <h3 class="font-bold text-lg">จัดการข้อมูลรายได้</h3>
        </div>
        <p class="mt-2 text-sm text-gray-600">บันทึกรายรับ/ค่าเทอม, ใบเสร็จ, สรุปรายงานตามเดือน/ภาคเรียน</p>
        <div class="mt-auto flex gap-2 pt-4">
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.income.create') ? route('admin.income.create') : url('/admin/income/create') }}"
             class="px-3 py-2 rounded-lg bg-fuchsia-600 text-white hover:bg-fuchsia-700 text-sm">+ เพิ่ม</a>
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.income.index') ? route('admin.income.index') : url('/admin/income') }}"
             class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">จัดการ</a>
        </div>
      </article>
    </section>

    <!-- OPTIONAL: Recent News table -->
    <section class="mt-8">
      <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <div class="flex items-center justify-between">
          <h2 class="font-bold text-lg">ข่าวสารล่าสุด</h2>
          <a href="{{ \Illuminate\Support\Facades\Route::has('admin.news.index') ? route('admin.news.index') : url('/admin/news') }}"
             class="text-indigo-700 hover:underline text-sm">ดูทั้งหมด →</a>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 border-b">
                <th class="py-2 pr-4">หัวข้อ</th>
                <th class="py-2 pr-4">หมวดหมู่</th>
                <th class="py-2 pr-4">ผู้เขียน</th>
                <th class="py-2 pr-4">เผยแพร่เมื่อ</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($recentNews ?? []) as $item)
                <tr class="border-b last:border-0">
                  <td class="py-2 pr-4">{{ $item['title'] ?? '-' }}</td>
                  <td class="py-2 pr-4">{{ $item['category'] ?? '-' }}</td>
                  <td class="py-2 pr-4">{{ $item['author'] ?? '-' }}</td>
                  <td class="py-2 pr-4">{{ $item['published_at'] ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="py-3 text-gray-500">ยังไม่มีรายการ</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </main>

  <footer class="mt-10 border-t border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500">
      © {{ date('Y') }} School Management — Admin
    </div>
  </footer>

</body>
</html>
