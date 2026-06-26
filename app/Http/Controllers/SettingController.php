<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function index()
    {
        $settings = Setting::select(
            columnLocalize('title',       table: 'setting') . ' as title',
            columnLocalize('description', table: 'setting') . ' as description',
            'img',
            'id',
        )
            ->where('visible', 1)
            ->orderByDesc('id')
            ->get();

        return view('dashboard.setting.setting', compact('settings'));
    }

    public function edit(Setting $setting)
    {
        return view('dashboard.setting.edit', compact('setting'));
    }

    /**
     * Update a setting row (text fields + optional logo/image).
     * FIX: image processing moved to ImageService.
     * Settings images use "contain" logic (logos should not be cropped).
     */
    public function update(Setting $setting, Request $request)
    {
        $request->validate([
            'title_ar' => 'required|max:255',
            'image'    => 'nullable|file',
        ]);

        $imgName = $setting->img; // default: keep existing image

        if ($request->hasFile('image')) {
            // For settings (logos), we use 'HowWeHelp' type = contain (no crop)
            $this->imageService->delete($setting->img);
            $imgName = $this->imageService->store($request->file('image'), 'HowWeHelp') ?? $setting->img;
        }

        $setting->update([
            'title_ar'       => $request->input('title_ar'),
            'title_en'       => $request->input('title_en'),
            'description_en' => $request->input('description_en'),
            'description_ar' => $request->input('description_ar'),
            'img'            => $imgName,
        ]);

        clearFrontendCache();

        return redirect()->back()->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();
        clearFrontendCache();

        return back();
    }
}
