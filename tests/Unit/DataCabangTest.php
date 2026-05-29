<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataCabangTest extends TestCase
{
    use RefreshDatabase;

    private function owner()
    {
        return User::factory()->create([
            'status' => 'owner'
        ]);
    }

    // ===============================
    // TC-DC-UT-01
    // ===============================
    public function test_tc_dc_ut_01_menampilkan_halaman_data_cabang()
    {
        $user = $this->owner();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);
    }

    // ===============================
    // TC-DC-UT-02
    // ===============================
    public function test_tc_dc_ut_02_menampilkan_seluruh_data_cabang()
    {
        $user = $this->owner();

        Cabang::factory()->create([
            'nama_cabang' => 'OutdoorKriss Glagah'
        ]);

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);

        $response->assertSee('OutdoorKriss Glagah');
    }

    // ===============================
    // TC-DC-UT-03
    // ===============================
    public function test_tc_dc_ut_03_halaman_tetap_tampil_saat_data_kosong()
    {
        $user = $this->owner();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);
    }

    // ===============================
    // TC-DC-UT-04
    // ===============================
    public function test_tc_dc_ut_04_toggle_status_aktif_ke_nonaktif()
    {
        $user = $this->owner();

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

    // ===============================
    // TC-DC-UT-05
    // ===============================
    public function test_tc_dc_ut_05_toggle_status_nonaktif_ke_aktif()
    {
        $user = $this->owner();

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

    // ===============================
    // TC-DC-UT-06
    // ===============================
    public function test_tc_dc_ut_06_menyetujui_data_cabang()
    {
        $user = $this->owner();

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

    // ===============================
    // TC-DC-UT-07
    // ===============================
    public function test_tc_dc_ut_07_menolak_data_cabang()
    {
        $user = $this->owner();

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

    // ===============================
    // TC-DC-UT-08
    // ===============================
    public function test_tc_dc_ut_08_pagination_dibatasi_maksimal_100()
    {
        $user = $this->owner();

        $response = $this->actingAs($user)
            ->get('/cabang?entries_cabang=999');

        $response->assertStatus(200);
    }
    public function test_tc_dc_ut_09_pagination_berjalan_dengan_benar()
{
    $user = $this->owner();

    // buat 21 data
    Cabang::factory()->count(21)->create();

    // halaman 1 -> tampil 20 data
    $responsePage1 = $this->actingAs($user)
        ->get('/cabang?entries_cabang=20&page=1');

    $responsePage1->assertStatus(200);

    // halaman 2
    $responsePage2 = $this->actingAs($user)
        ->get('/cabang?entries_cabang=20&page=2');

    $responsePage2->assertStatus(200);

    // cek data terakhir muncul di halaman 2
    $lastCabang = Cabang::latest('idcabang')->first();

    $responsePage2->assertSee($lastCabang->nama_cabang);
}
}