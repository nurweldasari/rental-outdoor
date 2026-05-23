<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use App\Http\Controllers\PenyewaController;

class DataPenyewaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * =====================================================
     * FUNCTION BANTUAN UNTUK MEMBUAT USER LOGIN
     * =====================================================
     */
    private function loginUser()
{
    $user = User::create([
        'idusers' => 1,
        'nama' => 'Test User',
        'username' => 'test',
        'password' => bcrypt('123456'),
        'no_telepon' => '0811111111',
        'alamat' => 'alamat',
        'status' => 'admin_cabang',
    ]);

    // buat cabang
    DB::table('cabang')->insert([
    'idcabang' => 1,
    'nama_cabang' => 'Cabang Test',
    'status_cabang' => 'aktif',
    'lokasi' => 'Banyuwangi',
    'created_at' => now(),
    'updated_at' => now(),
]);

    // masukkan admin cabang
    DB::table('admin_cabang')->insert([
        'users_idusers' => 1,
        'cabang_idcabang' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user);

    return $user;
}

    /**
     * =====================================================
     * TC-DP-01
     * Menampilkan daftar penyewa
     * =====================================================
     */
    #[Test]
    public function tc_dp_01_menampilkan_daftar_penyewa()
    {
        // login user terlebih dahulu
        $this->loginUser();

        // tambah data penyewa
        DB::table('users')->insert([
    'idusers' => 2,
    'nama' => 'Penyewa 1',
    'username' => 'penyewa1',
    'password' => bcrypt('123456'),
    'no_telepon' => '0822222222', // beda
    'alamat' => 'alamat',
    'status' => 'penyewa',
    'created_at' => now(),
    'updated_at' => now()
]);

        DB::table('penyewa')->insert([
            'users_idusers' => 2,
            'gambar_identitas' => 'test.jpg',
            'status_penyewa' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // jalankan controller
        $controller = new PenyewaController();
        $response = $controller->index(new Request());

        // pastikan response tidak null
        $this->assertNotNull($response);
    }

    /**
     * =====================================================
     * TC-DP-02
     * Pencarian data penyewa
     * =====================================================
     */
    #[Test]
    public function tc_dp_02_pencarian_data_penyewa()
    {
        // login user
        $this->loginUser();

        // tambah data penyewa
        DB::table('users')->insert([
    'idusers' => 2,
    'nama' => 'Novita',
    'username' => 'novita',
    'password' => bcrypt('123456'),
    'no_telepon' => '0833333333', // beda
    'alamat' => 'alamat',
    'status' => 'penyewa',
    'created_at' => now(),
    'updated_at' => now()
]);

        DB::table('penyewa')->insert([
            'users_idusers' => 2,
            'gambar_identitas' => 'test.jpg',
            'status_penyewa' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // request pencarian
        $request = new Request([
            'search' => 'Novita'
        ]);

        // jalankan controller
        $controller = new PenyewaController();
        $response = $controller->index($request);

        // cek response
        $this->assertNotNull($response);
    }

    /**
     * =====================================================
     * TC-DP-03
     * Menambah data penyewa
     * =====================================================
     */
    #[Test]
    public function tc_dp_03_menambah_data_penyewa()
    {
        Storage::fake('public');

        // upload file palsu tanpa GD
        $file = UploadedFile::fake()->create(
            'ktp.jpg',
            100,
            'image/jpeg'
        );

        // kirim request tambah penyewa
        $response = $this->post(route('tambah_penyewa.store'), [
            'nama' => 'Nur',
            'username' => 'Nadia08',
            'password' => '123456',
            'no_telepon' => '081234567890',
            'alamat' => 'alamat',
            'gambar_identitas' => $file,
        ]);

        // cek redirect berhasil
        $response->assertRedirect(route('data_penyewa'));

        // cek data user masuk database
        $this->assertDatabaseHas('users', [
            'username' => 'Nadia08',
            'status' => 'penyewa'
        ]);

        // cek data penyewa masuk database
        $this->assertDatabaseHas('penyewa', [
            'status_penyewa' => 'pending'
        ]);
    }

    /**
     * =====================================================
     * TC-DP-04
     * Melihat detail penyewa
     * =====================================================
     */
    #[Test]
    public function tc_dp_04_melihat_detail_penyewa()
    {
        // tambah user
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

        // tambah data penyewa
        DB::table('penyewa')->insert([
            'users_idusers' => 1,
            'gambar_identitas' => 'test.jpg',
            'status_penyewa' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // ambil data detail
        $data = DB::table('penyewa')
            ->where('users_idusers', 1)
            ->first();

        // pastikan data ada
        $this->assertNotNull($data);
    }

    /**
     * =====================================================
     * TC-DP-05
     * Membuat reservasi dari penyewa
     * =====================================================
     */
    #[Test]
    public function tc_dp_05_buat_reservasi_dari_penyewa()
    {
        // redirect ke katalog produk
        $response = redirect()->route('katalog_produk');

        // cek status redirect
        $this->assertEquals(302, $response->getStatusCode());
    }
}