<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ข่าวสารโรงเรียน</title>

  <!-- Tailwind (CDN) - ลบได้ถ้าใช้ Vite + Tailwind อยู่แล้ว -->
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <style>
    body{font-family:Figtree,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto}
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- NAVBAR -->
  <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white grid place-items-center font-bold">SM</div>
        <a href="{{ url('/') }}" class="font-extrabold text-lg tracking-wide">School Management</a>
      </div>

      <div class="hidden md:flex items-center gap-6">
        <a href="{{ url('/') }}" class="hover:text-indigo-600">หน้าแรก</a>
        <a href="{{ url('/news') }}" class="text-indigo-600 font-semibold">ข่าวสาร</a>
        <a href="{{ url('/announcements') }}" class="hover:text-indigo-600">ประกาศ</a>
        <a href="{{ url('/events') }}" class="hover:text-indigo-600">ปฏิทินกิจกรรม</a>
        <a href="{{ url('/contact') }}" class="hover:text-indigo-600">ติดต่อ</a>
      </div>

      <div class="flex items-center gap-3">
        @if (Route::has('login'))
          @auth
            @php
              $role = auth()->user()->role ?? 'User';
              $dash = $role === 'Admin'
                        ? (Route::has('admin.dashboard') ? route('admin.dashboard') : url('/home'))
                        : ($role === 'Teacher'
                            ? (Route::has('teacher.dashboard') ? route('teacher.dashboard') : url('/home'))
                            : (Route::has('user.dashboard') ? route('user.dashboard') : url('/home')));
            @endphp
            <a href="{{ $dash }}" class="hidden sm:inline-flex px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
              แดชบอร์ด
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
              @csrf
              <button class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">ออกจากระบบ</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">เข้าสู่ระบบ</a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="hidden sm:inline-flex px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">สมัครสมาชิก</a>
            @endif
          @endauth
        @endif
      </div>
    </div>
  </nav>

  <!-- HERO / FEATURED NEWS -->
  <section class="bg-gradient-to-r from-indigo-50 to-sky-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
        <div class="lg:col-span-2">
          <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <img src="https://picsum.photos/1200/500?random=9" alt="ภาพข่าวเด่น" class="w-full h-56 sm:h-72 object-cover">
            <div class="p-6">
              <span class="inline-block px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">ข่าวเด่น</span>
              <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold leading-snug">
                โรงเรียนจัดงาน “สัปดาห์วิชาการ” แสดงผลงานนักเรียน 9–13 กันยายน
              </h1>
              <p class="mt-2 text-gray-600">
                เชิญชวนผู้ปกครองและชุมชนร่วมชมผลงาน นวัตกรรม และกิจกรรมประกวดทักษะทางวิชาการตลอดสัปดาห์ ณ หอประชุมใหญ่ของโรงเรียน
              </p>
              <div class="mt-4 flex items-center gap-3 text-sm text-gray-500">
                <span>โดย งานวิชาการ</span>
                <span>•</span>
                <time datetime="2025-09-03">3 ก.ย. 2025</time>
              </div>
              <div class="mt-5">
                <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                  อ่านต่อ
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- QUICK ANNOUNCEMENTS -->
        <aside class="space-y-4">
          <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-lg">ประกาศสำคัญ</h3>
            <ul class="mt-3 space-y-3 text-sm">
              <li class="flex gap-2">
                <span class="mt-1 h-2 w-2 rounded-full bg-amber-500"></span>
                แจ้งเลื่อนเวลาเข้าแถวเช้าวันจันทร์ เป็น 08:30 น.
              </li>
              <li class="flex gap-2">
                <span class="mt-1 h-2 w-2 rounded-full bg-green-600"></span>
                เปิดรับสมัครนักกีฬา งานกีฬาสีประจำปี ถึง 20 ก.ย.
              </li>
              <li class="flex gap-2">
                <span class="mt-1 h-2 w-2 rounded-full bg-sky-600"></span>
                กำหนดชำระค่าเทอม ภาคเรียนที่ 2/2568 ภายใน 30 ก.ย.
              </li>
            </ul>
            <div class="mt-4">
              <a href="{{ url('/announcements') }}" class="text-indigo-700 hover:underline">ดูทั้งหมด →</a>
            </div>
          </div>

          <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-lg">ปฏิทินกิจกรรมใกล้เคียง</h3>
            <ul class="mt-3 space-y-3 text-sm">
              <li class="flex justify-between">
                <span>ประชุมผู้ปกครอง</span><time class="text-gray-500">7 ก.ย.</time>
              </li>
              <li class="flex justify-between">
                <span>แข่งขันตอบปัญหาวิทย์</span><time class="text-gray-500">10 ก.ย.</time>
              </li>
              <li class="flex justify-between">
                <span>ทัศนศึกษา ป.6</span><time class="text-gray-500">18 ก.ย.</time>
              </li>
            </ul>
            <div class="mt-4">
              <a href="{{ url('/events') }}" class="text-indigo-700 hover:underline">เปิดปฏิทิน →</a>
            </div>
          </div>
        </aside>
      </div>

      <!-- FILTER + SEARCH -->
      <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-sm text-gray-500 mr-1">หมวดหมู่:</span>
          <button class="px-3 py-1 rounded-full bg-indigo-600 text-white text-sm">ทั้งหมด</button>
          <button class="px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-sm">วิชาการ</button>
          <button class="px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-sm">กิจกรรม</button>
          <button class="px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-sm">ประกาศ</button>
        </div>
        <form class="w-full sm:w-80">
          <label class="sr-only" for="q">ค้นหาข่าว</label>
          <div class="relative">
            <input id="q" type="search" placeholder="ค้นหาข่าว..." class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <button class="absolute right-1 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-lg bg-gray-900 text-white text-sm hover:bg-black">ค้นหา</button>
          </div>
        </form>
      </div>

      <!-- NEWS LIST -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ([
          ['img'=>'https://picsum.photos/600/380?random=1','tag'=>'วิชาการ','title'=>'ผลการแข่งขันคณิตศาสตร์ระดับเขต','date'=>'2 ก.ย. 2025','by'=>'กลุ่มสาระคณิตฯ'],
          ['img'=>'https://picsum.photos/600/380?random=2','tag'=>'กิจกรรม','title'=>'ชมรมดนตรีเปิดรับสมาชิกใหม่ ภาคเรียนที่ 2','date'=>'1 ก.ย. 2025','by'=>'งานกิจการนักเรียน'],
          ['img'=>'https://picsum.photos/600/380?random=3','tag'=>'ประกาศ','title'=>'แนวทางแต่งกายสัปดาห์อนุรักษ์วัฒนธรรม','date'=>'31 ส.ค. 2025','by'=>'งานปกครอง'],
          ['img'=>'https://picsum.photos/600/380?random=4','tag'=>'วิชาการ','title'=>'ตารางสอบกลางภาค ม.ต้น/ม.ปลาย','date'=>'29 ส.ค. 2025','by'=>'ฝ่ายวัดผล'],
          ['img'=>'https://picsum.photos/600/380?random=5','tag'=>'กิจกรรม','title'=>'ค่ายผู้นำเยาวชน รุ่นที่ 5','date'=>'28 ส.ค. 2025','by'=>'ชุมนุมอาสา'],
          ['img'=>'https://picsum.photos/600/380?random=6','tag'=>'ประกาศ','title'=>'รับสมัครครูอัตราจ้าง วิทยาศาสตร์','date'=>'27 ส.ค. 2025','by'=>'กลุ่มบริหารบุคคล'],
        ] as $n)
          <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm flex flex-col">
            <img src="{{ $n['img'] }}" class="w-full h-40 object-cover" alt="">
            <div class="p-5 flex-1 flex flex-col">
              <span class="inline-block mb-2 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">{{ $n['tag'] }}</span>
              <h3 class="font-bold text-lg leading-snug">{{ $n['title'] }}</h3>
              <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                เนื้อหาข่าวย่อ… (ใส่สรุปข่าวสั้น ๆ เพื่อให้ผู้อ่านตัดสินใจกดอ่านต่อ)
              </p>
              <div class="mt-auto flex items-center justify-between pt-4 text-sm text-gray-500">
                <span>{{ $n['by'] }}</span>
                <time>{{ $n['date'] }}</time>
              </div>
              <div class="mt-3">
                <a href="#" class="inline-flex items-center gap-1 text-indigo-700 hover:underline">อ่านต่อ →</a>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="mt-10 border-t border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-600">
      <div class="flex flex-col sm:flex-row justify-between gap-4">
        <div>
          <div class="font-bold text-gray-900">โรงเรียนบ้านตัวอย่าง</div>
          <div>123 หมู่ 4 ต.ตัวอย่าง อ.ตัวอย่าง จ.ตัวอย่าง 00000</div>
          <div>โทร. 02-123-4567 • อีเมล: info@school.ac.th</div>
        </div>
        <div class="space-x-4">
          <a href="{{ url('/privacy') }}" class="hover:underline">นโยบายความเป็นส่วนตัว</a>
          <a href="{{ url('/terms') }}" class="hover:underline">ข้อกำหนดการใช้งาน</a>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
