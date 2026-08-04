<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdCardSetting extends Model
{
    protected $fillable = [
        'header_bg_start',
        'header_bg_end',
        'card_bg_color',
        'header_text_color',
        'accent_color',
        'border_color',
        'font_family',
        'card_width',
        'card_height',
        'font_size_name',
        'font_size_detail',
        'show_logo',
        'show_school_name',
        'show_npsn',
        'show_photo',
        'show_nis',
        'show_nisn',
        'show_class',
        'show_gender',
        'show_birthplace',
        'show_religion',
        'show_address',
        'show_headmaster',
        'show_validity',
        'custom_sidebar_text',
        'custom_footer_text',
        'background_template',
        'layout_mode',
        'element_positions',
    ];

    protected function casts(): array
    {
        return [
            'card_width' => 'decimal:1',
            'card_height' => 'decimal:1',
            'show_logo' => 'boolean',
            'show_school_name' => 'boolean',
            'show_npsn' => 'boolean',
            'show_photo' => 'boolean',
            'show_nis' => 'boolean',
            'show_nisn' => 'boolean',
            'show_class' => 'boolean',
            'show_gender' => 'boolean',
            'show_birthplace' => 'boolean',
            'show_religion' => 'boolean',
            'show_address' => 'boolean',
            'show_headmaster' => 'boolean',
            'show_validity' => 'boolean',
            'element_positions' => 'array',
        ];
    }

    public static function getSettings()
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([]);
        }
        return $setting;
    }

    public function getPosition($element, $defaultX = 0, $defaultY = 0)
    {
        $positions = $this->element_positions ?? [];
        $pos = $positions[$element] ?? [];
        return [
            'x' => $pos['x'] ?? $defaultX,
            'y' => $pos['y'] ?? $defaultY,
        ];
    }
}
