<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolProfileController extends Controller
{
    public function index()
    {
        $profile = SchoolProfile::first();
        return view('admin.school-profile.index', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'headmaster' => 'nullable|string|max:255',
            'nip_headmaster' => 'nullable|string|max:50',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'presence_latitude' => 'nullable|numeric|between:-90,90',
            'presence_longitude' => 'nullable|numeric|between:-180,180',
            'presence_radius' => 'nullable|integer|min:0',
        ]);

        $profile = SchoolProfile::first();

        if ($request->hasFile('logo')) {
            if ($profile && $profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $validated['logo'] = $request->file('logo')->store('school', 'public');
        }

        if ($profile) {
            $profile->update($validated);
        } else {
            SchoolProfile::create($validated);
        }

        return redirect()->route('admin.school-profile.index')
            ->with('success', 'Profil sekolah berhasil diperbarui.');
    }
}
