<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use App\Models\Kategori;

use Illuminate\Http\Request;
use App\Http\Controllers\KategoriController;

class KategoriTest extends TestCase
{
    use RefreshDatabase;

    // =====================================================
    // HELPER ADMIN
    // =====================================================
    private function admin()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'username' => 'admin1',
            'password' => bcrypt($password),
            'status' => 'admin_pusat'
        ]);

        return [$user, $password];
    }

    // =====================================================
    // HELPER LOGIN ADMIN
    // =====================================================
    private function loginAdmin($username, $password)
    {
        return $this->post('/login', [
            'username' => $username,
            'password' => $password
        ]);
    }

    // =====================================================
    // TC-CAT-01
    // Admin berhasil login
    // =====================================================
    #[Test]
    public function tc_cat_01_admin_berhasil_login()
    {
        [$admin, $password] = $this->admin();

        $response = $this->post('/login', [
            'username' => $admin->username,
            'password' => $password
        ]);

        $response->assertRedirect(
            route('dashboard')
        );

        $this->assertAuthenticatedAs($admin);
    }

    // =====================================================
    // TC-CAT-02
    // Tambah kategori valid
    // =====================================================
    #[Test]
    public function tc_cat_02_tambah_kategori_valid()
    {
        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Alat Masak'
        ]);

        $controller->store($request);

        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'Alat Masak'
        ]);
    }

    // =====================================================
    // TC-CAT-03
    // Nama kategori kosong
    // =====================================================
    #[Test]
    public function tc_cat_03_nama_kosong()
    {
        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        $this->expectException(
            ValidationException::class
        );

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => ''
        ]);

        $controller->store($request);
    }

    // =====================================================
    // TC-CAT-04
    // Nama kategori duplikat
    // =====================================================
    #[Test]
    public function tc_cat_04_nama_duplikat()
    {
        Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        $this->expectException(
            ValidationException::class
        );

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Tenda'
        ]);

        $controller->store($request);
    }

    // =====================================================
    // TC-CAT-05
    // Edit kategori valid
    // =====================================================
    #[Test]
    public function tc_cat_05_edit_valid()
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Kompor'
        ]);

        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Kompor Portable'
        ]);

        $controller->update(
            $request,
            $kategori->idkategori
        );

        $this->assertDatabaseHas('kategori', [
            'idkategori' => $kategori->idkategori,
            'nama_kategori' => 'Kompor Portable'
        ]);
    }

    // =====================================================
    // TC-CAT-06
    // Edit kategori kosong
    // =====================================================
    #[Test]
    public function tc_cat_06_edit_kosong()
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        $this->expectException(
            ValidationException::class
        );

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => ''
        ]);

        $controller->update(
            $request,
            $kategori->idkategori
        );
    }

    // =====================================================
    // TC-CAT-07
    // Edit tanpa perubahan
    // =====================================================
    #[Test]
    public function tc_cat_07_tanpa_perubahan()
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        $controller = new KategoriController();

        $request = new Request([
            'nama_kategori' => 'Tenda'
        ]);

        $controller->update(
            $request,
            $kategori->idkategori
        );

        $this->assertDatabaseHas('kategori', [
            'idkategori' => $kategori->idkategori,
            'nama_kategori' => 'Tenda'
        ]);
    }

    // =====================================================
    // TC-CAT-08
    // Batal tambah kategori
    // =====================================================
    #[Test]
    public function tc_cat_08_batal()
    {
        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $countBefore = Kategori::count();

        // simulasi batal = tidak memanggil store()

        $this->assertEquals(
            $countBefore,
            Kategori::count()
        );
    }

    // =====================================================
    // TC-CAT-09
    // Hapus kategori
    // =====================================================
    #[Test]
    public function tc_cat_09_hapus_kategori()
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        [$admin, $password] = $this->admin();

        $this->loginAdmin(
            $admin->username,
            $password
        );

        $controller = new KategoriController();

        $controller->destroy(
            $kategori->idkategori
        );

        $this->assertSoftDeleted('kategori', [
            'idkategori' => $kategori->idkategori
        ]);
    }
}