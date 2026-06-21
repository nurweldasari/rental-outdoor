<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataCabangTest extends TestCase
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
    // TC-DC-UT-01
    // Owner berhasil login
    // =====================================================
    public function test_tc_dc_ut_01_owner_berhasil_login()
    {
        [$owner, $password] = $this->owner();

        $response = $this->post('/login', [
            'username' => $owner->username,
            'password' => $password
        ]);

        $response->assertRedirect(
            route('dashboard')
        );

        $this->assertAuthenticatedAs($owner);
    }

    // =====================================================
    // TC-DC-UT-02
    // Owner dapat membuka halaman data cabang
    // =====================================================
    public function test_tc_dc_ut_02_owner_dapat_membuka_halaman_data_cabang()
    {
        [$owner, $password] = $this->owner();

        // LOGIN OWNER
        $this->loginOwner(
            $owner->username,
            $password
        );

        // AKSES HALAMAN
        $response = $this->get('/cabang');

        // CEK VIEW
        $response->assertViewIs('data_cabang');

        // CEK DATA VIEW
        $response->assertViewHas('cabang');
        $response->assertViewHas('rekening');
        $response->assertViewHas('listCabang');
    }

    // =====================================================
    // TC-DC-UT-03
    // Menampilkan seluruh data cabang
    // =====================================================
    public function test_tc_dc_ut_03_menampilkan_seluruh_data_cabang()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // BUAT DATA CABANG
        $cabang = Cabang::factory()->create([
            'nama_cabang' => 'OutdoorKriss Glagah',
            'status_cabang' => 'aktif'
        ]);

        // AKSES HALAMAN
        $response = $this->get('/cabang');

        // CEK DATA MUNCUL
        $response->assertSee(
            $cabang->nama_cabang
        );
    }

    // =====================================================
    // TC-DC-UT-04
    // Halaman tetap tampil saat data kosong
    // =====================================================
    public function test_tc_dc_ut_04_halaman_tetap_tampil_saat_data_kosong()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // AKSES HALAMAN
        $response = $this->get('/cabang');

        // VIEW TETAP ADA
        $response->assertViewIs(
            'data_cabang'
        );

        // DATA CABANG ADA WALAU KOSONG
        $response->assertViewHas('cabang');
    }

    // =====================================================
    // TC-DC-UT-05
    // Toggle status aktif menjadi nonaktif
    // =====================================================
    public function test_tc_dc_ut_05_toggle_status_aktif_ke_nonaktif()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // DATA CABANG
        $cabang = Cabang::factory()->create([
            'status_cabang' => 'aktif'
        ]);

        // TOGGLE STATUS
        $response = $this->post(
            '/cabang/toggle/' . $cabang->idcabang
        );

        $response->assertSessionHas('success');

        // CEK DATABASE
        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'nonaktif'
        ]);
    }

    // =====================================================
    // TC-DC-UT-06
    // Toggle status nonaktif menjadi aktif
    // =====================================================
    public function test_tc_dc_ut_06_toggle_status_nonaktif_ke_aktif()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // DATA CABANG
        $cabang = Cabang::factory()->create([
            'status_cabang' => 'nonaktif'
        ]);

        // TOGGLE STATUS
        $response = $this->post(
            '/cabang/toggle/' . $cabang->idcabang
        );

        $response->assertSessionHas('success');

        // CEK DATABASE
        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'aktif'
        ]);
    }

    // =====================================================
    // TC-DC-UT-07
    // Owner menyetujui data cabang
    // =====================================================
    public function test_tc_dc_ut_07_owner_menyetujui_data_cabang()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // DATA CABANG
        $cabang = Cabang::factory()->create([
            'status_cabang' => 'pending'
        ]);

        // SETUJUI CABANG
        $response = $this->post(
            '/cabang/terima/' . $cabang->idcabang
        );

        $response->assertSessionHas('success');

        // CEK STATUS BERUBAH
        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'aktif'
        ]);
    }

    // =====================================================
    // TC-DC-UT-08
    // Owner menolak data cabang
    // =====================================================
    public function test_tc_dc_ut_08_owner_menolak_data_cabang()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // DATA CABANG
        $cabang = Cabang::factory()->create([
            'status_cabang' => 'pending'
        ]);

        // TOLAK CABANG
        $response = $this->post(
            '/cabang/tolak/' . $cabang->idcabang
        );

        $response->assertSessionHas('success');

        // CEK STATUS BERUBAH
        $this->assertDatabaseHas('cabang', [
            'idcabang' => $cabang->idcabang,
            'status_cabang' => 'ditolak'
        ]);
    }

    // =====================================================
    // TC-DC-UT-09
    // Pagination dibatasi maksimal 100
    // =====================================================
    public function test_tc_dc_ut_09_pagination_dibatasi_maksimal_100()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // BUAT DATA
        Cabang::factory()->count(5)->create();

        // REQUEST ENTRIES BESAR
        $response = $this->get(
            '/cabang?entries_cabang=999'
        );

        // CEK VIEW
        $response->assertViewHas('entriesCabang');

        // CEK NILAI MAX 100
        $this->assertEquals(
            100,
            $response->viewData('entriesCabang')
        );
    }

    // =====================================================
    // TC-DC-UT-10
    // Pagination berjalan dengan benar
    // =====================================================
    public function test_tc_dc_ut_10_pagination_berjalan_dengan_benar()
    {
        [$owner, $password] = $this->owner();

        // LOGIN
        $this->loginOwner(
            $owner->username,
            $password
        );

        // BUAT 21 DATA
        Cabang::factory()->count(21)->create();

        // HALAMAN 1
        $responsePage1 = $this->get(
            '/cabang?entries_cabang=20&page=1'
        );

        $responsePage1->assertViewHas('cabang');

        // HALAMAN 2
        $responsePage2 = $this->get(
            '/cabang?entries_cabang=20&page=2'
        );

        $responsePage2->assertViewHas('cabang');

        $cabangPage2 = Cabang::skip(20)->first();

        // CEK DATA MUNCUL DI PAGE 2
        $responsePage2->assertSee(
            $cabangPage2->nama_cabang
        );
    }
}