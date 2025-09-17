<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = ['student_id','date','present','remark'];

    protected $casts = [
        'date' => 'date',
        'present' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
