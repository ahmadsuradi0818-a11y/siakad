<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE exams MODIFY COLUMN type ENUM('uh','uts','uas','pat','tryout') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE exams MODIFY COLUMN type ENUM('uts','uas') NOT NULL");
    }
};
