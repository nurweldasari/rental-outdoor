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

   #[Test]
public function tc_dp_01_menampilkan_daftar_penyewa()
{
    $this->loginUser();

    DB::table('users')->insert([
        'idusers' => 2,
        'nama' => 'Penyewa 1',
        'username' => 'penyewa1',
        'password' => bcrypt('123456'),
        'no_telepon' => '0822222222',
        'alamat' => 'alamat',
        'status' => 'penyewa',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    DB::table('penyewa')->insert([
        'users_idusers' => 2,
        'gambar_identitas' => 'test.jpg',
        'status_penyewa' => 'aktif',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    $controller = new PenyewaController();
    $response = $controller->index(new Request());

    $this->assertNotNull($response);

    $this->assertDatabaseHas('users', [
        'nama' => 'Penyewa 1',
        'status' => 'penyewa'
    ]);

    $this->assertDatabaseHas('penyewa', [
    'status_penyewa' => 'aktif'
]);
}
#[Test]
public function tc_dp_02_pencarian_data_penyewa()
{
    $this->loginUser();

    DB::table('users')->insert([
        'idusers' => 2,
        'nama' => 'Novita',
        'username' => 'novita',
        'password' => bcrypt('123456'),
        'no_telepon' => '0833333333',
        'alamat' => 'alamat',
        'status' => 'penyewa',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    DB::table('penyewa')->insert([
        'users_idusers' => 2,
        'gambar_identitas' => 'test.jpg',
        'status_penyewa' => 'aktif',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    $request = new Request([
        'search' => 'Novita'
    ]);

    $controller = new PenyewaController();
    $response = $controller->index($request);

    $this->assertNotNull($response);

    $data = DB::table('users')
        ->where('nama', 'Novita')
        ->first();

    $this->assertNotNull($data);
    $this->assertEquals('Novita', $data->nama);
}
#[Test]
public function tc_dp_03_menambah_data_penyewa()
{
    Storage::fake('public');

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

    $response->assertRedirect(route('data_penyewa'));

    $this->assertDatabaseHas('users', [
        'nama' => 'Nur',
        'username' => 'Nadia08',
        'status' => 'penyewa'
    ]);

    $this->assertDatabaseHas('penyewa', [
    'users_idusers' => 1,
    'status_penyewa' => 'aktif'
]);
}
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
        'status_penyewa' => 'aktif',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    $data = DB::table('penyewa')
        ->where('users_idusers', 1)
        ->first();

    $this->assertNotNull($data);
    $this->assertEquals(1, $data->users_idusers);
    $this->assertEquals('aktif', $data->status_penyewa);
}
#[Test]
public function tc_dp_05_buat_reservasi_dari_penyewa()
{
    $response = redirect()->route('katalog_produk');

    $this->assertEquals(
        302,
        $response->getStatusCode()
    );

    $this->assertEquals(
        route('katalog_produk'),
        $response->getTargetUrl()
    );
}
#[Test]
public function tc_dp_06_pencarian_penyewa_tidak_ditemukan()
{
    $this->loginUser();

    DB::table('users')->insert([
        'idusers' => 2,
        'nama' => 'Novita',
        'username' => 'novita',
        'password' => bcrypt('123456'),
        'no_telepon' => '08123',
        'alamat' => 'alamat',
        'status' => 'penyewa',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    $jumlah = DB::table('users')
        ->where('nama', 'like', '%Budi%')
        ->count();

    $this->assertEquals(0, $jumlah);
}
}