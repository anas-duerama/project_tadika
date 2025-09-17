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
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('no');                 // เลขที่
        $table->string('class_level', 50);             // ชั้นเรียน
        $table->string('first_name', 100);
        $table->string('last_name', 100);
        $table->string('student_code', 50)->unique();  // เลขประจำตัวนักเรียน
        $table->string('citizen_id', 13)->unique();    // บัตรประชาชน 13 หลัก
        $table->date('birthdate')->nullable();
        $table->string('father_name', 100)->nullable();
        $table->string('father_job', 100)->nullable();
        $table->string('mother_name', 100)->nullable();
        $table->string('mother_job', 100)->nullable();
        $table->string('phone', 20)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
