@extends('teacher.layout')

@section('title', 'จัดการข้อมูลนักเรียน — คุณครู')
@section('page_title', 'จัดการข้อมูลนักเรียน (แยกตามชั้น/ห้อง)')

@php
    use Illuminate\Support\Facades\Route as R;

    $routeIndex  = R::has('teacher.students.index')   ? route('teacher.students.index')   : url()->current();
    $routeAttend = R::has('teacher.attendance.index') ? 'teacher.attendance.index'        : null;
@endphp

@section('content')
    {{-- สรุป --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <div class="text-sm text-gray-500">นักเรียนตรงตามเงื่อนไข</div>
            <div class="mt-1 text-3xl font-extrabold">{{ $summary['total'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <div class="text-sm text-gray-500">จำนวนชั้น</div>
            <div class="mt-1 text-3xl font-extrabold">{{ $summary['class_count'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <div class="text-sm text-gray-500">จำนวนห้อง</div>
            <div class="mt-1 text-3xl font-extrabold">{{ $summary['room_count'] ?? 0 }}</div>
        </div>
    </div>

    {{-- ค้นหา/กรอง --}}
    <div class="bg-white rounded-2xl p-4 border shadow-sm mb-4">
        <div class="font-semibold mb-3">ค้นหาและกรอง</div>

        <form method="GET" action="{{ $routeIndex }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="ค้นหา รหัส/ชื่อ"
                class="rounded-xl border px-4 py-2 md:col-span-2"
            >

            <input
                {{-- type="text"
                name="class"
                value="{{ $class }}"
                placeholder="ชั้น เช่น ป.6"
                class="rounded-xl border px-4 py-2"
                @if(!$hasClass) disabled title="ไม่พบคอลัมน์ {{ $classCol }} ในตาราง students" @endif --}}
            >

            <input
                type="text"
                name="room"
                value="{{ $room }}"
                placeholder="ห้อง เช่น 1"
                class="rounded-xl border px-4 py-2"
                @if(!$hasRoom) disabled title="ไม่พบคอลัมน์ {{ $roomCol }} ในตาราง students" @endif
            >

            <div class="flex gap-2">
                <button class="rounded-xl bg-indigo-600 text-white px-5 py-2 font-medium hover:bg-indigo-700">
                    ค้นหา
                </button>
                <a href="{{ $routeIndex }}" class="rounded-xl border px-5 py-2 text-sm hover:bg-gray-100">
                    ล้างเงื่อนไข
                </a>
            </div>
        </form>

        @if(!$hasClass || !$hasRoom)
            <div class="mt-3 text-xs text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                <b>หมายเหตุ:</b>
                ไม่พบคอลัมน์
                @unless($hasClass)<code>{{ $classCol }}</code>@endunless
                @if(!$hasClass && !$hasRoom) และ @endif
                @unless($hasRoom)<code>{{ $roomCol }}</code>@endunless
                ในตาราง <code>students</code> — ระบบจะแสดงข้อมูลโดยไม่กรองชั้น/ห้อง
            </div>
        @endif
    </div>

    {{-- แสดงผล: ชั้น → ห้อง --}}
    @forelse ($grouped as $classLevel => $rooms)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xl font-bold">ชั้น {{ $classLevel }}</h2>
                <div class="text-sm text-gray-500">
                    ห้องทั้งหมด: {{ is_countable($rooms) ? count($rooms) : '-' }}
                </div>
            </div>

            @php
                $roomCount = is_countable($rooms) ? count($rooms) : 0;
            @endphp

            {{-- ถ้ามีห้องเดียว ให้การ์ดกินเต็ม 2 คอลัมน์ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($rooms as $roomNo => $list)
                    <div class="bg-white rounded-2xl border shadow-sm {{ $roomCount === 1 ? 'md:col-span-2' : '' }}">
                        <div class="px-4 py-3 border-b flex items-center justify-between">
                            <div class="font-semibold">ห้อง {{ $roomNo }}</div>
                            <div class="text-xs text-gray-500">จำนวนนักเรียน: {{ $list->count() }}</div>
                        </div>

                        <div class="p-4 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="text-left px-3 py-2">รหัส</th>
                                        <th class="text-left px-3 py-2">ชื่อนักเรียน</th>
                                        <th class="text-left px-3 py-2 w-40">จัดการ</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($list as $stu)
                                        @php
                                            $canAttend = ($classLevel !== '-' && $roomNo !== '-');
                                            $attendUrl = $canAttend && $routeAttend
                                                ? route($routeAttend, [
                                                    'date'  => now()->toDateString(),
                                                    'class' => $classLevel,
                                                    'room'  => $roomNo,
                                                  ])
                                                : 'javascript:void(0)';
                                        @endphp

                                        <tr class="border-t">
                                            <td class="px-3 py-2 font-mono">{{ $stu->_code }}</td>
                                            <td class="px-3 py-2">{{ $stu->_name }}</td>
                                            <td class="px-3 py-2">
                                                <div class="flex flex-wrap gap-2">
                                                    <a
                                                        href="{{ $attendUrl }}"
                                                        class="rounded-xl border px-3 py-1.5 text-xs hover:bg-gray-100 @if(!$canAttend) pointer-events-none opacity-50 @endif"
                                                    >
                                                        เช็คชื่อวันนี้
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-3 text-gray-500">
                                                ยังไม่มีนักเรียนในห้องนี้
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl p-6 border text-gray-500">
            ไม่พบข้อมูลนักเรียนตามเงื่อนไข
        </div>
    @endforelse
@endsection
