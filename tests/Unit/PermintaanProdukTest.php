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
    public function per_02_permintaan_produk_berhasil_disimpan()
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
    public function per_03_gagal_jika_user_bukan_admin_cabang()
    {
        $user = User::factory()->create();

        $produk = Produk::factory()->create([
            'kategori_idkategori' => Kategori::create([
                'nama_kategori' => 'Tenda'
            ])->idkategori
        ]);

        $response = $this->actingAs($user)->post(route('permintaan_produk.store'), [
            'produk_id' => [$produk->idproduk],
            'jumlah_diminta' => [2]
        ]);

        $response->assertSessionHas('error', 'Akun ini bukan admin cabang.');
    }

    /** @test */
    public function per_04_validasi_store_wajib_diisi()
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

        $response = $this->actingAs($user)->post(route('permintaan_produk.store'), []);

        $response->assertSessionHasErrors([
            'produk_id',
            'jumlah_diminta'
        ]);
    }

    /** @test */
    public function per_05_riwayat_hanya_bisa_diakses_admin_cabang()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('data_permintaan'));

        $response->assertStatus(403);
    }

    /** @test */
    public function per_06_riwayat_menampilkan_data_permintaan()
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
}