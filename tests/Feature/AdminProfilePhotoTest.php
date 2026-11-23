<?php

namespace Tests\Feature;

use App\Enums\ImageType;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_find_profile_photo_and_store_logo_upload_controls_on_settings_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('settings.index'));

        $response->assertOk()
            ->assertSee('profile-photo-form')
            ->assertSee(route('profile.photo'), false)
            ->assertSee('store-logo-preview');
    }

    public function test_admin_can_upload_profile_photo_and_save_it_to_public_storage(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->from(route('settings.index'))
            ->post(route('profile.photo'), [
                'image_type' => ImageType::Profile->value,
                'photo' => UploadedFile::fake()->image('admin.png', 400, 400),
            ]);

        $response->assertRedirect(route('settings.index'))
            ->assertSessionHas('success', 'Foto profil berhasil diperbarui!');

        $photoPath = $admin->fresh()->photo;
        $this->assertIsString($photoPath);
        $this->assertStringStartsWith('profiles/', $photoPath);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'photo' => $photoPath]);
        Storage::disk('public')->assertExists($photoPath);
    }

    public function test_dashboard_displays_uploaded_admin_photo_and_store_logo_in_navigation(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $profilePhotoPath = 'profiles/admin-photo.png';
        $storeLogoPath = 'settings/store-logo.png';
        Storage::disk('public')->put($profilePhotoPath, 'profile image');
        Storage::disk('public')->put($storeLogoPath, 'store logo');
        $admin->forceFill(['photo' => $profilePhotoPath])->save();
        Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $storeLogoPath]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('navbar-profile-photo')
            ->assertSee(Storage::disk('public')->url($profilePhotoPath), false)
            ->assertSee('sidebar-store-logo')
            ->assertSee(Storage::disk('public')->url($storeLogoPath), false);
    }

    public function test_invalid_profile_photo_keeps_the_existing_photo_and_file(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $existingPhotoPath = 'profiles/existing.jpg';
        Storage::disk('public')->put($existingPhotoPath, 'existing image');
        $admin->forceFill(['photo' => $existingPhotoPath])->save();

        $response = $this->actingAs($admin)
            ->from(route('settings.index'))
            ->post(route('profile.photo'), [
                'image_type' => ImageType::Profile->value,
                'photo' => UploadedFile::fake()->create('admin.svg', 100, 'image/svg+xml'),
            ]);

        $response->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors('photo');

        $this->assertSame($existingPhotoPath, $admin->fresh()->photo);
        Storage::disk('public')->assertExists($existingPhotoPath);
        $this->assertSame([$existingPhotoPath], Storage::disk('public')->allFiles());
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@profile-photo.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
