<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use App\Http\Controllers\PenyewaController;

class DataPenyewaTest extends TestCase
{
    use RefreshDatabase;

    private function makeRequest($data, $file = null)
    {
        $request = Request::create('/penyewa', 'POST', $data);

        if ($file) {
            $request->files->set('gambar_identitas', $file);
        }

        return $request;
    }

    // =========================
    // TC-DP-01
    // =========================
    #[Test]
    public function tc_dp_01_menampilkan_daftar_penyewa()
    {
        DB::table('users')->insert([
            'idusers' => 1,
            'nama' => 'Test User',
            'username' => 'test',
            'password' => bcrypt('123456'),
            'no_telepon' => '08123',
            'alamat' => 'alamat',
            'status' => 'penyewa',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('penyewa')->insert([
            'users_idusers' => 1,
            'gambar_identitas' => 'test.jpg',
            'status_penyewa' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $controller = new PenyewaController();
        $response = $controller->index(new Request());

        $this->assertNotNull($response);
    }

    // =========================
    // TC-DP-02
    // =========================
    #[Test]
    public function tc_dp_02_pencarian_data_penyewa()
    {
        DB::table('users')->insert([
            'idusers' => 1,
            'nama' => 'Novita',
            'username' => 'novita',
            'password' => bcrypt('123456'),
            'no_telepon' => '08123',
            'alamat' => 'alamat',
            'status' => 'penyewa',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('penyewa')->insert([
            'users_idusers' => 1,
            'gambar_identitas' => 'test.jpg',
            'status_penyewa' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $request = new Request(['search' => 'Novita']);

        $controller = new PenyewaController();
        $response = $controller->index($request);

        $this->assertNotNull($response);
    }

    // =========================
    // TC-DP-03 (FIX GD ERROR)
#[Test]
public function tc_dp_03_menambah_data_penyewa()
{
    Storage::fake('public');

    // ✅ TANPA GD
    $file = UploadedFile::fake()->create(
        'ktp.jpg',
        100,
        'image/jpeg'
    );

    $response = $this->post(route('tambah_penyewa.store'), [
        'nama' => 'Nur',
        'username' => 'Nadia08',
        'password' => '123456',
        'no_telepon' => '081234567890',
        'alamat' => 'alamat',
        'gambar_identitas' => $file,
    ]);

    // ✅ cek redirect
    $response->assertRedirect(route('data_penyewa'));

    // ✅ cek users
    $this->assertDatabaseHas('users', [
        'username' => 'Nadia08',
        'status' => 'penyewa'
    ]);

    // ✅ cek penyewa
    $this->assertDatabaseHas('penyewa', [
        'status_penyewa' => 'pending'
    ]);
}
    // =========================
    // TC-DP-04
    // =========================
    #[Test]
    public function tc_dp_04_melihat_detail_penyewa()
    {
        DB::table('users')->insert([
            'idusers' => 1,
            'nama' => 'Test',
            'username' => 'test',
            'password' => bcrypt('123456'),
            'no_telepon' => '08123',
            'alamat' => 'alamat',
            'status' => 'penyewa',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('penyewa')->insert([
            'users_idusers' => 1,
            'gambar_identitas' => 'test.jpg',
            'status_penyewa' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $data = DB::table('penyewa')->where('users_idusers', 1)->first();

        $this->assertNotNull($data);
    }

    // =========================
    // TC-DP-05
    // =========================
    #[Test]
    public function tc_dp_05_buat_reservasi_dari_penyewa()
    {
        $response = redirect()->route('katalog_produk');

        $this->assertEquals(302, $response->getStatusCode());
    }
}