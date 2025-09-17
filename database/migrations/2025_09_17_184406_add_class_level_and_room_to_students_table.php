<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'class_level')) {
                $table->string('class_level')->nullable()->after('fullname');
            }
            if (!Schema::hasColumn('students', 'room')) {
                $table->string('room')->nullable()->after('class_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'room')) {
                $table->dropColumn('room');
            }
            if (Schema::hasColumn('students', 'class_level')) {
                $table->dropColumn('class_level');
            }
        });
    }
};
