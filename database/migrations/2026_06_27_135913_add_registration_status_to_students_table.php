<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('registration_status', 20)->nullable()->default('pending')->after('religion');
            $table->text('registration_note')->nullable()->after('registration_status');
            $table->boolean('registered_online')->nullable()->default(false)->after('registration_note');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['registration_status', 'registration_note', 'registered_online']);
        });
    }
};
