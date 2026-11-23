<?php

namespace Tests\Unit;

use App\Enums\ImageType;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    public function test_it_compresses_jpeg_images_and_stores_a_string_path(): void
    {
        Storage::fake('public');

        $path = app(ImageService::class)->store(
            UploadedFile::fake()->image('photo.jpg', 2400, 1000),
            ImageType::Profile
        );

        $this->assertStringStartsWith('profiles/', $path);
        $this->assertSame('jpg', pathinfo($path, PATHINFO_EXTENSION));
        $this->assertLessThanOrEqual(2000, getimagesize(Storage::disk('public')->path($path))[0]);
    }

    public function test_it_preserves_transparency_as_png_and_deletes_by_asset_type(): void
    {
        Storage::fake('public');

        $path = app(ImageService::class)->store($this->transparentPng(), ImageType::StoreLogo);

        $this->assertStringStartsWith('settings/', $path);
        $this->assertSame('png', pathinfo($path, PATHINFO_EXTENSION));

        $image = imagecreatefromstring(Storage::disk('public')->get($path));
        $this->assertSame(127, (imagecolorat($image, 0, 0) >> 24) & 0x7F);
        imagedestroy($image);

        app(ImageService::class)->delete($path, ImageType::StoreLogo);
        Storage::disk('public')->assertMissing($path);
    }

    private function transparentPng(): UploadedFile
    {
        $image = imagecreatetruecolor(2, 2);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));

        ob_start();
        imagepng($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return UploadedFile::fake()->createWithContent('transparent.png', $contents);
    }
}
