<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase; // Ini akan mereset database setiap kali test jalan

    // Helper untuk membuat user
    private function createUser($role)
    {
        return User::create([
            'name' => 'User '.$role,
            'email' => $role.'@test.com',
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }

    // Helper untuk membuat produk
    private function createProduct($stock = 10)
    {
        $category = Category::create(['name' => 'Test Kategori']);

        return Product::create([
            'category_id' => $category->id,
            'sku' => 'TEST-'.rand(1, 1000),
            'name' => 'Produk Test',
            'unit' => 'Pcs',
            'purchase_price' => 5000,
            'selling_price' => 10000,
            'stock' => $stock,
            'min_stock' => 2,
            'is_active' => true,
        ]);
    }

    // 1. Tamu yang membuka /products diarahkan ke halaman login.
    public function test_tamu_diarahkan_ke_login()
    {
        $response = $this->get('/products');
        $response->assertRedirect('/login');
    }

    // 2. Kasir yang membuka /users mendapat 403.
    public function test_kasir_tidak_bisa_akses_halaman_users()
    {
        $kasir = $this->createUser('kasir');
        $response = $this->actingAs($kasir)->get('/users');
        $response->assertStatus(403);
    }

    // 3. Admin bisa menambah kategori; nama kategori duplikat ditolak.
    public function test_admin_bisa_tambah_kategori_dan_duplikat_ditolak()
    {
        $admin = $this->createUser('admin');

        // Tambah kategori berhasil
        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Kategori Baru',
            'description' => 'Deskripsi',
        ]);
        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Kategori Baru']);

        // Duplikat ditolak
        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Kategori Baru',
            'description' => 'Deskripsi 2',
        ]);
        $response->assertSessionHasErrors('name');
        $this->assertEquals(1, Category::where('name', 'Kategori Baru')->count());
    }

    // 4. Kategori yang masih punya produk tidak bisa dihapus.
    public function test_kategori_yang_punya_produk_tidak_bisa_dihapus()
    {
        $admin = $this->createUser('admin');
        $product = $this->createProduct();
        $category = $product->category;

        $response = $this->actingAs($admin)->delete('/categories/'.$category->id);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    // 5. Stok masuk menambah stok produk sesuai jumlah.
    public function test_stok_masuk_menambah_stok_produk()
    {
        $admin = $this->createUser('admin');
        $supplier = Supplier::create(['name' => 'Supplier Test', 'phone' => '08123456789']);
        $product = $this->createProduct(10); // Stok awal 10

        $response = $this->actingAs($admin)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'purchase_date' => date('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'purchase_price' => 4000,
                ],
            ],
        ]);

        $response->assertRedirect('/purchases');
        $this->assertEquals(15, $product->fresh()->stock); // 10 + 5
    }

    public function test_stok_masuk_menerima_jumlah_pecahan()
    {
        $admin = $this->createUser('admin');
        $supplier = Supplier::create(['name' => 'Supplier Test', 'phone' => '08123456789']);
        $product = $this->createProduct(1.25);

        $response = $this->actingAs($admin)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'purchase_date' => date('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 0.75,
                    'purchase_price' => 4000,
                ],
            ],
        ]);

        $response->assertRedirect('/purchases');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);
        $this->assertDatabaseHas('purchase_items', [
            'product_id' => $product->id,
            'quantity' => 0.75,
        ]);
    }

    // 6. Transaksi penjualan mengurangi stok dan menyimpan total yang benar.
    public function test_transaksi_penjualan_mengurangi_stok_dan_total_benar()
    {
        $kasir = $this->createUser('kasir');
        $product = $this->createProduct(10); // Stok 10, harga 10000

        $response = $this->actingAs($kasir)->post('/sales', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2], // Beli 2
            ],
            'discount' => 0,
            'payment_method' => 'tunai',
            'paid_amount' => 20000,
        ]);

        $sale = Sale::first();
        $response->assertRedirect('/sales/'.$sale->id);

        // Cek total (2 * 10000 = 20000)
        $this->assertEquals(20000, $sale->total);
        // Cek stok berkurang (10 - 2 = 8)
        $this->assertEquals(8, $product->fresh()->stock);
    }

    public function test_penjualan_menerima_jumlah_pecahan_dan_mengurangi_stok()
    {
        $kasir = $this->createUser('kasir');
        $product = $this->createProduct(2.5);

        $response = $this->actingAs($kasir)->post('/sales', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 0.75],
            ],
            'discount' => 0,
            'payment_method' => 'tunai',
            'paid_amount' => 7500,
        ]);

        $sale = Sale::first();
        $response->assertRedirect('/sales/'.$sale->id);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1.75,
        ]);
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'quantity' => 0.75,
        ]);
    }

    public function test_form_penjualan_menyediakan_kontrol_naik_turun_kuantitas()
    {
        $kasir = $this->createUser('kasir');
        $this->createProduct(1.5);

        $response = $this->actingAs($kasir)->get('/sales/create');

        $response->assertOk()
            ->assertSee('qty-stepper')
            ->assertSee('step="0.001"', false);
    }

    public function test_kasir_melihat_stok_produk_tanpa_nol_desimal_tidak_terpakai()
    {
        $kasir = $this->createUser('kasir');
        $this->createProduct(50);

        $response = $this->actingAs($kasir)->get('/sales/create');

        $response->assertOk()
            ->assertSee('Stok: 50</small>', false)
            ->assertDontSee('Stok: 50.000', false);
    }

    public function test_form_produk_menggunakan_dropdown_satuan()
    {
        $admin = $this->createUser('admin');

        $response = $this->actingAs($admin)->get('/products/create');

        $response->assertOk()
            ->assertSee('<select name="unit"', false)
            ->assertSee('value="Botol"', false)
            ->assertSee('value="pcs"', false);
    }

    public function test_harga_produk_ditampilkan_dan_disimpan_tanpa_mengalikan_desimal()
    {
        $admin = $this->createUser('admin');
        $product = $this->createProduct();

        $response = $this->actingAs($admin)->get('/products');

        $response->assertOk()
            ->assertSee('value="5.000"', false)
            ->assertSee('value="2"', false)
            ->assertDontSee('value="500.000"', false);

        $response = $this->actingAs($admin)->get('/products/'.$product->id.'/edit');

        $response->assertOk()
            ->assertSee('value="5.000"', false)
            ->assertSee('value="10"', false)
            ->assertSee('value="2"', false)
            ->assertDontSee('value="2.000"', false);

        $response = $this->actingAs($admin)->put('/products/'.$product->id, [
            'category_id' => $product->category_id,
            'sku' => $product->sku,
            'name' => $product->name,
            'unit' => $product->unit,
            'purchase_price' => '50.000',
            'selling_price' => '60.000',
            'min_stock' => '2',
        ]);

        $response->assertRedirect('/products');
        $this->assertEquals(50000, (float) $product->fresh()->purchase_price);
        $this->assertEquals(60000, (float) $product->fresh()->selling_price);
    }

    public function test_tabel_produk_menampilkan_batas_minimum_dan_peringatan_stok_rendah()
    {
        $admin = $this->createUser('admin');
        $this->createProduct(1.5);

        $response = $this->actingAs($admin)->get('/products');

        $response->assertOk()
            ->assertSee('Stok Minimum')
            ->assertSee('1.5 Pcs')
            ->assertSee('2')
            ->assertSee('Stok Menipis');
    }

    public function test_upload_gambar_produk_menggunakan_image_service()
    {
        Storage::fake('public');
        $admin = $this->createUser('admin');
        $category = Category::create(['name' => 'Kategori Gambar']);

        $response = $this->actingAs($admin)->post('/products', [
            'category_id' => $category->id,
            'sku' => 'IMG-TEST',
            'name' => 'Produk Gambar',
            'unit' => 'pcs',
            'purchase_price' => '5000',
            'selling_price' => '10000',
            'stock' => '1.25',
            'min_stock' => '0.5',
            'image' => UploadedFile::fake()->create('product.png', 10, 'image/png'),
        ]);

        $response->assertRedirect('/products');
        $product = Product::where('sku', 'IMG-TEST')->firstOrFail();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1.25,
        ]);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_memperbarui_gambar_produk_menghapus_gambar_lama()
    {
        Storage::fake('public');
        $admin = $this->createUser('admin');
        $category = Category::create(['name' => 'Kategori Gambar']);
        $oldImage = 'products/old-image.png';
        Storage::disk('public')->put($oldImage, 'old image');
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'IMG-UPDATE',
            'name' => 'Produk Gambar',
            'unit' => 'pcs',
            'purchase_price' => 5000,
            'selling_price' => 10000,
            'stock' => 1,
            'min_stock' => 0,
            'image' => $oldImage,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put('/products/'.$product->id, [
            'category_id' => $category->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'unit' => $product->unit,
            'purchase_price' => '5000',
            'selling_price' => '10000',
            'min_stock' => '0.5',
            'image' => UploadedFile::fake()->create('new-product.png', 10, 'image/png'),
        ]);

        $response->assertRedirect('/products');
        $updatedProduct = $product->fresh();
        Storage::disk('public')->assertMissing($oldImage);
        $this->assertNotSame($oldImage, $updatedProduct->image);
        Storage::disk('public')->assertExists($updatedProduct->image);
    }

    // 7. Transaksi dengan jumlah melebihi stok ditolak dan stok tidak berubah.
    public function test_transaksi_melebihi_stok_ditolak()
    {
        $kasir = $this->createUser('kasir');
        $product = $this->createProduct(3); // Stok cuma 3

        $response = $this->actingAs($kasir)->post('/sales', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5], // Beli 5
            ],
            'payment_method' => 'tunai',
            'paid_amount' => 50000,
        ]);

        $response->assertSessionHasErrors('items.0.quantity'); // Harus error
        $this->assertEquals(3, $product->fresh()->stock); // Stok tetap 3
        $this->assertDatabaseCount('sales', 0); // Tidak ada transaksi tersimpan
    }

    // 8. Transaksi dengan uang bayar kurang ditolak.
    public function test_transaksi_uang_kurang_ditolak()
    {
        $kasir = $this->createUser('kasir');
        $product = $this->createProduct(10); // Harga 10000

        $response = $this->actingAs($kasir)->post('/sales', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2], // Total 20000
            ],
            'payment_method' => 'tunai',
            'paid_amount' => 15000, // Uang kurang
        ]);

        $response->assertSessionHasErrors('paid_amount');
        $this->assertDatabaseCount('sales', 0);
    }
}
