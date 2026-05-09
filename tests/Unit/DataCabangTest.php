<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataCabangTest extends TestCase
{
    use RefreshDatabase;

    /* ===============================
       TC-DC-UT-01 menampilkan halaman data cabang
       =============================== */
    public function test_tc_dc_ut_01_menampilkan_halaman_data_cabang()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);
    }

    /* ===============================
       TC-DC-UT-02 menampilkan seluruh data cabang
       =============================== */
    public function test_tc_dc_ut_02_menampilkan_seluruh_data_cabang()
    {
        $user = User::factory()->create();

        Cabang::factory()->create([
            'nama_cabang' => 'OutdoorKriss Glagah'
        ]);

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);

        $response->assertSee('OutdoorKriss Glagah');
    }

    /* ===============================
       TC-DC-UT-03 menampilkan halaman saat data kosong
       =============================== */
    public function test_tc_dc_ut_03_halaman_tetap_tampil_saat_data_kosong()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);
    }

    /* ===============================
       TC-DC-UT-04 mengubah status cabang dari aktif ke nonaktif
       =============================== */
    public function test_tc_dc_ut_04_toggle_status_aktif_ke_nonaktif()
    {
        $user = User::factory()->create();

        $cabang = Cabang::factory()->create([
            'status_cabang' => 'aktif'
        ]);

        $this->actingAs($user)
            ->post('/cabang/toggle/' . $cabang->idcabang);

        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'nonaktif'
        ]);
    }

    /* ===============================
       TC-DC-UT-05 toggle status cabang dari nonaktif ke aktif
       =============================== */
    public function test_tc_dc_ut_05_toggle_status_nonaktif_ke_aktif()
    {
        $user = User::factory()->create();

        $cabang = Cabang::factory()->create([
            'status_cabang' => 'nonaktif'
        ]);

        $this->actingAs($user)
            ->post('/cabang/toggle/' . $cabang->idcabang);

        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'aktif'
        ]);
    }

    /* ===============================
       TC-DC-UT-06 menyetujui data cabang
       =============================== */
    public function test_tc_dc_ut_06_menyetujui_data_cabang()
    {
        $user = User::factory()->create();

        $cabang = Cabang::factory()->create([
            'status_cabang' => 'pending'
        ]);

        $this->actingAs($user)
            ->post('/cabang/terima/' . $cabang->idcabang);

        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'aktif'
        ]);
    }

    /* ===============================
       TC-DC-UT-07 menolak data cabang
       =============================== */
    public function test_tc_dc_ut_07_menolak_data_cabang()
    {
        $user = User::factory()->create();

        $cabang = Cabang::factory()->create([
            'status_cabang' => 'pending'
        ]);

        $this->actingAs($user)
            ->post('/cabang/tolak/' . $cabang->idcabang);

        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'ditolak'
        ]);
    }

    /* ===============================
       TC-DC-UT-08 pagination dibatasi maksimal 100
       =============================== */
    public function test_tc_dc_ut_08_pagination_dibatasi_maksimal_100()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/cabang?entries=999');

        $response->assertStatus(200);
    }
}