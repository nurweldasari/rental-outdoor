<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Session\Store;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper request + session
     */
    private function getRequestWithSession($data)
    {
        $request = Request::create('/register', 'POST', $data);

        $session = new Store('test', new ArraySessionHandler(120));

        $request->setLaravelSession($session);

        return $request;
    }

    /**
     * Helper upload file fake
     */
    private function fakeImage()
    {
        return UploadedFile::fake()->create(
            'ktp.jpg',
            100,
            'image/jpeg'
        );
    }

    #[Test]
    public function tc_reg_01_registrasi_berhasil()
    {
        Storage::fake('public');

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $response = $controller->registerPenyewa($request);

        $this->assertEquals(
            302,
            $response->getStatusCode()
        );

        $this->assertDatabaseHas('users', [
            'username' => 'user'
        ]);
    }

    #[Test]
    public function tc_reg_02_username_kosong()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => '',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_03_password_kosong()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_04_nama_kosong()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => '',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_05_alamat_kosong()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => '',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_06_no_telepon_kosong()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_07_gambar_kosong()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_08_password_kurang_dari_6()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '12345',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_09_nomor_telepon_duplicate()
    {
        Storage::fake('public');

        $controller = new \App\Http\Controllers\AuthController();

        $request1 = $this->getRequestWithSession([
            'nama' => 'User Lama',
            'username' => 'user1',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $request1->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller->registerPenyewa($request1);

        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request2 = $this->getRequestWithSession([
            'nama' => 'User Baru',
            'username' => 'user2',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Bandung',
        ]);

        $request2->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller->registerPenyewa($request2);
    }

    #[Test]
    public function tc_reg_10_nomor_tidak_diawali_08()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '7123456789',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_11_file_bukan_gambar()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $file = UploadedFile::fake()->create(
            'dokumen.pdf',
            100,
            'application/pdf'
        );

        $request->files->set(
            'gambar_identitas',
            $file
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_12_file_lebih_dari_2mb()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '08123456789',
            'alamat' => 'Jakarta',
        ]);

        $file = UploadedFile::fake()->create(
            'ktp.jpg',
            3000,
            'image/jpeg'
        );

        $request->files->set(
            'gambar_identitas',
            $file
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }

    #[Test]
    public function tc_reg_13_nomor_kurang_dari_10_digit()
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        $request = $this->getRequestWithSession([
            'nama' => 'Nur welda',
            'username' => 'user',
            'password' => '123456',
            'no_telepon' => '08123',
            'alamat' => 'Jakarta',
        ]);

        $request->files->set(
            'gambar_identitas',
            $this->fakeImage()
        );

        $controller = new \App\Http\Controllers\AuthController();

        $controller->registerPenyewa($request);
    }
}

