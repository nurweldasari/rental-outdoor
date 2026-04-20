<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterAdminCabangTest extends TestCase
{
    use RefreshDatabase;

    /* ================= TC-REG-01 ================= */
    public function test_register_admin_cabang_success()
    {
        Storage::fake('public');

        $response = $this->post('/register-admin-cabang', [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin123',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => 'novitaadmin123'
        ]);
    }

    /* ================= TC-REG-02 ================= */
    public function test_register_field_required()
    {
        $response = $this->post('/register-admin-cabang', [
            'nama_cabang' => ''
        ]);

        $response->assertSessionHasErrors('nama_cabang');
    }

    /* ================= TC-REG-03 ================= */
    public function test_register_username_duplicate()
    {
        User::factory()->create([
            'username' => 'novitaadmin'
        ]);

        $response = $this->post('/register-admin-cabang', [
            'nama_cabang' => 'Cabang A',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Alamat',
            'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
        ]);

        $response->assertSessionHasErrors('username');
    }

    /* ================= TC-REG-04 ================= */
    public function test_no_telepon_invalid()
    {
        Storage::fake('public');

        $response = $this->post('/register-admin-cabang', [
            'nama_cabang' => 'Cabang A',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri',
            'username' => 'user123',
            'password' => 'qwerty12',
            'no_telepon' => '08abc123',
            'alamat' => 'Alamat',
            'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
        ]);

        $response->assertSessionHasErrors('no_telepon');
    }

    /* ================= TC-REG-05 ================= */
    public function test_password_less_than_6()
    {
        Storage::fake('public');

        $response = $this->post('/register-admin-cabang', [
            'nama_cabang' => 'Cabang A',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri',
            'username' => 'user123',
            'password' => '123',
            'no_telepon' => '081234567890',
            'alamat' => 'Alamat',
            'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
        ]);

        $response->assertSessionHasErrors('password');
    }

    /* ================= TC-REG-06 ================= */
    public function test_mou_not_uploaded()
    {
        $response = $this->post('/register-admin-cabang', [
            'nama_cabang' => 'Cabang A',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri',
            'username' => 'user123',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Alamat'
        ]);

        $response->assertSessionHasErrors('gambar_mou');
    }

    /* ================= TC-REG-07 ================= */
    public function test_mou_invalid_format()
    {
        Storage::fake('public');

        $response = $this->post('/register-admin-cabang', [
            'nama_cabang' => 'Cabang A',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri',
            'username' => 'user123',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Alamat',
            'gambar_mou' => UploadedFile::fake()->create('mou.pdf', 100)
        ]);

        $response->assertSessionHasErrors('gambar_mou');
    }
}