<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->decimal('presence_latitude', 10, 7)->nullable()->after('mission');
            $table->decimal('presence_longitude', 10, 7)->nullable()->after('presence_latitude');
            $table->integer('presence_radius')->default(0)->after('presence_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn(['presence_latitude', 'presence_longitude', 'presence_radius']);
        });
    }
};
