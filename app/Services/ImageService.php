<?php

namespace App\Services;

use App\Enums\ImageType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;
use RuntimeException;

class ImageService
{
    public function store(UploadedFile $upload, ImageType $type): string
    {
        $imageInfo = getimagesize($upload->getRealPath());
        if ($imageInfo === false) {
            throw new RuntimeException('File yang diunggah bukan gambar yang valid.');
        }

        $hasAlphaChannel = $this->hasTransparency($upload, $imageInfo);
        $image = (new ImageManager(new GdDriver))->decodePath($upload->getRealPath());
        $image->scaleDown(width: 2000, height: 2000);
        $extension = $hasAlphaChannel ? 'png' : 'jpg';
        $encodedImage = $hasAlphaChannel
            ? $image->encode(new PngEncoder)
            : $image->encode(new JpegEncoder(quality: 82, strip: true));

        $path = $type->directory().'/'.str()->uuid().'.'.$extension;
        if (! Storage::disk('public')->put($path, $encodedImage->toString())) {
            throw new RuntimeException('Gambar gagal disimpan.');
        }

        return $path;
    }

    /**
     * @param  array<string, mixed>  $imageInfo
     */
    private function hasTransparency(UploadedFile $upload, array $imageInfo): bool
    {
        if (($imageInfo['mime'] ?? null) === 'image/jpeg') {
            return false;
        }

        if (($imageInfo['channels'] ?? 3) >= 4) {
            return true;
        }

        $image = imagecreatefromstring($upload->get());
        if ($image === false) {
            throw new RuntimeException('Gambar tidak dapat dibaca untuk pemeriksaan transparansi.');
        }

        try {
            $transparentColor = imagecolortransparent($image);
            if ($transparentColor >= 0) {
                return true;
            }

            for ($y = 0, $height = imagesy($image); $y < $height; $y++) {
                for ($x = 0, $width = imagesx($image); $x < $width; $x++) {
                    $color = imagecolorat($image, $x, $y);
                    $alpha = imageistruecolor($image)
                        ? ($color >> 24) & 0x7F
                        : imagecolorsforindex($image, $color)['alpha'];

                    if ($alpha > 0) {
                        return true;
                    }
                }
            }

            return false;
        } finally {
            imagedestroy($image);
        }
    }

    public function delete(?string $path, ImageType $type): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (str_contains($path, '/')) {
            if (! str_starts_with($path, $type->directory().'/')) {
                throw new RuntimeException('Path gambar tidak sesuai dengan tipe aset.');
            }

            if (! Storage::disk('public')->delete($path)) {
                throw new RuntimeException('Gambar gagal dihapus.');
            }

            return;
        }

        $legacyPath = public_path($type->legacyDirectory().DIRECTORY_SEPARATOR.basename($path));
        if (File::exists($legacyPath) && ! File::delete($legacyPath)) {
            throw new RuntimeException('Gambar lama gagal dihapus.');
        }
    }

    public function url(?string $path, ImageType $type): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_contains($path, '/')) {
            return Storage::disk('public')->exists($path)
                ? Storage::disk('public')->url($path)
                : null;
        }

        $legacyPath = public_path($type->legacyDirectory().DIRECTORY_SEPARATOR.basename($path));

        return File::exists($legacyPath)
            ? asset($type->legacyDirectory().'/'.rawurlencode(basename($path)))
            : null;
    }

    public function storeProductImage(UploadedFile $image): string
    {
        return $this->store($image, ImageType::Product);
    }

    public function deleteProductImage(?string $path): void
    {
        $this->delete($path, ImageType::Product);
    }
}
