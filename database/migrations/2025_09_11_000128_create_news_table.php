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
      Schema::create('news', function (Blueprint $table) {
    $table->id();
    $table->string('title');                 // หัวข้อ
    $table->string('category', 50);          // หมวดหมู่
    $table->string('excerpt', 255)->nullable(); // คำเกริ่น
    $table->text('body');                    // เนื้อหา
    $table->string('cover_path')->nullable();// path รูปหน้าปก
    $table->enum('status', ['draft','published'])->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
