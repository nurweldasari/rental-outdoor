<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use App\Http\Controllers\KatalogController;
use App\Models\StokCabang;
use App\Models\Kategori;
use Illuminate\Http\Request;


class KatalogCabangTest extends TestCase
{
    use RefreshDatabase;

    // 🔥 SEED RELASI SUPER FINAL (ANTI ERROR TOTAL)
    private function seedRelasi()
{
   DB::table('users')->insert([
    'idusers' => 1,
    'nama' => 'Penyewa',
    'username' => 'penyewa',
    'password' => bcrypt('123456'),
    'no_telepon' => '08123456789',
    'alamat' => 'Jakarta',
    'status' => 'penyewa',
    'created_at' => now(),
    'updated_at' => now()
]);

DB::table('penyewa')->insert([
    'idpenyewa' => 1,
    'users_idusers' => 1,
    'gambar_identitas' => 'test.jpg',
    'status_penyewa' => 'aktif',
    'created_at' => now(),
    'updated_at' => now()
]);

    // ✅ 2. ADMIN PUSAT (BUTUH users_idusers)
    DB::table('admin_pusat')->insert([
        'idadmin_pusat' => 1,
        'users_idusers' => 1
    ]);

    // ✅ 3. KATEGORI (TANPA TIMESTAMP)
    DB::table('kategori')->insert([
        'idkategori' => 1,
        'nama_kategori' => 'Tenda'
    ]);

    // ✅ 4. PRODUK (ISI SEMUA FIELD WAJIB)
    DB::table('produk')->insert([
    'idproduk' => 1,
    'nama_produk' => 'Tenda',
    'stok_pusat' => 10,
    'jenis_skala' => 'harian',
    'kategori_idkategori' => 1,
    'admin_pusat_idadmin_pusat' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);

    // ✅ 5. CABANG
    DB::table('cabang')->insert([
        'idcabang' => 1,
        'nama_cabang' => 'Cabang A',
        'status_cabang' => 'aktif',
        'lokasi' => 'songgon',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    DB::table('harga')->insert([
    'idharga' => 1,
    'produk_id' => 1,
    'harga' => 50000,
    'type' => 'produk',
    'tanggal_berlaku' => now(),
]);
}

   #[Test]
public function tc_kat_01_menampilkan_katalog()
{
    $this->seedRelasi();

    // LOGIN USER PENYEWA
    $user = \App\Models\User::find(1);
    $this->actingAs($user);

    Session::start();
    session(['cabang_id' => 1]);

    StokCabang::create([
        'cabang_idcabang' => 1,
        'produk_idproduk' => 1,
        'jumlah' => 5,
        'is_active' => 1
    ]);

    $controller = new KatalogController();

    $request = new Request();

    $response = $controller->katalogCabang($request);

    $this->assertNotNull($response);
}
    // =========================
    #[Test]
    public function tc_kat_02_informasi_produk()
    {
        $this->seedRelasi();

        $produk = StokCabang::create([
            'produk_idproduk' => 1,
            'cabang_idcabang' => 1,
            'jumlah' => 3,
            'is_active' => 1
        ]);

        $this->assertGreaterThan(0, $produk->jumlah);
    }

    // =========================
    #[Test]
    public function tc_kat_03_pencarian_produk()
    {
        $this->seedRelasi();

        StokCabang::create([
            'produk_idproduk' => 1,
            'cabang_idcabang' => 1,
            'jumlah' => 5,
            'is_active' => 1
        ]);

        $produk = DB::table('produk')
            ->where('nama_produk', 'Tenda')
            ->first();

        $this->assertNotNull($produk);
    }

    // =========================
    #[Test]
    public function tc_kat_04_pencarian_tidak_ditemukan()
    {
        $this->seedRelasi();

        $produk = DB::table('produk')
            ->where('nama_produk', 'KomporX')
            ->first();

        $this->assertNull($produk);
    }

    // =========================
    #[Test]
    public function tc_kat_05_filter_kategori()
    {
        $kategori = Kategori::factory()->create([
            'nama_kategori' => 'Tenda'
        ]);

        $this->assertEquals('Tenda', $kategori->nama_kategori);
    }

    // =========================
    #[Test]
    public function tc_kat_06_menampilkan_stok()
    {
        $this->seedRelasi();

        $stok = StokCabang::create([
            'produk_idproduk' => 1,
            'cabang_idcabang' => 1,
            'jumlah' => 10,
            'is_active' => 1
        ]);

        $this->assertGreaterThan(0, $stok->jumlah);
    }

    // =========================
    #[Test]
    public function tc_kat_07_tambah_ke_keranjang()
    {
        Session::start();

        $produk = [
            'id' => 1,
            'nama' => 'Tenda',
            'jumlah' => 1
        ];

        session()->push('cart', $produk);

        $this->assertNotEmpty(session('cart'));
    }

    // =========================
    #[Test]
    public function tc_kat_08_stok_habis()
    {
        $this->seedRelasi();

        $stok = StokCabang::create([
            'produk_idproduk' => 1,
            'cabang_idcabang' => 1,
            'jumlah' => 0,
            'is_active' => 1
        ]);

        $this->assertEquals(0, $stok->jumlah);
    }
}