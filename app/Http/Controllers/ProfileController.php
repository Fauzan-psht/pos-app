<?php

namespace App\Http\Controllers;

use App\Enums\ImageType;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class ProfileController extends Controller
{
    // Update Nama, Email, Password
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Pake Query Builder biar pasti update
        DB::table('users')->where('id', $user->id)->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // KHUSUS UPLOAD FOTO PROFIL (BERSIH DARI DEBUGGING)
    public function updatePhoto(Request $request, ImageService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:max_width=6000,max_height=6000',
            'image_type' => 'required|in:'.ImageType::Profile->value,
        ]);

        $user = auth()->user();
        $oldPhoto = DB::table('users')->where('id', $user->id)->value('photo');
        $newPhoto = $imageService->store($validated['photo'], ImageType::Profile);

        DB::table('users')->where('id', $user->id)->update(['photo' => $newPhoto]);
        $imageService->delete($oldPhoto, ImageType::Profile);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    // Update Toko (Logo & Nama)
    public function updateStore(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Hanya Admin yang bisa mengubah logo toko.');
        }

        $request->validate([
            'store_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'store_name' => 'required|string|max:255',
        ]);

        Setting::set('store_name', $request->store_name);

        if ($request->hasFile('store_logo')) {
            try {
                $oldLogo = Setting::get('store_logo');
                if ($oldLogo && file_exists(public_path('images/settings/' . $oldLogo))) {
                    unlink(public_path('images/settings/' . $oldLogo));
                }

                $file = $request->file('store_logo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/settings'), $filename);
                Setting::set('store_logo', $filename);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal upload logo: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}