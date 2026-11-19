<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ImageService
{
    public function storeProductImage(UploadedFile $image): string
    {
        $path = $image->store('products', 'public');

        if ($path === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        return $path;
    }

    public function deleteProductImage(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('Gambar produk gagal dihapus.');
        }
    }
}
