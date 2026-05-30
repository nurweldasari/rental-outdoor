<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Cabang;
use App\Models\AdminCabang;
use App\Models\Penyewaan;
use App\Models\Penyewa;
use App\Models\AdminPusat;

class DataPenyewaanTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin()
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

        return [$user, $cabang];
    }

    private function createPenyewaan($cabang, $status = 'menunggu_pembayaran')
    {
        $userPenyewa = User::factory()->create();

        $penyewa = Penyewa::create([
            'users_idusers' => $userPenyewa->idusers,
            'gambar_identitas' => null,
            'status_penyewa' => 'aktif'
        ]);

        $adminPusat = AdminPusat::factory()->create();

        return Penyewaan::create([
            'tanggal_sewa' => now(),
            'tanggal_selesai' => now()->addDays(3),
            'tanggal_kembali' => null,
            'sudah_diingatkan' => 0,
            'total' => 100000,
            'total_produk' => 1,
            'bukti_bayar' => null,
            'status_penyewaan' => $status,
            'metode_bayar' => 'transfer',
            'batas_pembayaran' => now()->addDay(),
            'penyewa_idpenyewa' => $penyewa->idpenyewa,
            'cabang_idcabang' => $cabang->idcabang,
            'admin_pusat_idadmin_pusat' => $adminPusat->idadmin_pusat,
        ]);
    }

    /**
     * TC-PS-01
     */
    public function test_pen_01_admin_dapat_melihat_daftar_penyewaan()
{
    [$user, $cabang] = $this->makeAdmin();

    $penyewaan = $this->createPenyewaan($cabang);

    $response = $this->actingAs($user)
        ->get(route('data_penyewaan'));

    $response->assertStatus(200);
    $response->assertViewIs('data_penyewaan');

    $this->assertDatabaseHas('penyewaan', [
        'idpenyewaan' => $penyewaan->idpenyewaan
    ]);
}
    /**
     * TC-PS-02
     */
    public function test_pen_02_admin_dapat_menyetujui_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan(
            $cabang,
            'menunggu_pembayaran'
        );

        $this->actingAs($user)
            ->post(
                route(
                    'admin.konfirmasi_bayar',
                    $penyewaan->idpenyewaan
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseHas('penyewaan', [
            'idpenyewaan' => $penyewaan->idpenyewaan,
            'status_penyewaan' => 'sedang_disewa'
        ]);
    }

    /**
     * TC-PS-03
     */
    public function test_pen_03_admin_dapat_membatalkan_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan(
            $cabang,
            'menunggu_pembayaran'
        );

        $this->actingAs($user)
            ->post(
                route(
                    'admin.penyewaan.cancel',
                    $penyewaan->idpenyewaan
                )
            )
            ->assertRedirect();

        $this->assertDatabaseHas('penyewaan', [
            'idpenyewaan' => $penyewaan->idpenyewaan,
            'status_penyewaan' => 'dibatalkan'
        ]);
    }

    /**
     * TC-PS-04
     */
    public function test_pen_04_admin_dapat_melihat_detail_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan($cabang);

        $this->actingAs($user)
            ->get(
                route(
                    'admin.penyewaan.detail',
                    $penyewaan->idpenyewaan
                )
            )
            ->assertStatus(200)
            ->assertViewIs('detail_penyewaan')
            ->assertViewHas('penyewaan');
    }

    /**
     * TC-PS-05
     */
    public function test_pen_05_admin_dapat_menyelesaikan_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan(
            $cabang,
            'sedang_disewa'
        );

        $this->actingAs($user)
            ->post(
                route(
                    'admin.penyewaan.selesai',
                    $penyewaan->idpenyewaan
                )
            )
            ->assertRedirect(route('data_riwayat'));

        $this->assertDatabaseHas('penyewaan', [
            'idpenyewaan' => $penyewaan->idpenyewaan,
            'status_penyewaan' => 'selesai'
        ]);
    }

    /**
     * TC-PS-09
     */
    public function test_pen_06_detail_penyewaan_tidak_ditemukan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $this->actingAs($user)
            ->get(route('admin.penyewaan.detail', 999))
            ->assertStatus(404);
    }

    /**
     * TC-PS-10
     */
    public function test_pen_07_konfirmasi_pengembalian_belum_aktif()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan(
            $cabang,
            'menunggu_pembayaran'
        );

        $this->actingAs($user)
            ->post(
                route(
                    'admin.penyewaan.selesai',
                    $penyewaan->idpenyewaan
                )
            );

        $this->assertDatabaseHas('penyewaan', [
            'idpenyewaan' => $penyewaan->idpenyewaan,
            'status_penyewaan' => 'menunggu_pembayaran'
        ]);
    }
}