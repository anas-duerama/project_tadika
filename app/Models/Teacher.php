<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id','first_name','last_name','email','phone',
        'primary_subject','department','homeroom','hire_date','bio',
        'photo_path','status',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }

    // ชื่อเต็ม
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }
}
