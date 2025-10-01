<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    protected $fillable = [
        'user_id','first_name','last_name','email','phone',
        'primary_subject','department','homeroom','hire_date','bio',
        'photo_path','status',
        // แยกฟิลด์ชั้น/ห้อง (ถ้ามี)
        'homeroom_class','homeroom_room',
        // ถ้ามีฟิลด์เก็บรายชื่อวิชาเป็น csv/json
        'subjects',
        // ห้องที่สอน (เก็บเป็น array/json)
        'teaching_rooms',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'teaching_rooms' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** คืนรายชื่อ "ชั้น/ห้อง" ที่ครูสอน */
    public function taughtRooms(): array
    {
        $rooms = [];

        // แบบระบุแยกฟิลด์
        if (($this->homeroom_class ?? null) || ($this->homeroom_room ?? null)) {
            $rooms[] = ['class' => trim((string) $this->homeroom_class), 'room' => trim((string) $this->homeroom_room)];
        }

        // แบบสตริงรวม "ป.6/1, ป.5/2"
        if ($this->homeroom ?? null) {
            foreach (preg_split('/\s*,\s*/u', (string) $this->homeroom, -1, PREG_SPLIT_NO_EMPTY) as $pair) {
                [$c, $r] = array_pad(preg_split('/\s*\/\s*/u', $pair, 2), 2, '');
                $rooms[] = ['class' => trim($c), 'room' => trim($r)];
            }
        }

        // unique + กรองค่าว่าง
        return collect($rooms)
            ->filter(fn($x) => ($x['class'] ?? '') !== '' || ($x['room'] ?? '') !== '')
            ->unique(fn($x) => ($x['class'] ?? '') . '/' . ($x['room'] ?? ''))
            ->values()
            ->all();
    }

    /** คืนรายชื่อ "วิชา" จากโปรไฟล์ (primary_subject / subjects(csv|json)) */
    public function taughtSubjects(): array
    {
        $subs = [];

        if ($this->primary_subject) {
            $subs[] = trim((string) $this->primary_subject);
        }
        // ถ้ามีฟิลด์ subjects เป็น csv หรือ json
        if ($this->subjects) {
            $raw = $this->subjects;
            if (is_string($raw)) {
                // ลอง parse json; ถ้าไม่ใช่ก็แตก csv
                $arr = json_decode($raw, true);
                if (is_array($arr)) {
                    foreach ($arr as $s) {
                        $subs[] = trim((string) $s);
                    }
                } else {
                    foreach (preg_split('/\s*,\s*/u', $raw, -1, PREG_SPLIT_NO_EMPTY) as $s) {
                        $subs[] = trim($s);
                    }
                }
            } elseif (is_array($raw)) {
                foreach ($raw as $s) $subs[] = trim((string) $s);
            }
        }

        // เผื่อไม่มีข้อมูลอะไรเลย
        if (empty($subs)) $subs[] = 'ทั่วไป';

        return collect($subs)->filter()->unique()->values()->all();
    }

    // ชื่อเต็ม (quality-of-life)
    protected $appends = ['full_name','taught_rooms','taught_subjects'];
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }
    public function getTaughtRoomsAttribute(): array  { return $this->taughtRooms(); }
    public function getTaughtSubjectsAttribute(): array { return $this->taughtSubjects(); }
}
