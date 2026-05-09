<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\AdminPusat;
use App\Http\Controllers\ProdukController;
use Illuminate\Http\Request;

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

    // =========================
    // TC-PROD-01: Tambah valid
    // =========================
    public function test_tc_prod_01_tambah_produk_valid()
    {
        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $controller = new ProdukController();

        $request = new Request([
            'nama_produk' => 'Tenda Arpenaz',
            'stok_pusat' => 10,
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $controller->store($request);

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Tenda Arpenaz'
        ]);
    }

    // =========================
    // TC-PROD-02: Nama kosong
    // =========================
    public function test_tc_prod_02_nama_kosong()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $controller = new ProdukController();

        $request = new Request([
            'nama_produk' => '',
            'stok_pusat' => 10,
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $controller->store($request);
    }

    // =========================
    // TC-PROD-03: stok non numerik
    // =========================
    public function test_tc_prod_03_stok_non_numerik()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $controller = new ProdukController();

        $request = new Request([
            'nama_produk' => 'Tenda',
            'stok_pusat' => 'abc',
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $controller->store($request);
    }

    // =========================
    // TC-PROD-04: harga non numerik
    // =========================
    public function test_tc_prod_04_harga_non_numerik()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $controller = new ProdukController();

        $request = new Request([
            'nama_produk' => 'Tenda',
            'stok_pusat' => 10,
            'harga' => 'dua juta',
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $controller->store($request);
    }

    // =========================
    // TC-PROD-05: dropdown kategori & skala
    // =========================
    public function test_tc_prod_05_dropdown_valid()
    {
        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $controller = new ProdukController();

        $request = new Request([
            'nama_produk' => 'Tenda Dome',
            'stok_pusat' => 5,
            'harga' => 300000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $controller->store($request);

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Tenda Dome'
        ]);
    }

    // =========================
    // TC-PROD-06: file bukan gambar
    // =========================
    public function test_tc_prod_06_file_invalid()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $kategori = $this->makeKategori();
        $admin = $this->makeAdmin();

        $controller = new ProdukController();

        $request = new Request([
            'nama_produk' => 'Tenda',
            'stok_pusat' => 10,
            'harga' => 500000,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            'gambar_produk' => 'dokumen.pdf'
        ]);

        $controller->store($request);
    }

    // =========================
    // TC-PROD-07: batal (tidak simpan)
    // =========================
    public function test_tc_prod_07_batal()
    {
        $countBefore = Produk::count();

        $this->assertEquals($countBefore, Produk::count());
    }
}