<?php

namespace App\Http\Controllers;

use App\Enums\ImageType;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(ImageService $imageService): View
    {
        $settings = Setting::all()->pluck('value', 'key');
        $profilePhotoUrl = $imageService->url(auth()->user()->photo, ImageType::Profile);
        $storeLogoUrl = $imageService->url($settings['store_logo'] ?? null, ImageType::StoreLogo);

        return view('settings.index', compact('settings', 'profilePhotoUrl', 'storeLogoUrl'));
    }

    public function update(Request $request, ImageService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'required|string|max:255',
            'store_phone' => 'required|string|max:20',
            'receipt_footer' => 'nullable|string|max:150',
            'store_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:max_width=6000,max_height=6000',
            'image_type' => 'required_with:store_logo|in:'.ImageType::StoreLogo->value,
        ]);

        $logoUpload = $validated['store_logo'] ?? null;
        unset($validated['store_logo'], $validated['image_type']);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($logoUpload !== null) {
            $oldLogo = Setting::where('key', 'store_logo')->value('value');
            $newLogo = $imageService->store($logoUpload, ImageType::StoreLogo);

            Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $newLogo]);
            $imageService->delete($oldLogo, ImageType::StoreLogo);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
