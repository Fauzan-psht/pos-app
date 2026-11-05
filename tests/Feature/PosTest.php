<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase; // Ini akan mereset database setiap kali test jalan

    // Helper untuk membuat user
    private function createUser($role)
    {
        return User::create([
            'name' => 'User ' . $role,
            'email' => $role . '@test.com',
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
            'sku' => 'TEST-' . rand(1, 1000),
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
            'description' => 'Deskripsi'
        ]);
        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Kategori Baru']);

        // Duplikat ditolak
        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Kategori Baru',
            'description' => 'Deskripsi 2'
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

        $response = $this->actingAs($admin)->delete('/categories/' . $category->id);
        
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
                    'purchase_price' => 4000
                ]
            ]
        ]);

        $response->assertRedirect('/purchases');
        $this->assertEquals(15, $product->fresh()->stock); // 10 + 5
    }

    // 6. Transaksi penjualan mengurangi stok dan menyimpan total yang benar.
    public function test_transaksi_penjualan_mengurangi_stok_dan_total_benar()
    {
        $kasir = $this->createUser('kasir');
        $product = $this->createProduct(10); // Stok 10, harga 10000

        $response = $this->actingAs($kasir)->post('/sales', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2] // Beli 2
            ],
            'discount' => 0,
            'payment_method' => 'tunai',
            'paid_amount' => 20000
        ]);

        $sale = Sale::first();
        $response->assertRedirect('/sales/' . $sale->id);
        
        // Cek total (2 * 10000 = 20000)
        $this->assertEquals(20000, $sale->total);
        // Cek stok berkurang (10 - 2 = 8)
        $this->assertEquals(8, $product->fresh()->stock);
    }

    // 7. Transaksi dengan jumlah melebihi stok ditolak dan stok tidak berubah.
    public function test_transaksi_melebihi_stok_ditolak()
    {
        $kasir = $this->createUser('kasir');
        $product = $this->createProduct(3); // Stok cuma 3

        $response = $this->actingAs($kasir)->post('/sales', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5] // Beli 5
            ],
            'payment_method' => 'tunai',
            'paid_amount' => 50000
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
                ['product_id' => $product->id, 'quantity' => 2] // Total 20000
            ],
            'payment_method' => 'tunai',
            'paid_amount' => 15000 // Uang kurang
        ]);

        $response->assertSessionHasErrors('paid_amount');
        $this->assertDatabaseCount('sales', 0);
    }
}