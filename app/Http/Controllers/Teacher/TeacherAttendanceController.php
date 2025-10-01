<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    private string $classCol = 'class_level';
    private string $roomCol  = 'room';

    public function index(Request $request)
    {
        $date  = $request->get('date', now()->toDateString());

        $teacher = $request->user()?->teacher ?? null;
        $primarySubject = $request->get('class', $teacher?->primary_subject ?? null);
        // normalize teaching_rooms to array (accept array, json string, or comma-separated)
        $rawTeaching = $teacher?->teaching_rooms ?? [];
        if (is_array($rawTeaching)) {
            $teachingRooms = $rawTeaching;
        } else {
            $raw = (string)$rawTeaching;
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $teachingRooms = $decoded;
            } elseif ($raw === '') {
                $teachingRooms = [];
            } else {
                $teachingRooms = preg_split('/\s*,\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
            }
        }
        $teachingRooms = array_values(array_unique(array_map('trim', $teachingRooms)));

        $selectedRoom = $request->get('room', null) ?? ($teachingRooms[0] ?? null);

        // helper to produce likely variants for a room string
        $makeRoomVariants = function($r) {
            $r = trim((string)$r);
            if ($r === '') return [];
            $variants = [$r];
            // digits-only
            $digits = preg_replace('/\D+/', '', $r);
            if ($digits !== '') $variants[] = $digits;
            // strip prefixes like 'ห้อง', 'room', 'r'
            $noPrefix = preg_replace('/^(ห้อง|room|r)\s*/iu', '', $r);
            if ($noPrefix !== '' && $noPrefix !== $r) $variants[] = $noPrefix;
            $variants = array_values(array_filter(array_unique(array_map('trim', $variants))));
            return $variants;
        };

        $roomCol = $this->roomCol;

        // detect if students table actually has room values
    $studentHasRoomValues = Student::query()->whereNotNull($roomCol)->where($roomCol, '<>', '')->exists();

        $students = Student::query()
            ->when($selectedRoom && $studentHasRoomValues, function($q) use ($selectedRoom, $makeRoomVariants, $roomCol) {
                $variants = $makeRoomVariants($selectedRoom);
                if (empty($variants)) return;
                $q->where(function($w) use ($variants, $roomCol) {
                    foreach ($variants as $v) {
                        $w->orWhere($roomCol, $v);
                    }
                });
            })
            ->orderBy('student_code')
            ->get();

        $rangeFrom = $request->get('from');
        $rangeTo   = $request->get('to');
        $period    = $request->get('period');

        if ($period === 'week') {
            $base = Carbon::parse($date);
            $rangeFrom = $base->startOfWeek()->toDateString();
            $rangeTo   = $base->endOfWeek()->toDateString();
        } elseif ($period === 'month') {
            $base = Carbon::parse($date);
            $rangeFrom = $base->startOfMonth()->toDateString();
            $rangeTo   = $base->endOfMonth()->toDateString();
        } else {
            $rangeFrom = $rangeFrom ?? $date;
            $rangeTo   = $rangeTo ?? $date;
        }

        $studentIds = $students->pluck('id')->toArray();
        $rangeRecords = AttendanceRecord::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$rangeFrom, $rangeTo])
            ->get();

        $recordsByStudent = $rangeRecords->groupBy('student_id');
        $studentSummaries = [];
        foreach ($students as $s) {
            $group = $recordsByStudent[$s->id] ?? collect([]);
            $p = $group->filter(fn($r) => ($r->status ?? ($r->present ? 'present' : 'absent')) === 'present')->count();
            $a = $group->filter(fn($r) => ($r->status ?? '') === 'absent')->count();
            $l = $group->filter(fn($r) => ($r->status ?? '') === 'leave')->count();
            $studentSummaries[$s->id] = compact('p','a','l');
        }

        $presentCount = $rangeRecords->filter(fn($r) => ($r->status ?? ($r->present ? 'present' : 'absent')) === 'present')->count();
        $absentCount  = $rangeRecords->filter(fn($r) => ($r->status ?? '') === 'absent')->count();
        $leaveCount   = $rangeRecords->filter(fn($r) => ($r->status ?? '') === 'leave')->count();
        $lastSavedAt  = $rangeRecords->max('updated_at');

        $records = AttendanceRecord::whereDate('date', $date)->get()->keyBy('student_id');

        return view('teacher.attendance.index', compact(
            'date','selectedRoom','students','records','primarySubject','teachingRooms',
            'presentCount','absentCount','leaveCount','lastSavedAt',
            'rangeFrom','rangeTo','period','studentSummaries'
        ));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'date'    => ['required','date'],
            'class'   => ['nullable','string','max:50'],
            'room'    => ['nullable','string','max:50'],
            'present' => ['array'],
            'status'  => ['array'],
            'remark'  => ['array'],
        ]);

        $selectedRoom = $v['room'] ?? null;
        // reuse variant matching
        $makeRoomVariants = function($r) {
            $r = trim((string)$r);
            if ($r === '') return [];
            $variants = [$r];
            $digits = preg_replace('/\D+/', '', $r);
            if ($digits !== '') $variants[] = $digits;
            $noPrefix = preg_replace('/^(ห้อง|room|r)\s*/iu', '', $r);
            if ($noPrefix !== '' && $noPrefix !== $r) $variants[] = $noPrefix;
            $variants = array_values(array_filter(array_unique(array_map('trim', $variants))));
            return $variants;
        };

    $roomCol = $this->roomCol;
    $studentHasRoomValues = Student::query()->whereNotNull($roomCol)->where($roomCol, '<>', '')->exists();

        $students = Student::query()
            ->when($selectedRoom && $studentHasRoomValues, function($q) use ($selectedRoom, $makeRoomVariants, $roomCol) {
                $variants = $makeRoomVariants($selectedRoom);
                if (empty($variants)) return;
                $q->where(function($w) use ($variants, $roomCol) {
                    foreach ($variants as $v) {
                        $w->orWhere($roomCol, $v);
                    }
                });
            })
            ->orderBy('student_code')
            ->get(['id']);

        foreach ($students as $s) {
            $status = ($v['status'] ?? [])[$s->id] ?? null;
            $isPresent = $status === 'present';
            $note = ($v['remark'] ?? [])[$s->id] ?? null;

            AttendanceRecord::updateOrCreate(
                ['student_id' => $s->id, 'date' => $v['date']],
                ['present' => $isPresent, 'remark' => $note, 'status' => $status]
            );
        }

        return back()->with('ok','บันทึกการมาเรียนสำเร็จ');
    }

    public function history(Request $request)
    {
        $teacher = $request->user()?->teacher ?? null;
        // normalize teaching_rooms to array like in index()
        $rawTeaching = $teacher?->teaching_rooms ?? [];
        if (is_array($rawTeaching)) {
            $teachingRooms = $rawTeaching;
        } else {
            $raw = (string)$rawTeaching;
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $teachingRooms = $decoded;
            } elseif ($raw === '') {
                $teachingRooms = [];
            } else {
                $teachingRooms = preg_split('/\s*,\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
            }
        }
        $teachingRooms = array_values(array_unique(array_map('trim', $teachingRooms)));

        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to   = $request->get('to', now()->toDateString());
        $selectedRoom = $request->get('room', $teachingRooms[0] ?? null);

        $roomCol = $this->roomCol;

        // helper for history() — produce variants for a room string
        $makeRoomVariants = function($r) {
            $r = trim((string)$r);
            if ($r === '') return [];
            $variants = [$r];
            $digits = preg_replace('/\D+/', '', $r);
            if ($digits !== '') $variants[] = $digits;
            $noPrefix = preg_replace('/^(ห้อง|room|r)\s*/iu', '', $r);
            if ($noPrefix !== '' && $noPrefix !== $r) $variants[] = $noPrefix;
            $variants = array_values(array_filter(array_unique(array_map('trim', $variants))));
            return $variants;
        };

        $studentHasRoomValues = Student::query()->whereNotNull($roomCol)->where($roomCol, '<>', '')->exists();

        $studentIds = Student::query()
            ->when($selectedRoom && $studentHasRoomValues, function($q) use ($selectedRoom, $makeRoomVariants, $roomCol) {
                $variants = $makeRoomVariants($selectedRoom);
                if (empty($variants)) return;
                $q->where(function($w) use ($variants, $roomCol) {
                    foreach ($variants as $v) {
                        $w->orWhere($roomCol, $v);
                    }
                });
            })
            ->pluck('id')
            ->toArray();

        $records = collect([]);
        if (!empty($studentIds)) {
            $records = AttendanceRecord::with('student')
                ->whereIn('student_id', $studentIds)
                ->whereBetween('date', [$from, $to])
                ->orderBy('date','desc')
                ->get()
                ->groupBy(fn($r) => $r->date->toDateString());
        }

        return view('teacher.history.index', compact('from','to','teachingRooms','selectedRoom','records'));
    }
}