<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('place_of_birth', 255)->nullable()->after('nis');
            $table->date('date_of_birth')->nullable()->after('place_of_birth');
            $table->text('address')->nullable()->after('date_of_birth');
            $table->string('parent_name', 255)->nullable()->after('address');
            $table->string('nisn', 20)->nullable()->unique()->after('parent_name');
            $table->enum('gender', ['L', 'P'])->nullable()->after('nisn');
            $table->string('religion', 50)->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['place_of_birth', 'date_of_birth', 'address', 'parent_name', 'nisn', 'gender', 'religion']);
        });
    }
};
