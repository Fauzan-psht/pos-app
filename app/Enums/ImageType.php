<?php

namespace App\Enums;

enum ImageType: string
{
    case Product = 'product';
    case Profile = 'profile';
    case StoreLogo = 'store_logo';

    public function directory(): string
    {
        return match ($this) {
            self::Product => 'products',
            self::Profile => 'profiles',
            self::StoreLogo => 'settings',
        };
    }

    public function legacyDirectory(): string
    {
        return match ($this) {
            self::Product => 'storage/products',
            self::Profile => 'images/profiles',
            self::StoreLogo => 'images/settings',
        };
    }
}
