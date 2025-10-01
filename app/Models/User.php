<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* =========================
     |  Relationships
     |=========================*/

    /**
     * โปรไฟล์การสอนของผู้ใช้ (one-to-one)
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /* =========================
     |  Role helpers (case-insensitive)
     |=========================*/

    protected function normalizedRole(): string
    {
        return strtolower((string) $this->role);
    }

    public function isAdmin(): bool
    {
        return $this->normalizedRole() === 'admin';
    }

    public function isTeacher(): bool
    {
        $r = $this->normalizedRole();
        return $r === 'teacher' || $r === 'teachers';
    }

    public function isUser(): bool
    {
        $r = $this->normalizedRole();
        return $r === 'user' || $r === '';
    }

    /* =========================
     |  Query Scopes สะดวกใช้
     |=========================*/
    public function scopeTeachers($query)
    {
        return $query->whereIn('role', ['teacher','teachers','Teacher','TEACHER']);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['admin','Admin','ADMIN']);
    }
}
