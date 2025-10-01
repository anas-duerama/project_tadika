<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw ALTER TABLE to avoid requiring doctrine/dbal for column changes
        DB::statement("ALTER TABLE `teachers` MODIFY `first_name` varchar(255) NULL");
        DB::statement("ALTER TABLE `teachers` MODIFY `last_name` varchar(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `teachers` MODIFY `first_name` varchar(255) NOT NULL");
        DB::statement("ALTER TABLE `teachers` MODIFY `last_name` varchar(255) NOT NULL");
    }
};
