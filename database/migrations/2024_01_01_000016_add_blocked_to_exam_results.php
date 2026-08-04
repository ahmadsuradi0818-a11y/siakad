<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('score');
            $table->timestamp('started_at')->nullable()->after('is_blocked');
            $table->decimal('score', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropColumn(['is_blocked', 'started_at']);
            $table->decimal('score', 5, 2)->nullable(false)->change();
        });
    }
};
