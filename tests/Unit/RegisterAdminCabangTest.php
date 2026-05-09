<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Session\Store;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

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

        $request = Request::create('/register-admin', 'POST', [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadminbaru',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12'
        ], [], [
            'gambar_mou' => $file
        ]);

        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);

        $controller = new \App\Http\Controllers\AuthController();

        $response = $controller->registerAdminCabang($request);

        $this->assertEquals(302, $response->getStatusCode());
    }


    /* =========================================
       TC-REG-02
       Field wajib kosong
    ========================================= */
    public function test_tc_reg_02_field_wajib_kosong()
    {
        $this->expectException(ValidationException::class);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $request = Request::create('/register-admin', 'POST', [
            'nama_cabang' => '',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12'
        ], [], [
            'gambar_mou' => $file
        ]);

        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerAdminCabang($request);
    }


    /* =========================================
       TC-REG-03
       Username sudah digunakan
    ========================================= */
    public function test_tc_reg_03_username_sudah_digunakan()
    {
        $this->expectException(ValidationException::class);

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

        $request = Request::create('/register-admin', 'POST', [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12'
        ], [], [
            'gambar_mou' => $file
        ]);

        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerAdminCabang($request);
    }


    /* =========================================
       TC-REG-04
       Nomor telepon tidak valid
    ========================================= */
    public function test_tc_reg_04_nomor_tidak_valid()
    {
        $this->expectException(ValidationException::class);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $request = Request::create('/register-admin', 'POST', [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '08abc123',
            'alamat' => 'Jl. Cluring 12'
        ], [], [
            'gambar_mou' => $file
        ]);

        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerAdminCabang($request);
    }


    /* =========================================
       TC-REG-05
       Password kurang dari 6 karakter
    ========================================= */
    public function test_tc_reg_05_password_kurang()
    {
        $this->expectException(ValidationException::class);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('mou.jpg');

        $request = Request::create('/register-admin', 'POST', [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => '123',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12'
        ], [], [
            'gambar_mou' => $file
        ]);

        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerAdminCabang($request);
    }


    /* =========================================
       TC-REG-06
       MoU tidak diupload
    ========================================= */
    public function test_tc_reg_06_mou_tidak_diupload()
    {
        $this->expectException(ValidationException::class);

        $request = Request::create('/register-admin', 'POST', [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12'
        ]);

        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerAdminCabang($request);
    }


    /* =========================================
       TC-REG-07
       Format file tidak sesuai
    ========================================= */
    public function test_tc_reg_07_format_file_tidak_sesuai()
    {
        $this->expectException(ValidationException::class);

        Storage::fake('public');

        $file = UploadedFile::fake()->create(
            'mou.pdf',
            100,
            'application/pdf'
        );

        $request = Request::create('/register-admin', 'POST', [
            'nama_cabang' => 'OutdoorKriss Glagah',
            'lokasi' => 'Banyuwangi',
            'nama' => 'Putri Novita',
            'username' => 'novitaadmin',
            'password' => 'qwerty12',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Cluring 12'
        ], [], [
            'gambar_mou' => $file
        ]);

        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerAdminCabang($request);
    }
}