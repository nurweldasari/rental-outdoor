<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Penyewaan;
use App\Models\BagiHasil;
use App\Models\SkalaBagiHasil;

class BagiHasilTest extends TestCase
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
    // TC-BH-01
    // Owner berhasil login
    // =====================================================
    public function test_tc_bh_01_owner_berhasil_login()
    {
        [$owner, $password] = $this->owner();

        $response = $this->post('/login', [
            'username' => $owner->username,
            'password' => $password
        ]);

        $response->assertRedirect(
            route('dashboard')
        );

        $this->assertAuthenticated();
    }

    // =====================================================
    // TC-BH-02
    // Owner dapat membuka halaman bagi hasil
    // =====================================================
    public function test_tc_bh_02_owner_dapat_membuka_halaman_bagi_hasil()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $response = $this->get('/bagi-hasil');

        $response->assertViewIs(
            'bagi_hasil_owner'
        );

        $response->assertViewHas('cabangs');

        $response->assertViewHas('riwayat');
    }

    // =====================================================
    // TC-BH-03
    // Perhitungan otomatis bagi hasil
    // =====================================================
    public function test_tc_bh_03_perhitungan_otomatis_bagi_hasil()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $cabang = Cabang::factory()->create();

        Penyewaan::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'total' => 1000000,
            'status_penyewaan' => 'selesai',
            'tanggal_sewa' => now(),
        ]);

        SkalaBagiHasil::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'owner' => 50,
            'cabang' => 50,
        ]);

        $response = $this->get(
            '/bagi-hasil/detail/' . $cabang->idcabang
        );

        $response->assertViewIs(
            'bagi_hasil_owner'
        );

        $response->assertViewHas(
            'totalPendapatan',
            1000000
        );

        $response->assertViewHas(
            'hasilOwner',
            500000
        );

        $response->assertViewHas(
            'hasilCabang',
            500000
        );
    }

    // =====================================================
    // TC-BH-04
    // Simpan bagi hasil ke database
    // =====================================================
    public function test_tc_bh_04_simpan_bagi_hasil_ke_database()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $cabang = Cabang::factory()->create();

        $response = $this->post(
            '/bagi-hasil/store',
            [
                'cabang_idcabang' => $cabang->idcabang,
                'bulan' => '2026-01',
                'presentase_owner' => 50,
                'presentase_cabang' => 50,
                'nominal_owner' => 500000,
                'nominal_cabang' => 500000,
            ]
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'success'
        );

        $this->assertDatabaseHas('bagi_hasil', [
            'cabang_idcabang' => $cabang->idcabang,
            'bulan' => '2026-01',
            'presentase_owner' => 50,
            'presentase_cabang' => 50,
            'nominal_owner' => 500000,
            'nominal_cabang' => 500000,
            'status' => 'terkunci',
        ]);
    }

    // =====================================================
    // TC-BH-05
    // Gagal simpan karena sudah closing
    // =====================================================
    public function test_tc_bh_05_gagal_simpan_karena_sudah_closing()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $cabang = Cabang::factory()->create();

        BagiHasil::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'bulan' => '2026-01',
        ]);

        $response = $this->post(
            '/bagi-hasil/store',
            [
                'cabang_idcabang' => $cabang->idcabang,
                'bulan' => '2026-01',
                'presentase_owner' => 50,
                'presentase_cabang' => 50,
                'nominal_owner' => 500000,
                'nominal_cabang' => 500000,
            ]
        );

        $response->assertSessionHas(
            'error'
        );
    }

    // =====================================================
    // TC-BH-06
    // Konfirmasi bukti fee
    // =====================================================
    public function test_tc_bh_06_konfirmasi_bukti_fee()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $data = BagiHasil::factory()->create([
            'status' => 'menunggu'
        ]);

        $response = $this->post(
            '/bagi-hasil/' . $data->idbagi_hasil . '/konfirmasi'
        );

        $response->assertSessionHas(
            'success'
        );

        $this->assertDatabaseHas('bagi_hasil', [
            'idbagi_hasil' => $data->idbagi_hasil,
            'status' => 'terkonfirmasi'
        ]);
    }

    // =====================================================
    // TC-BH-07
    // Menolak bukti fee
    // =====================================================
    public function test_tc_bh_07_menolak_bukti_fee()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $data = BagiHasil::factory()->create([
            'status' => 'menunggu'
        ]);

        $response = $this->post(
            '/bagi-hasil/' . $data->idbagi_hasil . '/tolak'
        );

        $response->assertSessionHas(
            'success'
        );

        $this->assertDatabaseHas('bagi_hasil', [
            'idbagi_hasil' => $data->idbagi_hasil,
            'status' => 'ditolak'
        ]);
    }

    // =====================================================
    // TC-BH-08
    // Upload bukti fee
    // =====================================================
    public function test_tc_bh_08_upload_bukti_fee()
    {
        Storage::fake('public');

        $data = BagiHasil::factory()->create([
            'status' => 'terkunci'
        ]);

        $file = UploadedFile::fake()->image(
            'bukti.jpg'
        );

        $response = $this->post(
            '/bagi-hasil/upload/' . $data->idbagi_hasil,
            [
                'bukti_fee' => $file
            ]
        );

        $response->assertSessionHas(
            'success'
        );

        $this->assertDatabaseHas('bagi_hasil', [
            'idbagi_hasil' => $data->idbagi_hasil,
            'status' => 'menunggu'
        ]);
    }

    // =====================================================
    // TC-BH-09
    // Melihat bukti fee
    // =====================================================
    public function test_tc_bh_09_melihat_bukti_fee()
    {
        [$owner, $password] = $this->owner();

        $this->loginOwner(
            $owner->username,
            $password
        );

        $cabang = Cabang::factory()->create();

        BagiHasil::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'bukti_fee' => 'bukti.jpg',
            'status' => 'menunggu'
        ]);

        $response = $this->get(
            '/bagi-hasil/detail/' . $cabang->idcabang
        );

        $response->assertViewIs(
            'bagi_hasil_owner'
        );

        $response->assertViewHas(
            'buktiFee'
        );
    }
}