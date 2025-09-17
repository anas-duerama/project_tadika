<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('term');    // เช่น 1/2568
            $table->string('subject'); // เช่น คณิตศาสตร์
            $table->string('score')->nullable();
            $table->timestamps();

            $table->unique(['student_id','term','subject']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
