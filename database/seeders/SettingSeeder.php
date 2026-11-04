<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Toko Berkah Jaya', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Merdeka No. 123, Jakarta', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '081234567890', 'group' => 'store'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja!', 'group' => 'receipt'],
        ];

        foreach ($settings as $s) {
            Setting::create($s);
        }
    }
}