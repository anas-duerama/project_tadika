<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // เพิ่มคอลัมน์ term/subject ถ้ายังไม่มี (ชนิด string และทำดัชนีย่อย)
            if (!Schema::hasColumn('grades', 'term')) {
                $table->string('term', 100)->index();
            }
            if (!Schema::hasColumn('grades', 'subject')) {
                $table->string('subject', 100)->index();
            }

            // สร้าง unique constraint กันข้อมูลซ้ำในชุด (student_id, term, subject)
            // ชื่ออินเด็กซ์กำหนดเองเพื่อให้ drop ตอน down ได้แน่นอน
            $table->unique(['student_id', 'term', 'subject'], 'grades_student_term_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // ลบ unique constraint ตัวนี้
            $table->dropUnique('grades_student_term_subject_unique');

            // หมายเหตุ: ไม่ลบคอลัมน์ term/subject กลับ (ปกติเราคงอยากเก็บไว้ใช้งาน)
            // ถ้าต้องการลบจริง ๆ ให้เพิ่ม:
            // if (Schema::hasColumn('grades','term'))    $table->dropColumn('term');
            // if (Schema::hasColumn('grades','subject')) $table->dropColumn('subject');
        });
    }
};
