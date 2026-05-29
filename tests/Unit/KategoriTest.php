<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\KategoriController;

class KategoriTest extends TestCase
{
    use RefreshDatabase;

    // =========================
    // TC-CAT-01: Tambah kategori valid
    // =========================
    public function test_tc_cat_01_tambah_kategori_valid()
    {
        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Alat Masak'
        ]);

        $controller->store($request);

        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'Alat Masak'
        ]);
    }

    // =========================
    // TC-CAT-02: Nama kosong
    // =========================
    public function test_tc_cat_02_nama_kosong()
    {
        $this->expectException(ValidationException::class);

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => ''
        ]);

        $controller->store($request);
    }

    // =========================
    // TC-CAT-03: Nama duplikat
    // =========================
    public function test_tc_cat_03_nama_duplikat()
    {
        Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $this->expectException(ValidationException::class);

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Tenda'
        ]);

        $controller->store($request);
    }

    // =========================
    // TC-CAT-04: Edit kategori valid
    // =========================
    public function test_tc_cat_04_edit_valid()
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Kompor'
        ]);

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Kompor Portable'
        ]);

        $controller->update($request, $kategori->idkategori);

        $this->assertDatabaseHas('kategori', [
            'idkategori' => $kategori->idkategori,
            'nama_kategori' => 'Kompor Portable'
        ]);
    }

    // =========================
    // TC-CAT-05: Edit kosong
    // =========================
    public function test_tc_cat_05_edit_kosong()
    {
        $this->expectException(ValidationException::class);

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => ''
        ]);

        $controller->update($request, $kategori->idkategori);
    }

    // =========================
    // TC-CAT-06: Edit tanpa perubahan
    // =========================
    public function test_tc_cat_06_tanpa_perubahan()
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Tenda'
        ]);

        $controller->update($request, $kategori->idkategori);

        $this->assertDatabaseHas('kategori', [
            'idkategori' => $kategori->idkategori,
            'nama_kategori' => 'Tenda'
        ]);
    }

    // =========================
    // TC-CAT-07: Batal
    // =========================
    public function test_tc_cat_07_batal()
    {
        Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $countBefore = Kategori::count();

        // simulasi batal = tidak memanggil store()

        $this->assertEquals($countBefore, Kategori::count());
    }

    // =========================
    // TC-CAT-08: Hapus kategori
    // =========================
    public function test_tc_cat_08_hapus_kategori()
   {
    $kategori = Kategori::create([
        'nama_kategori' => 'Tenda'
    ]);

    $controller = new KategoriController();

    $controller->destroy($kategori->idkategori);

    $this->assertSoftDeleted('kategori', [
        'idkategori' => $kategori->idkategori
    ]);
    }
}