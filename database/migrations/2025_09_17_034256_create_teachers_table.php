<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('teachers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // ผูกบัญชีผู้ใช้ (ถ้ามี)
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email')->nullable()->unique();
        $table->string('phone', 20)->nullable();
        $table->string('primary_subject', 100)->nullable(); // วิชาหลัก
        $table->string('department', 100)->nullable();      // กลุ่มสาระ/แผนก
        $table->string('homeroom')->nullable();             // ห้องประจำ: ห้อง 1..6
        $table->date('hire_date')->nullable();              // วันเริ่มงาน
        $table->text('bio')->nullable();                    // แนะนำตัว
        $table->string('photo_path')->nullable();           // รูปโปรไฟล์ (storage)
        $table->enum('status', ['active','inactive'])->default('active');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
