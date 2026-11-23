<?php

namespace Tests\Feature;

use App\Enums\ImageType;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_store_logo_and_print_it_on_receipts(): void
    {
        Storage::fake('public');
        $admin = $this->createUser('admin');

        $response = $this->actingAs($admin)->put('/settings', [
            'store_name' => 'Toko Logo',
            'store_address' => 'Jl. Merdeka',
            'store_phone' => '08123456789',
            'receipt_footer' => 'Terima kasih',
            'image_type' => ImageType::StoreLogo->value,
            'store_logo' => UploadedFile::fake()->image('logo.png', 400, 200),
        ]);

        $response->assertRedirect(route('settings.index'));
        $logoPath = Setting::get('store_logo');
        $this->assertNotNull($logoPath);
        Storage::disk('public')->assertExists($logoPath);

        $this->actingAs($admin)
            ->get('/settings')
            ->assertOk()
            ->assertSee('store-logo-preview');

        $sale = Sale::create([
            'invoice_number' => 'INV-LOGO-001',
            'user_id' => $admin->id,
            'sale_date' => now(),
            'subtotal' => 1000,
            'discount' => 0,
            'total' => 1000,
            'payment_method' => 'tunai',
            'paid_amount' => 1000,
            'change_amount' => 0,
            'status' => 'paid',
        ]);

        $this->actingAs($admin)
            ->get('/sales/'.$sale->id)
            ->assertOk()
            ->assertSee('receipt-logo')
            ->assertSee(Storage::disk('public')->url($logoPath), false);
    }

    public function test_replacing_store_logo_deletes_the_previous_image(): void
    {
        Storage::fake('public');
        $admin = $this->createUser('admin');
        $imageService = app(ImageService::class);
        $oldLogoPath = $imageService->store(
            UploadedFile::fake()->image('old-logo.png'),
            ImageType::StoreLogo
        );
        Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $oldLogoPath]);

        $response = $this->actingAs($admin)->put('/settings', [
            'store_name' => 'Toko Logo',
            'store_address' => 'Jl. Merdeka',
            'store_phone' => '08123456789',
            'receipt_footer' => 'Terima kasih',
            'image_type' => ImageType::StoreLogo->value,
            'store_logo' => UploadedFile::fake()->image('new-logo.png'),
        ]);

        $response->assertRedirect(route('settings.index'));
        Storage::disk('public')->assertMissing($oldLogoPath);
        Storage::disk('public')->assertExists(Setting::get('store_logo'));
    }

    public function test_invalid_store_logo_is_rejected_without_saving_settings(): void
    {
        Storage::fake('public');
        $admin = $this->createUser('admin');

        $response = $this->actingAs($admin)->put('/settings', [
            'store_name' => 'Toko Logo',
            'store_address' => 'Jl. Merdeka',
            'store_phone' => '08123456789',
            'receipt_footer' => 'Terima kasih',
            'image_type' => ImageType::StoreLogo->value,
            'store_logo' => UploadedFile::fake()->create('logo.svg', 100, 'image/svg+xml'),
        ]);

        $response->assertSessionHasErrors('store_logo');
        $this->assertNull(Setting::get('store_logo'));
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'@settings-logo.test',
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
