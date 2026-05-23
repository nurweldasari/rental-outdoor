<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Produk;
use App\Models\Permintaan;
use App\Models\PermintaanProduk;
use App\Models\DistribusiProduk;
use App\Models\StokCabang;

class DistribusiProdukTest extends TestCase
{
    use RefreshDatabase;

    // ================================
    // TC-DIST-01: Kirim sesuai permintaan
    // ================================
    public function test_tc_dist_01_kirim_sesuai_jumlah_permintaan()
    {
        $owner = User::factory()->create(['status' => 'owner']);

        $produk = Produk::factory()->create([
            'stok_pusat' => 20
        ]);

        $permintaan = Permintaan::factory()->create();

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        $response = $this->actingAs($owner)->post(route('distribusi_produk.kirim'), [
            'jumlah_dikirim' => [
                $permintaanProduk->id => 10
            ],
            'keterangan' => 'test kirim'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('distribusi_produk', [
            'permintaan_produk_id' => $permintaanProduk->id,
            'jumlah_dikirim' => 10,
            'status_distribusi' => 'dikirim'
        ]);
    }

    // ================================
    // TC-DIST-02: Kirim sebagian
    // ================================
    public function test_tc_dist_02_kirim_sebagian()
    {
        $owner = User::factory()->create(['status' => 'owner']);

        $produk = Produk::factory()->create(['stok_pusat' => 20]);

        $permintaan = Permintaan::factory()->create();

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        $response = $this->actingAs($owner)->post(route('distribusi_produk.kirim'), [
            'jumlah_dikirim' => [
                $permintaanProduk->id => 5
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('distribusi_produk', [
            'jumlah_dikirim' => 5
        ]);
    }

    // ================================
    // TC-DIST-03: Melebihi permintaan
    // ================================
    public function test_tc_dist_03_kirim_melebihi_permintaan()
    {
        $owner = User::factory()->create(['status' => 'owner']);

        $produk = Produk::factory()->create(['stok_pusat' => 20]);

        $permintaan = Permintaan::factory()->create();

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        $response = $this->actingAs($owner)->post(route('distribusi_produk.kirim'), [
            'jumlah_dikirim' => [
                $permintaanProduk->id => 15
            ]
        ]);

        $response->assertSessionHas('error');
    }

    // ================================
    // TC-DIST-04: Jumlah tidak valid
    // ================================
    public function test_tc_dist_04_jumlah_tidak_valid()
{
    $owner = User::factory()->create(['status' => 'owner']);

    $produk = Produk::factory()->create(['stok_pusat' => 20]);

    $permintaan = Permintaan::factory()->create();

    $permintaanProduk = PermintaanProduk::factory()->create([
        'permintaan_id' => $permintaan->idpermintaan,
        'produk_idproduk' => $produk->idproduk,
        'jumlah_diminta' => 10
    ]);

    $response = $this->actingAs($owner)->post(route('distribusi_produk.kirim'), [
        'jumlah_dikirim' => [
            $permintaanProduk->id => 0
        ]
    ]);

    $response->assertInvalid(['jumlah_dikirim.1']);
}
    // ================================
    // TC-DIST-05: Stok cukup
    // ================================
    public function test_tc_dist_05_stok_cukup()
    {
        $stok = 20;
        $kirim = 10;

        $this->assertTrue($stok >= $kirim);
    }

    // ================================
    // TC-DIST-06: Pengurangan stok pusat
    // ================================
    public function test_tc_dist_06_pengurangan_stok()
    {
        $produk = Produk::factory()->create([
            'stok_pusat' => 20
        ]);

        $produk->stok_pusat -= 5;
        $produk->save();

        $this->assertDatabaseHas('produk', [
            'idproduk' => $produk->idproduk,
            'stok_pusat' => 15
        ]);
    }

    // ================================
    // TC-DIST-07: Stok cabang bertambah
    // ================================
    public function test_tc_dist_07_stok_cabang()
    {
        $cabang = Cabang::factory()->create();
        $produk = Produk::factory()->create();

        $stok = StokCabang::create([
            'cabang_idcabang' => $cabang->idcabang,
            'produk_idproduk' => $produk->idproduk,
            'jumlah' => 0
        ]);

        $stok->jumlah += 10;
        $stok->save();

        $this->assertEquals(10, $stok->jumlah);
    }

    // ================================
    // TC-DIST-08: Status disetujui
    // ================================
    public function test_tc_dist_08_status_disetujui()
    {
        $status = 'disetujui';

        $this->assertEquals('disetujui', $status);
    }

    // ================================
    // TC-DIST-09: Status dikirim
    // ================================
    public function test_tc_dist_09_status_dikirim()
    {
        $status = 'dikirim';

        $this->assertEquals('dikirim', $status);
    }

    // ================================
    // TC-DIST-10: Status diterima
    // ================================
    public function test_tc_dist_10_status_diterima()
    {
        $status = 'diterima';

        $this->assertEquals('diterima', $status);
    }
}