<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('place_of_birth', 255)->nullable()->after('nip');
            $table->date('date_of_birth')->nullable()->after('place_of_birth');
            $table->text('address')->nullable()->after('date_of_birth');
            $table->string('phone', 20)->nullable()->after('address');
            $table->enum('gender', ['L', 'P'])->nullable()->after('phone');
            $table->string('religion', 50)->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['place_of_birth', 'date_of_birth', 'address', 'phone', 'gender', 'religion']);
        });
    }
};
