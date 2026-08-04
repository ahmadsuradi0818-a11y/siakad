<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('id_card_settings', function (Blueprint $table) {
            $table->string('background_template')->nullable()->after('custom_footer_text');
            $table->string('layout_mode', 20)->default('auto')->after('background_template');
            $table->json('element_positions')->nullable()->after('layout_mode');
        });
    }

    public function down(): void
    {
        Schema::table('id_card_settings', function (Blueprint $table) {
            $table->dropColumn(['background_template', 'layout_mode', 'element_positions']);
        });
    }
};
