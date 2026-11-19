<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Toko Berkah Jaya'],
            ['key' => 'store_address', 'value' => 'Jl. Merdeka No. 123, Jakarta'],
            ['key' => 'store_phone', 'value' => '081234567890'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja!'],
        ];

        foreach ($settings as $setting) {
            // Pake updateOrCreate biar ga error kalau data udah ada
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}