<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Makanan', 'Minuman', 'Alat Tulis', 'Sembako', 'Elektronik'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat, 'description' => "Kategori $cat"]);
        }
    }
}