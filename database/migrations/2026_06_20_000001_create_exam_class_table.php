<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_class', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unique(['exam_id', 'class_id']);
            $table->timestamps();
        });

        DB::statement('INSERT INTO exam_class (exam_id, class_id, created_at, updated_at) SELECT id, class_id, NOW(), NOW() FROM exams');
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_class');
    }
};
