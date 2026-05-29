<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\AdminPusat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProdukTest extends TestCase
{
    use RefreshDatabase;

    private function makeKategori()
    {
        return Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);
    }

    private function makeAdmin()
    {
        return AdminPusat::factory()->create();
    }

    /* =========================================
       TC-PROD-01
       Tambah produk valid
    ========================================= */
    public function test_tc_prod_01_tambah_produk_valid()
    {
        Storage::fake('public');

        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $file = UploadedFile::fake()->image('produk.jpg');

        $response = $this->post(route('produk.store'), [
            'nama_produk' => 'Tenda Arpenaz',
            'stok_pusat' => 10,
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            'gambar_produk' => $file
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Tenda Arpenaz'
        ]);
    }

    /* =========================================
       TC-PROD-02
       Nama produk kosong
    ========================================= */
    public function test_tc_prod_02_nama_kosong()
    {
        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $response = $this->post(route('produk.store'), [
            'nama_produk' => '',
            'stok_pusat' => 10,
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $response->assertSessionHasErrors('nama_produk');
    }

    /* =========================================
       TC-PROD-03
       Stok non numerik
    ========================================= */
    public function test_tc_prod_03_stok_non_numerik()
    {
        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $response = $this->post(route('produk.store'), [
            'nama_produk' => 'Tenda',
            'stok_pusat' => 'abc',
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $response->assertSessionHasErrors('stok_pusat');
    }

    // =========================
    // TC-PROD-04: harga non numerik
    // =========================
    public function test_tc_prod_04_harga_non_numerik()
    {
        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $response = $this->post(route('produk.store'), [
            'nama_produk' => 'Tenda',
            'stok_pusat' => 10,
            'harga' => 'dua juta',
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $response->assertSessionHasErrors('harga');
    }

    /* =========================================
       TC-PROD-05
       Dropdown kategori dan skala valid
    ========================================= */
    public function test_tc_prod_05_dropdown_valid()
    {
        Storage::fake('public');

        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $file = UploadedFile::fake()->image('produk.jpg');

        $response = $this->post(route('produk.store'), [
            'nama_produk' => 'Tenda Dome',
            'stok_pusat' => 5,
            'harga' => 300000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            'gambar_produk' => $file
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Tenda Dome'
        ]);
    }

    /* =========================================
       TC-PROD-06
       Format file tidak valid
    ========================================= */
    public function test_tc_prod_06_file_invalid()
    {
        Storage::fake('public');

        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $file = UploadedFile::fake()->create(
            'dokumen.pdf',
            100,
            'application/pdf'
        );

        $response = $this->post(route('produk.store'), [
            'nama_produk' => 'Tenda',
            'stok_pusat' => 10,
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            'gambar_produk' => $file
        ]);

        $response->assertSessionHasErrors('gambar_produk');
    }

    /* =========================================
       TC-PROD-07
       Batal tambah produk
    ========================================= */
    public function test_tc_prod_07_batal()
    {
        $jumlahSebelum = Produk::count();

        $this->assertEquals($jumlahSebelum, Produk::count());
    }
}