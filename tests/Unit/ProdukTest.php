<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\AdminPusat;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProdukTest extends TestCase
{
    use RefreshDatabase;

    // =====================================================
    // HELPER ADMIN PUSAT
    // =====================================================
    private function adminPusat()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'username' => 'admin1',
            'password' => bcrypt($password),
            'status' => 'admin_pusat'
        ]);

        $admin = AdminPusat::factory()->create([
            'users_idusers' => $user->idusers
        ]);

        return [$user, $admin, $password];
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
    // TC-PROD-01
    // Admin berhasil login
    // =====================================================
    #[Test]
    public function tc_prod_01_admin_berhasil_login()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => $password
        ]);

        $response->assertRedirect(
            route('dashboard')
        );

        $this->assertAuthenticatedAs($user);
    }

    // =====================================================
    // TC-PROD-02
    // Menampilkan halaman produk
    // =====================================================
    #[Test]
    public function tc_prod_02_menampilkan_halaman_produk()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $response = $this->get(
            route('data_produk')
        );

        $response->assertViewIs(
            'data_produk'
        );

        $response->assertViewHas(
            'produk'
        );
    }

    // =====================================================
    // TC-PROD-03
    // Menampilkan form tambah produk
    // =====================================================
    #[Test]
    public function tc_prod_03_menampilkan_form_tambah_produk()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $response = $this->get(
            route('tambah_produk')
        );

        $response->assertViewIs(
            'tambah_produk'
        );

        $response->assertViewHas(
            'kategori'
        );
    }

    // =====================================================
    // TC-PROD-04
    // Tambah produk berhasil
    // =====================================================
    #[Test]
    public function tc_prod_04_tambah_produk_berhasil()
    {
        Storage::fake('public');

        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $response = $this->post(
            route('produk.store'),
            [
                'nama_produk' => 'Tenda Dome',
                'stok_pusat' => 10,
                'harga' => 500000,
                'jenis_skala' => 'Per Hari',
                'kategori_idkategori' => $kategori->idkategori,
                'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
                'gambar_produk' => UploadedFile::fake()->image('produk.jpg')
            ]
        );

        $response->assertRedirect(
            route('data_produk')
        );

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Tenda Dome'
        ]);
    }

    // =====================================================
    // TC-PROD-05
    // Nama produk kosong
    // =====================================================
    #[Test]
    public function tc_prod_05_nama_produk_kosong()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $response = $this->post(
            route('produk.store'),
            [
                'nama_produk' => '',
                'stok_pusat' => 10,
                'harga' => 500000,
                'jenis_skala' => 'Per Hari',
                'kategori_idkategori' => $kategori->idkategori,
                'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            ]
        );

        $response->assertSessionHasErrors(
            'nama_produk'
        );
    }

    // =====================================================
    // TC-PROD-06
    // Stok non numerik
    // =====================================================
    #[Test]
    public function tc_prod_06_stok_non_numerik()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $response = $this->post(
            route('produk.store'),
            [
                'nama_produk' => 'Tenda',
                'stok_pusat' => 'abc',
                'harga' => 500000,
                'jenis_skala' => 'Per Hari',
                'kategori_idkategori' => $kategori->idkategori,
                'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            ]
        );

        $response->assertSessionHasErrors(
            'stok_pusat'
        );
    }

    // =====================================================
    // TC-PROD-07
    // Harga non numerik
    // =====================================================
    #[Test]
    public function tc_prod_07_harga_non_numerik()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $response = $this->post(
            route('produk.store'),
            [
                'nama_produk' => 'Tenda',
                'stok_pusat' => 10,
                'harga' => 'abc',
                'jenis_skala' => 'Per Hari',
                'kategori_idkategori' => $kategori->idkategori,
                'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            ]
        );

        $response->assertSessionHasErrors(
            'harga'
        );
    }

    // =====================================================
    // TC-PROD-08
    // File bukan gambar
    // =====================================================
    #[Test]
    public function tc_prod_08_file_bukan_gambar()
    {
        Storage::fake('public');

        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $response = $this->post(
            route('produk.store'),
            [
                'nama_produk' => 'Tenda',
                'stok_pusat' => 10,
                'harga' => 500000,
                'jenis_skala' => 'Per Hari',
                'kategori_idkategori' => $kategori->idkategori,
                'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
                'gambar_produk' => UploadedFile::fake()->create(
                    'dokumen.pdf',
                    100,
                    'application/pdf'
                )
            ]
        );

        $response->assertSessionHasErrors(
            'gambar_produk'
        );
    }

    // =====================================================
    // TC-PROD-09
    // File lebih dari 2 MB
    // =====================================================
    #[Test]
    public function tc_prod_09_file_lebih_dari_2mb()
    {
        Storage::fake('public');

        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $response = $this->post(
            route('produk.store'),
            [
                'nama_produk' => 'Tenda',
                'stok_pusat' => 10,
                'harga' => 500000,
                'jenis_skala' => 'Per Hari',
                'kategori_idkategori' => $kategori->idkategori,
                'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
                'gambar_produk' => UploadedFile::fake()
                    ->image('produk.jpg')
                    ->size(3000)
            ]
        );

        $response->assertSessionHasErrors(
            'gambar_produk'
        );
    }

    // =====================================================
    // TC-PROD-10
    // Menampilkan form edit produk
    // =====================================================
    #[Test]
    public function tc_prod_10_menampilkan_form_edit_produk()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $produk = Produk::create([
            'nama_produk' => 'Tenda Dome',
            'stok_pusat' => 10,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $response = $this->get(
            route('produk.edit', $produk->idproduk)
        );

        $response->assertViewIs(
            'edit_produk'
        );

        $response->assertViewHas(
            'produk'
        );
    }

    // =====================================================
    // TC-PROD-11
    // Update produk berhasil
    // =====================================================
    #[Test]
    public function tc_prod_11_update_produk_berhasil()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $produk = Produk::create([
            'nama_produk' => 'Tenda Lama',
            'stok_pusat' => 5,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $response = $this->put(
            route('produk.update', $produk->idproduk),
            [
                'nama_produk' => 'Tenda Baru',
                'stok_pusat' => 20,
                'harga' => 750000,
                'jenis_skala' => 'Per Hari',
                'kategori_idkategori' => $kategori->idkategori,
                'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
            ]
        );

        $response->assertRedirect(
            route('data_produk')
        );

        $this->assertDatabaseHas('produk', [
            'idproduk' => $produk->idproduk,
            'nama_produk' => 'Tenda Baru',
            'stok_pusat' => 20
        ]);
    }

    // =====================================================
    // TC-PROD-12
    // Hapus produk berhasil
    // =====================================================
    #[Test]
    public function tc_prod_12_hapus_produk_berhasil()
    {
        [$user, $admin, $password] = $this->adminPusat();

        $this->loginAdmin(
            $user->username,
            $password
        );

        $kategori = Kategori::create([
            'nama_kategori' => 'Tenda'
        ]);

        $produk = Produk::create([
            'nama_produk' => 'Tenda Hapus',
            'stok_pusat' => 5,
            'jenis_skala' => 'Per Hari',
            'kategori_idkategori' => $kategori->idkategori,
            'admin_pusat_idadmin_pusat' => $admin->idadmin_pusat,
        ]);

        $response = $this->delete(
            route('produk.destroy', $produk->idproduk)
        );

        $response->assertRedirect(
            route('data_produk')
        );

        $this->assertSoftDeleted('produk', [
            'idproduk' => $produk->idproduk
        ]);
    }
}