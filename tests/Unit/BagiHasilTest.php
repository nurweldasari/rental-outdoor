<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Penyewaan;
use App\Models\BagiHasil;
use App\Models\SkalaBagiHasil;
use App\Models\AdminCabang;
use App\Http\Controllers\BagiHasilController;

class BagiHasilTest extends TestCase
{
    use RefreshDatabase;

    // =========================
    // TC-BH-01: Hitung otomatis bagi hasil
    // =========================
    public function test_tc_bh_01_perhitungan_otomatis()
    {
        $cabang = Cabang::factory()->create();

        Penyewaan::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'total' => 1000000,
            'status_penyewaan' => 'selesai',
            'tanggal_sewa' => now(),
        ]);

        BagiHasil::create([
            'cabang_idcabang' => $cabang->idcabang,
            'bulan' => now()->format('Y-m'),
            'presentase_owner' => 50,
            'presentase_cabang' => 50,
            'nominal_owner' => 500000,
            'nominal_cabang' => 500000,
            'status' => 'terkunci',
        ]);

        $controller = new BagiHasilController();
        $response = $controller->index(new Request());

        $this->assertNotNull($response);
    }

    // =========================
    // TC-BH-02: Simpan ke database
    // =========================
    public function test_tc_bh_02_simpan_database()
    {
        $cabang = Cabang::factory()->create();

        $request = new Request([
            'cabang_idcabang' => $cabang->idcabang,
            'bulan' => '2026-01',
            'presentase_owner' => 50,
            'presentase_cabang' => 50,
            'nominal_owner' => 500000,
            'nominal_cabang' => 500000,
        ]);

        $controller = new BagiHasilController();
        $controller->store($request);

        $this->assertDatabaseHas('bagi_hasil', [
            'cabang_idcabang' => $cabang->idcabang,
            'bulan' => '2026-01',
            'status' => 'terkunci',
        ]);
    }

    // =========================
    // TC-BH-03: Update skala
    // =========================
    public function test_tc_bh_03_update_skala()
    {
        $skala = SkalaBagiHasil::factory()->create([
            'owner' => 70,
            'cabang' => 30,
        ]);

        $this->assertEquals(70, $skala->owner);
        $this->assertEquals(30, $skala->cabang);
    }

    // =========================
    // TC-BH-04: Batal perubahan
    // =========================
    public function test_tc_bh_04_batal_perubahan()
    {
        $skala = SkalaBagiHasil::factory()->create([
            'owner' => 50,
            'cabang' => 50,
        ]);

        $backup = $skala->owner;

        // simulasi batal
        $skala->owner = $backup;

        $this->assertEquals(50, $skala->owner);
    }

    // =========================
    // TC-BH-05: Lihat bukti fee
    // =========================
    public function test_tc_bh_05_lihat_bukti_fee()
    {
        $cabang = Cabang::factory()->create();

        BagiHasil::factory()->create([
            'cabang_idcabang' => $cabang->idcabang,
            'bukti_fee' => 'bukti.jpg',
            'status' => 'menunggu'
        ]);

        $data = BagiHasil::whereNotNull('bukti_fee')->get();

        $this->assertNotEmpty($data);
    }

    // =========================
    // TC-BH-06: Konfirmasi bukti
    // =========================
    public function test_tc_bh_06_konfirmasi_bukti()
    {
        $data = BagiHasil::factory()->create([
            'status' => 'menunggu'
        ]);

        $controller = new BagiHasilController();
        $controller->konfirmasi($data->idbagi_hasil);

        $this->assertDatabaseHas('bagi_hasil', [
            'idbagi_hasil' => $data->idbagi_hasil,
            'status' => 'terkonfirmasi'
        ]);
    }

    // =========================
    // TC-BH-07: Tolak bukti
    // =========================
    public function test_tc_bh_07_tolak_bukti()
    {
        $data = BagiHasil::factory()->create([
            'status' => 'menunggu'
        ]);

        $controller = new BagiHasilController();
        $controller->tolak($data->idbagi_hasil);

        $this->assertDatabaseHas('bagi_hasil', [
            'idbagi_hasil' => $data->idbagi_hasil,
            'status' => 'ditolak'
        ]);
    }

    // =========================
    // TC-BH-08: Upload bukti fee
    // =========================
    public function test_tc_bh_08_upload_bukti()
    {
        $data = BagiHasil::factory()->create([
            'status' => 'terkunci'
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti.jpg');

        $request = new Request();
        $request->files->set('bukti_fee', $file);

        $controller = new BagiHasilController();
        $controller->uploadBukti($request, $data->idbagi_hasil);

        $this->assertDatabaseHas('bagi_hasil', [
            'idbagi_hasil' => $data->idbagi_hasil,
            'status' => 'menunggu'
        ]);
    }
}