{{-- resources/views/admin/layout.blade.php --}}
@php
    use Illuminate\Support\Facades\Route as R;

    $dashboardUrl     = R::has('admin.dashboard')      ? route('admin.dashboard')      : url('/admin/dashboard');
    $studentsIndexUrl = R::has('admin.students.index') ? route('admin.students.index') : url('/admin/students');
    $newsIndexUrl     = R::has('admin.news.index')     ? route('admin.news.index')     : url('/admin/news');
    $gradesIndexUrl   = R::has('admin.grades.index')   ? route('admin.grades.index')   : (R::has('teacher.grades.index') ? route('teacher.grades.index') : '#');
    $teachersIndexUrl = R::has('admin.teachers.index') ? route('admin.teachers.index') : url('/admin/teachers');
    $incomeIndexUrl   = R::has('admin.income.index')   ? route('admin.income.index')   : url('/admin/income');
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Admin Panel')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto}</style>
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex md:gap-6 lg:gap-8">

  {{-- SIDEBAR --}}
  <aside class="hidden md:flex md:flex-col w-72 bg-white border-r border-gray-200 p-4">
    <div class="flex items-center gap-3 px-2">
      <div class="w-12 h-12 rounded-full bg-pink-600/10 grid place-items-center">
        <span class="text-pink-600 font-extrabold text-xl">SM</span>
      </div>
      <div>
        <div class="font-semibold leading-tight">ตาดีกา</div>
        <div class="text-xs text-gray-500">School Admin</div>
      </div>
    </div>

    <nav class="mt-6 space-y-1">
      <a href="{{ $dashboardUrl }}"
         class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <span>🏠</span> <span>หน้าแรกผู้ดูแล</span>
      </a>
      <a href="{{ $studentsIndexUrl }}"
         class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->is('admin/students*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <span>🎓</span> <span>ข้อมูลนักเรียน</span>
      </a>
      <a href="{{ $newsIndexUrl }}"
         class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->is('admin/news*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <span>📰</span> <span>ข่าวสาร</span>
      </a>
      <a href="{{ $gradesIndexUrl }}"
         class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->is('admin/grades*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <span>🟩</span> <span>ผลการเรียน</span>
      </a>
      <a href="{{ $teachersIndexUrl }}"
         class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->is('admin/teachers*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <span>👩‍🏫</span> <span>ผู้สอน</span>
      </a>
      {{-- ลิงก์ไปยังโปรไฟล์/แดชบอร์ดของผู้สอน (สำหรับดูข้อมูลผู้สอนแบบเฉพาะ) --}}
    <a href="{{ R::has('teacher.dashboard') ? route('teacher.dashboard') : url('/teacher') }}"
      class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->is('teacher*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <span>🧾</span> <span>โปรไฟล์ผู้สอน</span>
      </a>
      <a href="{{ $incomeIndexUrl }}"
         class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->is('admin/income*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <span>💰</span> <span>รายได้</span>
      </a>

      <form method="POST" action="{{ route('logout') }}" class="px-1 pt-3">
        @csrf
        <button class="w-full rounded-xl border px-4 py-2 text-sm hover:bg-gray-100">ออกจากระบบ</button>
      </form>
    </nav>
  </aside>

  {{-- MAIN --}}
  <section class="flex-1 flex flex-col">
    <header class="bg-white border-b border-gray-200 md:pl-6 lg:pl-8">
      <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <div class="min-w-0">
          <h1 class="text-2xl md:text-3xl font-extrabold">@yield('page_title','Admin Panel')</h1>
          @hasSection('page_subtitle')
            <p class="text-sm text-gray-500 mt-1">@yield('page_subtitle')</p>
          @endif
        </div>
        <div class="shrink-0">
          @yield('page_actions')
        </div>
      </div>
    </header>

    <main class="px-4 sm:px-6 lg:px-8 py-6 md:pl-6 lg:pl-8">
      @yield('content')
    </main>

    <footer class="mt-auto border-t border-gray-200 bg-white">
      <div class="px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500">
        © {{ date('Y') }} School Management — Admin
      </div>
    </footer>
  </section>
</div>
</body>
</html>
