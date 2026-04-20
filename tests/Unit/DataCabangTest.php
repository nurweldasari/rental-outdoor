<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataCabangTest extends TestCase
{
    use RefreshDatabase;

    /* ================= TC-DC-OW-01 ================= */
    public function test_owner_bisa_membuka_halaman_data_cabang()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);
        $response->assertViewIs('data_cabang');
    }

    /* ================= TC-DC-OW-02 ================= */
    public function test_menampilkan_semua_data_cabang()
    {
        $user = User::factory()->create();

        Cabang::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);
        $response->assertViewHas('cabang');
    }

    /* ================= TC-DC-OW-03 ================= */
    public function test_pencarian_data_cabang_ditemukan()
    {
        $user = User::factory()->create();

        Cabang::factory()->create([
            'nama_cabang' => 'OutdoorKriss Glagah'
        ]);

        $response = $this->actingAs($user)
            ->get('/cabang?search=OutdoorKriss');

        $response->assertStatus(200);
        $response->assertSee('OutdoorKriss Glagah');
    }

    /* ================= TC-DC-OW-04 ================= */
    public function test_pencarian_data_cabang_tidak_ditemukan()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/cabang?search=TidakAda');

        $response->assertStatus(200);
        $response->assertSee('Tidak ada data cabang');
    }

    /* ================= TC-DC-OW-05 ================= */
    public function test_detail_cabang_tampil()
    {
        $user = User::factory()->create();

        $cabang = Cabang::factory()->create([
            'nama_cabang' => 'OutdoorKriss Glagah'
        ]);

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertSee('OutdoorKriss Glagah');
    }

    /* ================= TC-DC-OW-06 ================= */
    public function test_validasi_data_detail_cabang()
    {
        $user = User::factory()->create();

        $cabang = Cabang::factory()->create([
            'nama_cabang' => 'OutdoorKriss',
            'lokasi' => 'Jl. Ikan Pari'
        ]);

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertSee('OutdoorKriss');
        $response->assertSee('Jl. Ikan Pari');
    }

    /* ================= TC-DC-OW-07 ================= */
    public function test_file_mou_bisa_diakses()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $cabang = Cabang::factory()->create([
            'mou_file' => 'mou/test.pdf'
        ]);

        $response = $this->actingAs($user)
            ->get('/storage/mou/test.pdf');

        $response->assertStatus(200);
    }

    /* ================= TC-DC-OW-08 ================= */
    public function test_modal_bisa_ditutup()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertStatus(200);
    }

    /* ================= TC-DC-OW-09 ================= */
    public function test_status_dan_konfirmasi_tampil()
    {
        $user = User::factory()->create();

        Cabang::factory()->create([
            'status_cabang' => 'aktif'
        ]);

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertSee('aktif');
    }

    /* ================= TC-DC-OW-10 ================= */
    public function test_tidak_ada_data_cabang()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/cabang');

        $response->assertSee('Tidak ada data cabang');
    }
}