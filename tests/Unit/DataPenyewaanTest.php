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

            // 🔥 FIX FK VALID
            'penyewa_idpenyewa' => $penyewa->idpenyewa,
            'cabang_idcabang' => $cabang->idcabang,
            'admin_pusat_idadmin_pusat' => $adminPusat->idadmin_pusat,
        ]);
    }

    /** @test */
    public function pen_01_admin_dapat_melihat_daftar_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $this->actingAs($user)
            ->get(route('data_penyewaan'))
            ->assertStatus(200)
            ->assertViewIs('data_penyewaan');
    }

    /** @test */
    public function pen_02_admin_dapat_menyetujui_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan($cabang);

        $this->actingAs($user)
            ->post(route('admin.konfirmasi_bayar', $penyewaan->idpenyewaan))
            ->assertSessionHas('success');
    }

    /** @test */
    public function pen_03_admin_dapat_membatalkan_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan($cabang);

        $this->actingAs($user)
            ->post(route('admin.penyewaan.cancel', $penyewaan->idpenyewaan))
            ->assertRedirect();
    }

    /** @test */
    public function pen_04_admin_dapat_melihat_detail_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan($cabang);

        $this->actingAs($user)
            ->get(route('admin.penyewaan.detail', $penyewaan->idpenyewaan))
            ->assertStatus(200)
            ->assertViewIs('detail_penyewaan');
    }

    /** @test */
    public function pen_05_admin_dapat_menyelesaikan_penyewaan()
    {
        [$user, $cabang] = $this->makeAdmin();

        $penyewaan = $this->createPenyewaan($cabang, 'sedang_disewa');

        $this->actingAs($user)
            ->post(route('admin.penyewaan.selesai', $penyewaan->idpenyewaan))
            ->assertRedirect(route('data_riwayat'));
    }
}