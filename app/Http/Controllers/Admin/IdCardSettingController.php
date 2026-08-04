<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdCardSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IdCardSettingController extends Controller
{
    public function edit()
    {
        $setting = IdCardSetting::getSettings();
        return view('admin.id-card-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'header_bg_start' => 'required|string|max:20',
            'header_bg_end' => 'required|string|max:20',
            'card_bg_color' => 'required|string|max:20',
            'header_text_color' => 'required|string|max:20',
            'accent_color' => 'required|string|max:20',
            'border_color' => 'required|string|max:20',
            'font_family' => 'required|string|max:100',
            'card_width' => 'required|numeric|min:50|max:120',
            'card_height' => 'required|numeric|min:30|max:90',
            'font_size_name' => 'required|integer|min:8|max:24',
            'font_size_detail' => 'required|integer|min:5|max:16',
            'layout_mode' => 'required|in:auto,custom',
            'custom_sidebar_text' => 'nullable|string|max:100',
            'custom_footer_text' => 'nullable|string',
        ]);

        $data['show_logo'] = $request->boolean('show_logo');
        $data['show_school_name'] = $request->boolean('show_school_name');
        $data['show_npsn'] = $request->boolean('show_npsn');
        $data['show_photo'] = $request->boolean('show_photo');
        $data['show_nis'] = $request->boolean('show_nis');
        $data['show_nisn'] = $request->boolean('show_nisn');
        $data['show_class'] = $request->boolean('show_class');
        $data['show_gender'] = $request->boolean('show_gender');
        $data['show_birthplace'] = $request->boolean('show_birthplace');
        $data['show_religion'] = $request->boolean('show_religion');
        $data['show_address'] = $request->boolean('show_address');
        $data['show_headmaster'] = $request->boolean('show_headmaster');
        $data['show_validity'] = $request->boolean('show_validity');

        if ($request->hasFile('background_template')) {
            $request->validate([
                'background_template' => 'image|mimes:png,jpg,jpeg|max:2048',
            ]);
            $setting = IdCardSetting::getSettings();
            if ($setting->background_template) {
                Storage::disk('public')->delete($setting->background_template);
            }
            $path = $request->file('background_template')->store('id-card-templates', 'public');
            $data['background_template'] = $path;
        }

        $positions = [];
        $elementKeys = [
            'logo', 'school_name', 'npsn', 'photo', 'student_name',
            'nis_nisn', 'class', 'gender', 'birthplace', 'religion',
            'address', 'headmaster', 'validity', 'custom_sidebar_text', 'custom_footer_text',
        ];
        foreach ($elementKeys as $key) {
            $posX = $request->input("pos_{$key}_x");
            $posY = $request->input("pos_{$key}_y");
            $fontSize = $request->input("pos_{$key}_font_size");
            if ($posX !== null || $posY !== null) {
                $positions[$key] = [];
                if ($posX !== null) $positions[$key]['x'] = (int) $posX;
                if ($posY !== null) $positions[$key]['y'] = (int) $posY;
                if ($fontSize !== null) $positions[$key]['font_size'] = (int) $fontSize;
            }
        }
        if (!empty($positions)) {
            $data['element_positions'] = $positions;
        }

        $setting = IdCardSetting::getSettings();
        $setting->update($data);

        return redirect()->route('admin.id-card-settings.edit')
            ->with('success', 'Template kartu ID berhasil diperbarui.');
    }

    public function destroyTemplate()
    {
        $setting = IdCardSetting::getSettings();
        if ($setting->background_template) {
            Storage::disk('public')->delete($setting->background_template);
            $setting->update(['background_template' => null]);
        }
        return back()->with('success', 'Background template berhasil dihapus.');
    }
}
