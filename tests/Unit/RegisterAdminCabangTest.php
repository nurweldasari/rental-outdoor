<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterAdminCabangTest extends TestCase
{
    use RefreshDatabase;

    /* =========================================
       TC-REG-01
       Registrasi berhasil
    ========================================= */
    public function test_tc_reg_01_register_berhasil()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadminbaru',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => $file
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'username' => 'novitaadminbaru'
        ]);

        $this->assertDatabaseHas('cabang', [
            'nama_cabang' => 'OutdoorKriss Glagah'
        ]);
    }

    /* =========================================
       TC-REG-02
       Field wajib kosong
    ========================================= */
    public function test_tc_reg_02_field_wajib_kosong()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => '',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => $file
        ]);

        $response->assertSessionHasErrors('nama_cabang');
    }

    /* =========================================
       TC-REG-03
       Username sudah digunakan
    ========================================= */
    public function test_tc_reg_03_username_sudah_digunakan()
    {
        User::create([
            'nama' => 'User Lama',
            'username' => 'novitaadmin',
            'password' => bcrypt('123456'),
            'no_telepon' => '081111111111',
            'alamat' => 'Banyuwangi',
            'status' => 'owner'
        ]);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => $file
        ]);

        $response->assertSessionHasErrors('username');
    }

    /* =========================================
       TC-REG-04
       Nomor telepon tidak valid
    ========================================= */
    public function test_tc_reg_04_nomor_tidak_valid()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '08abc123',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => $file
        ]);

        $response->assertSessionHasErrors('no_telepon');
    }

    /* =========================================
       TC-REG-05
       Password kurang dari 6 karakter
    ========================================= */
    public function test_tc_reg_05_password_kurang()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => '123',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => $file
        ]);

        $response->assertSessionHasErrors('password');
    }

    /* =========================================
       TC-REG-06
       MoU tidak diupload
    ========================================= */
    public function test_tc_reg_06_mou_tidak_diupload()
    {
        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12'
        ]);

        $response->assertSessionHasErrors('gambar_mou');
    }

    /* =========================================
       TC-REG-07
       Format file tidak sesuai
    ========================================= */
    public function test_tc_reg_07_format_file_tidak_sesuai()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create(
            'mou.pdf',
            100,
            'application/pdf'
        );

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => $file
        ]);

        $response->assertSessionHasErrors('gambar_mou');
    }
}