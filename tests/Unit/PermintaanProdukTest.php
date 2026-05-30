<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Produk;
use App\Models\AdminCabang;
use App\Models\Permintaan;
use App\Models\PermintaanProduk;
use App\Models\Cabang;
use App\Models\Kategori;
use PHPUnit\Framework\Attributes\Test;

class PermintaanProdukTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function per_01_admin_dapat_menampilkan_form_permintaan_produk()
    {
        $user = User::factory()->create();

        $cabang = Cabang::create([
            'nama_cabang' => 'Cabang Test',
            'status_cabang' => 'aktif',
            'lokasi' => 'Banyuwangi'
        ]);

        AdminCabang::create([
            'users_idusers' => $user->idusers,
            'cabang_idcabang' => $cabang->idcabang,
        ]);

        Produk::factory()->create([
            'kategori_idkategori' => Kategori::create([
                'nama_kategori' => 'Tenda'
            ])->idkategori
        ]);

        $this->actingAs($user)
            ->get(route('permintaan_produk.create'))
            ->assertStatus(200)
            ->assertViewIs('permintaan_alat')
            ->assertViewHas('produkList');
    }
 /** @test */

    
    /** @test */
    /** @test */
public function per_02_produk_dapat_ditambahkan_ke_permintaan()
{
    $user = User::factory()->create();

    $cabang = Cabang::create([
        'nama_cabang' => 'Cabang Test',
        'status_cabang' => 'aktif',
        'lokasi' => 'Banyuwangi'
    ]);

    AdminCabang::create([
        'users_idusers' => $user->idusers,
        'cabang_idcabang' => $cabang->idcabang,
    ]);

    $kategori = Kategori::factory()->create();

    $produk = Produk::factory()->create([
        'nama_produk' => 'Tenda 5 Orang',
        'kategori_idkategori' => $kategori->idkategori
    ]);

    $this->actingAs($user)->post(route('permintaan_produk.store'), [
        'produk_id' => [$produk->idproduk],
        'jumlah_diminta' => [5],
        'keterangan' => 'Butuh cepat'
    ]);

    $this->assertDatabaseHas('permintaan_produk', [
        'produk_idproduk' => $produk->idproduk,
        'jumlah_diminta' => 5
    ]);
}

    /** @test */
    public function per_03_permintaan_produk_berhasil_disimpan()
    {
        $user = User::factory()->create();

        $cabang = Cabang::create([
            'nama_cabang' => 'Cabang Test',
            'status_cabang' => 'aktif',
            'lokasi' => 'Banyuwangi'
        ]);

        AdminCabang::create([
            'users_idusers' => $user->idusers,
            'cabang_idcabang' => $cabang->idcabang,
        ]);

        $kategori = Kategori::factory()->create();

$produk = Produk::factory()->create([
    'kategori_idkategori' => $kategori->idkategori,
]);

        $response = $this->actingAs($user)->post(route('permintaan_produk.store'), [
            'produk_id' => [$produk->idproduk],
            'jumlah_diminta' => [5],
            'keterangan' => 'Butuh cepat'
        ]);

        $response->assertRedirect(route('data_permintaan'));

        $this->assertDatabaseHas('permintaan', [
            'cabang_idcabang' => $cabang->idcabang,
            'status' => 'menunggu',
            'keterangan' => 'Butuh cepat'
        ]);
    }
/** @test */
public function per_04_konfirmasi_permintaan_produk_berhasil()
{
    $user = User::factory()->create();

    $cabang = Cabang::create([
        'nama_cabang' => 'Cabang Test',
        'status_cabang' => 'aktif',
        'lokasi' => 'Banyuwangi'
    ]);

    AdminCabang::create([
        'users_idusers' => $user->idusers,
        'cabang_idcabang' => $cabang->idcabang,
    ]);

    $kategori = Kategori::create([
        'nama_kategori' => 'Tenda'
    ]);

    $produk = Produk::factory()->create([
        'kategori_idkategori' => $kategori->idkategori
    ]);

    $permintaan = Permintaan::factory()->create([
        'cabang_idcabang' => $cabang->idcabang,
        'status' => 'disetujui'
    ]);

    PermintaanProduk::factory()->create([
        'permintaan_id' => $permintaan->idpermintaan,
        'produk_idproduk' => $produk->idproduk,
        'jumlah_diminta' => 5
    ]);

    $response = $this->actingAs($user)
        ->get(route('data_permintaan'));

    $response->assertStatus(200);

    // memastikan status tampil di halaman
    $response->assertSee('Disetujui');

    // memastikan data benar di database
    $this->assertDatabaseHas('permintaan', [
        'idpermintaan' => $permintaan->idpermintaan,
        'status' => 'disetujui'
    ]);
}
    /** @test */
    public function per_05_riwayat_menampilkan_data_permintaan()
    {
        $user = User::factory()->create();

        $cabang = Cabang::create([
            'nama_cabang' => 'Cabang Test',
            'status_cabang' => 'aktif',
            'lokasi' => 'Banyuwangi'
        ]);

        AdminCabang::create([
            'users_idusers' => $user->idusers,
            'cabang_idcabang' => $cabang->idcabang,
        ]);

        $permintaan = Permintaan::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'status' => 'menunggu'
        ]);

        PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => Produk::factory()->create([
                'kategori_idkategori' => Kategori::create([
                    'nama_kategori' => 'Tenda'
                ])->idkategori
            ])->idproduk,
            'jumlah_diminta' => 3
        ]);

        $response = $this->actingAs($user)
            ->get(route('data_permintaan'));

        $response->assertStatus(200);
        $response->assertViewIs('data_permintaan');
        $response->assertViewHas('permintaan');
    }

  
#[Test]
public function tc_da_06_produk_tidak_dipilih()
{
    $user = User::factory()->create();

    $cabang = Cabang::create([
        'nama_cabang' => 'Cabang Test',
        'status_cabang' => 'aktif',
        'lokasi' => 'Banyuwangi'
    ]);

    AdminCabang::create([
        'users_idusers' => $user->idusers,
        'cabang_idcabang' => $cabang->idcabang,
    ]);

    $response = $this->actingAs($user)
        ->from(route('permintaan_produk.create'))
        ->post(route('permintaan_produk.store'), [
            'produk_id' => [], // kosong
            'jumlah_diminta' => [5],
            'keterangan' => 'Butuh cepat'
        ]);

    $response->assertSessionHasErrors('produk_id');
}
#[Test]
public function tc_da_07_jumlah_permintaan_kosong()
{
    $user = User::factory()->create();

    $cabang = Cabang::create([
        'nama_cabang' => 'Cabang Test',
        'status_cabang' => 'aktif',
        'lokasi' => 'Banyuwangi'
    ]);

    AdminCabang::create([
        'users_idusers' => $user->idusers,
        'cabang_idcabang' => $cabang->idcabang,
    ]);

    $kategori = Kategori::factory()->create();

    $produk = Produk::factory()->create([
        'kategori_idkategori' => $kategori->idkategori
    ]);

    $response = $this->actingAs($user)
        ->from(route('permintaan_produk.create'))
        ->post(route('permintaan_produk.store'), [
            'produk_id' => [$produk->idproduk],
            'jumlah_diminta' => [''], // kosong
            'keterangan' => 'Butuh cepat'
        ]);

    $response->assertSessionHasErrors('jumlah_diminta.0');
}
}