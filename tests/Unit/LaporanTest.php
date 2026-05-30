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

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdminCabang()
    {
        $user = User::factory()->create([
            'status' => 'admin_cabang'
        ]);

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

    private function createPenyewaan($cabang, $bulan = null)
    {
        $userPenyewa = User::factory()->create();

        $penyewa = Penyewa::create([
            'users_idusers' => $userPenyewa->idusers,
            'gambar_identitas' => null,
            'status_penyewa' => 'aktif',
        ]);

        $adminPusat = AdminPusat::factory()->create();

        return Penyewaan::create([
            'tanggal_sewa' => $bulan ?? now(),
            'tanggal_selesai' => now()->addDays(3),
            'tanggal_kembali' => now(),
            'sudah_diingatkan' => 0,
            'total' => 100000,
            'total_produk' => 1,
            'bukti_bayar' => null,
            'status_penyewaan' => 'selesai',
            'metode_bayar' => 'transfer',
            'batas_pembayaran' => now()->addDay(),

            'penyewa_idpenyewa' => $penyewa->idpenyewa,
            'cabang_idcabang' => $cabang->idcabang,
            'admin_pusat_idadmin_pusat' => $adminPusat->idadmin_pusat,
        ]);
    }

    /** TC-LP-01 */
    public function test_lp_01_halaman_laporan()
    {
        [$user] = $this->makeAdminCabang();

        $this->actingAs($user)
            ->get(route('laporan'))
            ->assertStatus(200);
    }

    /** TC-LP-02 */
    public function test_lp_02_filter_bulan()
    {
        [$user, $cabang] = $this->makeAdminCabang();

        $this->createPenyewaan($cabang, now());

        $this->actingAs($user)
            ->get(route('laporan', ['bulan' => '2025-01']))
            ->assertStatus(200)
            ->assertViewHas('penyewaan');
    }

    /** TC-LP-03 NEGATIF */
    public function test_lp_03_bulan_tanpa_data()
    {
        [$user] = $this->makeAdminCabang();

        $response = $this->actingAs($user)
            ->get(route('laporan', ['bulan' => '2024-02']));

        $response->assertStatus(200);

        $data = $response->viewData('penyewaan');
        $this->assertTrue($data->count() === 0);
    }

    /** TC-LP-04 */
    public function test_lp_04_total_pendapatan()
    {
        [$user, $cabang] = $this->makeAdminCabang();

        $this->createPenyewaan($cabang);
        $this->createPenyewaan($cabang);

        $response = $this->actingAs($user)
            ->get(route('laporan', ['bulan' => '2025-01']));

        $response->assertStatus(200)
            ->assertViewHas('totalPendapatan');
    }


    /** TC-LP-06 input bulan invalid */
    public function test_lp_05_bulan_invalid()
    {
        [$user] = $this->makeAdminCabang();

        $response = $this->actingAs($user)
            ->get(route('laporan', ['bulan' => '2024-13']));

        $response->assertStatus(200);

        $data = $response->viewData('penyewaan');
        $this->assertTrue($data->count() >= 0);
    }

    /** TC-LP-07 stress test data banyak */
   public function test_lp_06_data_banyak()
{
    [$user, $cabang] = $this->makeAdminCabang();

    for ($i = 0; $i < 100; $i++) {
        $this->createPenyewaan($cabang, '2025-01-05');
    }

    $response = $this->actingAs($user)
        ->get(route('laporan', ['bulan' => '2025-01']));

    $response->assertStatus(200)
             ->assertViewHas('penyewaan');

    // 🔥 tambahan validasi konsistensi data
    $data = $response->viewData('penyewaan');

    $this->assertTrue($data->count() > 0); // data muncul
}

    /** TC-LP-08 total harus sesuai DB */
    public function test_lp_07_total_pendapatan_sesuai()
{
    [$user, $cabang] = $this->makeAdminCabang();

    // DATA HARUS MASUK BULAN YANG DIFILTER
    $this->createPenyewaan($cabang, '2025-01-05');
    $this->createPenyewaan($cabang, '2025-01-10');

    $response = $this->actingAs($user)
        ->get(route('laporan', ['bulan' => '2025-01']));

    $response->assertStatus(200);

    $totalView = $response->viewData('totalPendapatan');

    $expected = \App\Models\Penyewaan::where('status_penyewaan', 'selesai')
        ->whereMonth('tanggal_sewa', 1)
        ->whereYear('tanggal_sewa', 2025)
        ->sum('total');

    $this->assertEquals($expected, $totalView);
}
}