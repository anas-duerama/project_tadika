<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Teacher')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style> body{font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto} </style>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="min-h-screen flex">

  {{-- ============ SIDEBAR ============ --}}
  @php
    $studentsIndex = \Illuminate\Support\Facades\Route::has('teacher.students.index') ? route('teacher.students.index') : url('/teacher/students');
    $gradesIndex   = \Illuminate\Support\Facades\Route::has('teacher.grades.index')   ? route('teacher.grades.index')   : url('/teacher/grades');
    $attIndex      = \Illuminate\Support\Facades\Route::has('teacher.attendance.index')? route('teacher.attendance.index'): url('/teacher/attendance');
    $historyIndex  = \Illuminate\Support\Facades\Route::has('teacher.history.index')? route('teacher.history.index'): url('/teacher/history');

  @endphp
  <aside class="w-72 bg-white border-r border-gray-200 hidden md:block">
    <div class="p-4">
      <div class="text-xl font-extrabold">Teacher Panel</div>
      <div class="text-sm text-gray-500 mt-1">{{ auth()->user()->name ?? 'คุณครู' }}</div>
    </div>
    <nav class="p-2 space-y-1">
  <a href="{{ route('teacher.dashboard') }}" class="block px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('teacher.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-50' }}">แดชบอร์ด</a>
  <a href="{{ route('teacher.profile') }}" class="block px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('teacher.profile') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-50' }}">โปรไฟล์ผู้สอน</a>
      <a href="{{ $studentsIndex }}" class="block px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('teacher.students.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-50' }}">จัดการข้อมูลนักเรียน</a>
      <a href="{{ $gradesIndex }}" class="block px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('teacher.grades.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-50' }}">จัดการผลการเรียน</a>
      <a href="{{ $attIndex }}" class="block px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('teacher.attendance.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-50' }}">จัดการการมาเรียน</a>
      <a href="{{ $historyIndex  }}" class="block px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('teacher.history.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-50' }}">ประวัติการมาเรียน</a>
      <form method="POST" action="{{ route('logout') }}" class="px-2 pt-3">
        @csrf
        <button class="w-full rounded-xl border px-4 py-2 text-sm hover:bg-gray-100">ออกจากระบบ</button>
      </form>
    </nav>
  </aside>

  {{-- ============ MAIN ============ --}}
  <section class="flex-1 flex flex-col">
    <header class="bg-white border-b border-gray-200">
      <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <h1 class="text-2xl md:text-3xl font-extrabold">@yield('page_title','แดชบอร์ด — คุณครู')</h1>
        <div class="text-sm text-gray-500">บทบาท: <span class="font-semibold uppercase">teacher</span></div>
      </div>
    </header>

    <main class="px-4 sm:px-6 lg:px-8 py-6">
      @if(session('ok'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700">{{ session('ok') }}</div>
      @endif
      @yield('content')
    </main>

    <footer class="mt-10 border-t border-gray-200 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500">
        © {{ date('Y') }} School Management — Teacher
      </div>
    </footer>
  </section>
</div>
</body>
</html>
