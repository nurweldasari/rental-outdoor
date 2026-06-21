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

    // =====================================================
    // HELPER OWNER
    // =====================================================
    private function owner()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'username' => 'owner1',
            'password' => bcrypt($password),
            'status' => 'owner'
        ]);

        return [$user, $password];
    }

    // =====================================================
    // HELPER LOGIN OWNER
    // =====================================================
    private function loginOwner($username, $password)
    {
        return $this->post('/login', [
            'username' => $username,
            'password' => $password
        ]);
    }

    // =====================================================
    // TC-DIST-01
    // Owner berhasil login
    // =====================================================
    public function test_tc_dist_01_owner_berhasil_login()
    {
        [$owner, $password] = $this->owner();

        $this->post('/login', [
            'username' => $owner->username,
            'password' => $password
        ]);

        $this->assertAuthenticated();

        $this->assertAuthenticatedAs($owner);
    }

    // =====================================================
    // TC-DIST-02
    // Owner dapat membuka halaman distribusi
    // =====================================================
    public function test_tc_dist_02_owner_dapat_membuka_halaman_distribusi()
    {
        [$owner, $password] = $this->owner();

        // LOGIN OWNER
        $this->loginOwner(
            $owner->username,
            $password
        );

        // AKSES HALAMAN
        $response = $this->get(
            route('distribusi_produk')
        );

        // CEK VIEW
        $response->assertViewIs(
            'distribusi_produk'
        );

        // CEK DATA VIEW
        $response->assertViewHas('permintaan');

        $response->assertViewHas('riwayat');

        // CEK MASIH LOGIN
        $this->assertAuthenticatedAs($owner);
    }

    // =====================================================
    // TC-DIST-03
    // Mengirim produk sesuai jumlah permintaan
    // =====================================================
    public function test_tc_dist_03_kirim_produk_sesuai_permintaan()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $produk = Produk::factory()->create([
            'stok_pusat' => 20
        ]);

        $permintaan = Permintaan::factory()->create([
            'status' => 'menunggu'
        ]);

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        // KIRIM PRODUK
        $this->post(
            route('distribusi_produk.kirim'),
            [
                'jumlah_dikirim' => [
                    $permintaanProduk->id => 10
                ],
                'keterangan' => 'test kirim'
            ]
        );

        // CEK DISTRIBUSI
        $this->assertDatabaseHas('distribusi_produk', [
            'permintaan_produk_id' => $permintaanProduk->id,
            'jumlah_dikirim' => 10,
            'status_distribusi' => 'dikirim'
        ]);

        // CEK STOK BERKURANG
        $this->assertDatabaseHas('produk', [
            'idproduk' => $produk->idproduk,
            'stok_pusat' => 10
        ]);

        // CEK STATUS PERMINTAAN
        $this->assertDatabaseHas('permintaan', [
            'idpermintaan' => $permintaan->idpermintaan,
            'status' => 'disetujui'
        ]);
    }

    // =====================================================
    // TC-DIST-04
    // Mengirim sebagian produk
    // =====================================================
    public function test_tc_dist_04_kirim_sebagian_produk()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $produk = Produk::factory()->create([
            'stok_pusat' => 20
        ]);

        $permintaan = Permintaan::factory()->create([
            'status' => 'menunggu'
        ]);

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        $this->post(
            route('distribusi_produk.kirim'),
            [
                'jumlah_dikirim' => [
                    $permintaanProduk->id => 5
                ]
            ]
        );

        $this->assertDatabaseHas('distribusi_produk', [
            'permintaan_produk_id' => $permintaanProduk->id,
            'jumlah_dikirim' => 5
        ]);

        // STOK BERKURANG 5
        $this->assertDatabaseHas('produk', [
            'idproduk' => $produk->idproduk,
            'stok_pusat' => 15
        ]);
    }

    // =====================================================
    // TC-DIST-05
    // Jumlah kirim melebihi permintaan
    // =====================================================
    public function test_tc_dist_05_jumlah_kirim_melebihi_permintaan()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $produk = Produk::factory()->create([
            'stok_pusat' => 20
        ]);

        $permintaan = Permintaan::factory()->create([
            'status' => 'menunggu'
        ]);

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        $response = $this->post(
            route('distribusi_produk.kirim'),
            [
                'jumlah_dikirim' => [
                    $permintaanProduk->id => 15
                ]
            ]
        );

        $response->assertSessionHas('error');

        // DISTRIBUSI TIDAK TERSIMPAN
        $this->assertDatabaseMissing('distribusi_produk', [
            'permintaan_produk_id' => $permintaanProduk->id,
            'jumlah_dikirim' => 15
        ]);
    }

    // =====================================================
    // TC-DIST-06
    // Jumlah kirim tidak valid
    // =====================================================
    public function test_tc_dist_06_jumlah_kirim_tidak_valid()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $produk = Produk::factory()->create([
            'stok_pusat' => 20
        ]);

        $permintaan = Permintaan::factory()->create([
            'status' => 'menunggu'
        ]);

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        $response = $this->post(
            route('distribusi_produk.kirim'),
            [
                'jumlah_dikirim' => [
                    $permintaanProduk->id => 0
                ]
            ]
        );

        $response->assertInvalid([
            'jumlah_dikirim.' . $permintaanProduk->id
        ]);

        // DATA TIDAK TERSIMPAN
        $this->assertDatabaseMissing('distribusi_produk', [
            'permintaan_produk_id' => $permintaanProduk->id
        ]);
    }

    // =====================================================
    // TC-DIST-07
    // Stok cabang bertambah saat distribusi diterima
    // =====================================================
    public function test_tc_dist_07_stok_cabang_bertambah()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $cabang = Cabang::factory()->create();

        $produk = Produk::factory()->create([
            'stok_pusat' => 20
        ]);

        $permintaan = Permintaan::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'status' => 'disetujui'
        ]);

        $permintaanProduk = PermintaanProduk::factory()->create([
            'permintaan_id' => $permintaan->idpermintaan,
            'produk_idproduk' => $produk->idproduk,
            'jumlah_diminta' => 10
        ]);

        DistribusiProduk::create([
            'permintaan_produk_id' => $permintaanProduk->id,
            'tanggal_distribusi' => now(),
            'jumlah_dikirim' => 10,
            'status_distribusi' => 'dikirim'
        ]);

        $this->post(
            route(
                'distribusi_produk.terima',
                $permintaan->idpermintaan
            )
        );

        // CEK STOK CABANG
        $this->assertDatabaseHas('stok_cabang', [
            'cabang_idcabang' => $cabang->idcabang,
            'produk_idproduk' => $produk->idproduk,
            'jumlah' => 10
        ]);

        // CEK STATUS DISTRIBUSI
        $this->assertDatabaseHas('distribusi_produk', [
            'permintaan_produk_id' => $permintaanProduk->id,
            'status_distribusi' => 'diterima'
        ]);

        // CEK STATUS HEADER
        $this->assertDatabaseHas('permintaan', [
            'idpermintaan' => $permintaan->idpermintaan,
            'status' => 'sampai'
        ]);
    }
}