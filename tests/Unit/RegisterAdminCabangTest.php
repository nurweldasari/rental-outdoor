<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterAdminCabangTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tc_reg_01_register_berhasil()
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

    #[Test]
    public function tc_reg_02_username_kosong()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => '',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12',
            'gambar_mou' => $file
        ]);

        $response->assertSessionHasErrors('username');
    }

    #[Test]
    public function tc_reg_03_username_sudah_digunakan()
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
    
    #[Test]
    public function tc_reg_04_nomor_tidak_valid()
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

    #[Test]
    public function tc_reg_05_password_kurang()
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

    #[Test]
    public function tc_reg_06_mou_tidak_diupload()
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

    #[Test]
    public function tc_reg_07_format_file_tidak_sesuai()
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
    
    #[Test]
    public function tc_reg_08_lokasi_kosong()
    {
    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => '',
        'nama' => 'Putri',
        'username' => 'putri1',
        'password' => 'qwerty12',
        'no_telepon' => '081234567890',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('lokasi');
    }

    #[Test]
    public function tc_reg_09_nama_kosong()
   {
    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => 'Banyuwangi',
        'nama' => '',
        'username' => 'putri1',
        'password' => 'qwerty12',
        'no_telepon' => '081234567890',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('nama');
    }
    
    #[Test]
    public function tc_reg_10_password_kosong()
    {
    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => 'Banyuwangi',
        'nama' => 'Putri',
        'username' => 'putri1',
        'password' => '',
        'no_telepon' => '081234567890',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('password');
    }
    
    #[Test]
    public function tc_reg_11_nomor_telepon_sudah_digunakan()
    {
    User::create([
        'nama' => 'User Lama',
        'username' => 'lama',
        'password' => bcrypt('123456'),
        'no_telepon' => '081234567890',
        'alamat' => 'Banyuwangi',
        'status' => 'owner'
    ]);

    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => 'Banyuwangi',
        'nama' => 'Putri',
        'username' => 'putri1',
        'password' => 'qwerty12',
        'no_telepon' => '081234567890',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('no_telepon');
    }
    
    #[Test]
    public function tc_reg_12_nomor_tidak_diawali_08()
   {
    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => 'Banyuwangi',
        'nama' => 'Putri',
        'username' => 'putri1',
        'password' => 'qwerty12',
        'no_telepon' => '71234567890',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('no_telepon');
    }
    
    #[Test]
    public function tc_reg_13_nomor_telepon_kurang_dari_10_digit()
    {
        Storage::fake('public');

        $response = $this->post(route('register.admin_cabang'), [
            'nama_cabang' => 'Cabang A',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri',
            'username' => 'putri1',
            'password' => 'qwerty12',
            'no_telepon' => '081234567',
            'alamat' => 'Banyuwangi',
            'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
        ]);

        $response->assertSessionHasErrors('no_telepon');
    }

    #[Test]
    public function tc_reg_14_alamat_kosong()
   {
    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => 'Banyuwangi',
        'nama' => 'Putri',
        'username' => 'putri1',
        'password' => 'qwerty12',
        'no_telepon' => '081234567890',
        'alamat' => '',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('alamat');
    }

    #[Test]
    public function tc_reg_15_file_lebih_dari_2mb()
    {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('mou.jpg')->size(3000);

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => 'Banyuwangi',
        'nama' => 'Putri',
        'username' => 'putri1',
        'password' => 'qwerty12',
        'no_telepon' => '081234567890',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => $file
    ]);

    $response->assertSessionHasErrors('gambar_mou');
    }
    #[Test]
    public function tc_reg_16_nama_cabang_kosong()
    {
    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => '',
        'lokasi' => 'Banyuwangi',
        'nama' => 'Putri',
        'username' => 'putri1',
        'password' => 'qwerty12',
        'no_telepon' => '081234567890',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('nama_cabang');
    }
    #[Test]
    public function tc_reg_17_nomor_telepon_lebih_dari_13_digit()
    {
    Storage::fake('public');

    $response = $this->post(route('register.admin_cabang'), [
        'nama_cabang' => 'Cabang A',
        'lokasi' => 'Banyuwangi',
        'nama' => 'Putri',
        'username' => 'putri123',
        'password' => 'qwerty12',
        'no_telepon' => '081234567890123',
        'alamat' => 'Banyuwangi',
        'gambar_mou' => UploadedFile::fake()->image('mou.jpg')
    ]);

    $response->assertSessionHasErrors('no_telepon');
    }
}