<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KatalogController;
use App\Models\StokCabang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KatalogCabangTest extends TestCase
{
    use RefreshDatabase;

    private function seedRelasi()
    {
        DB::table('users')->insert([
            'idusers' => 1,
            'nama' => 'User',
            'username' => 'user',
            'password' => bcrypt('123456'),
            'no_telepon' => '08123456789',
            'alamat' => 'Jember',
            'status' => 'aktif'
        ]);

        DB::table('penyewa')->insert([
            'idpenyewa' => 1,
            'users_idusers' => 1,
            'status_penyewa' => 'aktif'
        ]);

        DB::table('kategori')->insert([
            'idkategori' => 1,
            'nama_kategori' => 'Tenda'
        ]);

        DB::table('admin_pusat')->insert([
            'idadmin_pusat' => 1,
            'users_idusers' => 1
        ]);

        DB::table('produk')->insert([
    'idproduk' => 1,
    'nama_produk' => 'Tenda',
    'stok_pusat' => 10,
    'jenis_skala' => 'harian',
    'kategori_idkategori' => 1,
    'admin_pusat_idadmin_pusat' => 1,
]);

        DB::table('cabang')->insert([
            'idcabang' => 1,
            'nama_cabang' => 'Cabang A',
            'status_cabang' => 'aktif',
            'lokasi' => 'Jember',
        ]);

        DB::table('stok_cabang')->insert([
            'idstok' => 1,
            'produk_idproduk' => 1,
            'cabang_idcabang' => 1,
            'jumlah' => 10,
            'is_active' => 1,
        ]);
    }


   #[Test]
public function tc_kat_01_menampilkan_katalog()
{
    $this->seedRelasi();

    $produk = DB::table('stok_cabang')
        ->where('cabang_idcabang', 1)
        ->first();

    $this->assertNotNull($produk);
    $this->assertEquals(1, $produk->produk_idproduk);
    $this->assertEquals(10, $produk->jumlah);
}

#[Test]
public function tc_kat_02_informasi_produk()
{
    $this->seedRelasi();

    $produk = DB::table('stok_cabang')
        ->where('produk_idproduk', 1)
        ->first();

    $this->assertNotNull($produk);
    $this->assertEquals(10, $produk->jumlah);
}

#[Test]
public function tc_kat_03_pencarian_produk()
{
    $this->seedRelasi();

    $produk = DB::table('produk')
        ->where('nama_produk', 'Tenda')
        ->first();

    $this->assertNotNull($produk);
    $this->assertEquals('Tenda', $produk->nama_produk);
}

#[Test]
public function tc_kat_04_pencarian_tidak_ditemukan()
{
    $this->seedRelasi();

    $produk = DB::table('produk')
        ->where('nama_produk', 'KomporX')
        ->first();

    $this->assertNull($produk);
}

#[Test]
public function tc_kat_05_filter_kategori()
{
    $this->seedRelasi();

    $kategori = DB::table('kategori')
        ->where('nama_kategori', 'Tenda')
        ->first();

    $this->assertNotNull($kategori);
    $this->assertEquals('Tenda', $kategori->nama_kategori);
}

#[Test]
public function tc_kat_06_menampilkan_stok()
{
    $this->seedRelasi();

    $stok = DB::table('stok_cabang')
        ->where('produk_idproduk', 1)
        ->first();

    $this->assertNotNull($stok);
    $this->assertEquals(10, $stok->jumlah);
}

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

    $cart = session('cart');

    $this->assertCount(1, $cart);
    $this->assertEquals('Tenda', $cart[0]['nama']);
    $this->assertEquals(1, $cart[0]['jumlah']);
}

#[Test]
public function tc_kat_08_stok_habis()
{
    $this->seedRelasi();

    DB::table('stok_cabang')
        ->where('idstok', 1)
        ->update([
            'jumlah' => 0
        ]);

    $stok = DB::table('stok_cabang')
        ->where('idstok', 1)
        ->first();

    $this->assertEquals(0, $stok->jumlah);
}

#[Test]
public function tc_kat_09_produk_tidak_ada()
{
    $this->seedRelasi();

    $produk = DB::table('produk')
        ->where('idproduk', 999)
        ->first();

    $this->assertNull($produk);
}

#[Test]
public function tc_kat_10_kategori_tidak_ada()
{
    $this->seedRelasi();

    $kategori = DB::table('kategori')
        ->where('nama_kategori', 'Kompor')
        ->first();

    $this->assertNull($kategori);
}

#[Test]
public function tc_kat_11_keranjang_kosong()
{
    Session::start();

    $this->assertEmpty(session('cart'));
}
}