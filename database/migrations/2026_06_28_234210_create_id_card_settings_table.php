<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('id_card_settings', function (Blueprint $table) {
            $table->id();

            $table->string('header_bg_start', 20)->default('#1e3a5f');
            $table->string('header_bg_end', 20)->default('#2563eb');
            $table->string('card_bg_color', 20)->default('#ffffff');
            $table->string('header_text_color', 20)->default('#ffffff');
            $table->string('accent_color', 20)->default('#1e3a5f');
            $table->string('border_color', 20)->default('#e0e0e0');
            $table->string('font_family', 100)->default('Segoe UI, Arial, sans-serif');
            $table->decimal('card_width', 5, 1)->default(85.6);
            $table->decimal('card_height', 5, 1)->default(54.0);
            $table->integer('font_size_name')->default(11);
            $table->integer('font_size_detail')->default(6);

            $table->boolean('show_logo')->default(true);
            $table->boolean('show_school_name')->default(true);
            $table->boolean('show_npsn')->default(true);
            $table->boolean('show_photo')->default(true);
            $table->boolean('show_nis')->default(true);
            $table->boolean('show_nisn')->default(true);
            $table->boolean('show_class')->default(true);
            $table->boolean('show_gender')->default(true);
            $table->boolean('show_birthplace')->default(true);
            $table->boolean('show_religion')->default(true);
            $table->boolean('show_address')->default(true);
            $table->boolean('show_headmaster')->default(true);
            $table->boolean('show_validity')->default(true);

            $table->string('custom_sidebar_text', 100)->nullable();
            $table->text('custom_footer_text')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('id_card_settings');
    }
};
