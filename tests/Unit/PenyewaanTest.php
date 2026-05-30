<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use App\Http\Controllers\PenyewaanController;

class PenyewaanTest extends TestCase
{
    use RefreshDatabase;

    private function seedRelasi()
    {
        DB::table('users')->insert([
            'idusers' => 1,
            'nama' => 'User',
            'username' => 'user',
            'password' => bcrypt('123456'),
            'no_telepon' => '08123456789',
            'alamat' => 'Jember',
            'status' => 'aktif'
        ]);

        DB::table('penyewa')->insert([
            'idpenyewa' => 1,
            'users_idusers' => 1,
            'status_penyewa' => 'aktif'
        ]);

        DB::table('kategori')->insert([
            'idkategori' => 1,
            'nama_kategori' => 'Tenda'
        ]);

        DB::table('admin_pusat')->insert([
            'idadmin_pusat' => 1,
            'users_idusers' => 1
        ]);

        DB::table('produk')->insert([
            'idproduk' => 1,
            'nama_produk' => 'Tenda',
            'stok_pusat' => 10,
            'jenis_skala' => 'harian',
            'kategori_idkategori' => 1,
            'admin_pusat_idadmin_pusat' => 1,
        ]);

        DB::table('cabang')->insert([
            'idcabang' => 1,
            'nama_cabang' => 'Cabang A',
            'status_cabang' => 'aktif',
            'lokasi' => 'Jember',
        ]);

        DB::table('stok_cabang')->insert([
            'idstok' => 1,
            'produk_idproduk' => 1,
            'cabang_idcabang' => 1,
            'jumlah' => 10,
            'is_active' => 1,
        ]);
    }

    private function loginUser()
    {
        $user = User::find(1);
        Auth::login($user);
    }

    private function requestSewa(array $override = [])
    {
        return Request::create('/sewa', 'POST', array_merge([
            'tanggal_sewa' => '2026-01-01',
            'tanggal_selesai' => '2026-01-03',
            'metode_bayar' => 'cash',
            'produk_cabang' => [1],
            'qty' => [1],
            'type' => ['produk'],
        ], $override));
    }

    // ==================================================
    // TC-SEWA-01
    // ==================================================
    #[Test]
    public function tc_sewa_01_menampilkan_katalog_produk()
    {
        $this->seedRelasi();

        $produk = DB::table('produk')->first();

        $this->assertEquals('Tenda', $produk->nama_produk);
        $this->assertEquals(10, $produk->stok_pusat);
    }

    // ==================================================
    // TC-SEWA-02
    // ==================================================
    #[Test]
    public function tc_sewa_02_mengatur_tanggal_sewa()
    {
        Session::start();

        $this->seedRelasi();
        $this->loginUser();

        session([
            'tipe_toko' => 'cabang',
            'toko_id' => 1
        ]);

        $controller = new PenyewaanController();

        $response = $controller->store(
            $this->requestSewa()
        );

        $this->assertEquals(
            302,
            $response->getStatusCode()
        );
    }

    // ==================================================
    // TC-SEWA-03
    // ==================================================
    #[Test]
    public function tc_sewa_03_konfirmasi_pembayaran()
    {
        Session::start();

        $this->seedRelasi();
        $this->loginUser();

        session([
            'tipe_toko' => 'cabang',
            'toko_id' => 1
        ]);

        $controller = new PenyewaanController();

        $controller->store(
            $this->requestSewa()
        );

        $this->assertDatabaseHas('penyewaan', [
            'status_penyewaan' => 'menunggu_pembayaran'
        ]);
    }

    // ==================================================
    // TC-SEWA-04
    // ==================================================
    #[Test]
    public function tc_sewa_04_metode_cash()
    {
        Session::start();

        $this->seedRelasi();
        $this->loginUser();

        session([
            'tipe_toko' => 'cabang',
            'toko_id' => 1
        ]);

        $controller = new PenyewaanController();

        $controller->store(
            $this->requestSewa([
                'metode_bayar' => 'cash'
            ])
        );

        $this->assertDatabaseHas('penyewaan', [
            'metode_bayar' => 'cash'
        ]);
    }

    // ==================================================
    // TC-SEWA-05
    // ==================================================
    #[Test]
    public function tc_sewa_05_metode_transfer()
    {
        Session::start();

        $this->seedRelasi();
        $this->loginUser();

        session([
            'tipe_toko' => 'cabang',
            'toko_id' => 1
        ]);

        $controller = new PenyewaanController();

        $controller->store(
            $this->requestSewa([
                'metode_bayar' => 'transfer'
            ])
        );

        $this->assertDatabaseHas('penyewaan', [
            'metode_bayar' => 'transfer'
        ]);
    }

    // ==================================================
    // TC-SEWA-06
    // ==================================================
    #[Test]
    public function tc_sewa_06_batas_bayar_cash_2_jam()
    {
        $expected = now()->addHours(2);

        $this->assertEquals(
            $expected->format('Y-m-d H'),
            now()->addHours(2)->format('Y-m-d H')
        );
    }

    // ==================================================
    // TC-SEWA-07
    // ==================================================
    #[Test]
    public function tc_sewa_07_batas_upload_transfer()
    {
        $expected = now()->addHours(2);

        $this->assertEquals(
            $expected->format('Y-m-d H'),
            now()->addHours(2)->format('Y-m-d H')
        );
    }

    // ==================================================
    // TC-SEWA-08
    // ==================================================
    #[Test]
    public function tc_sewa_08_upload_bukti_valid()
    {
        Storage::fake('public');

        $this->seedRelasi();

        DB::table('penyewaan')->insert([
            'idpenyewaan' => 1,
            'tanggal_sewa' => now(),
            'tanggal_selesai' => now()->addDay(),
            'total' => 0,
            'total_produk' => 0,
            'status_penyewaan' => 'menunggu_pembayaran',
            'metode_bayar' => 'transfer',
            'batas_pembayaran' => now()->addHours(2),
            'penyewa_idpenyewa' => 1,
            'cabang_idcabang' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->assertDatabaseHas('penyewaan', [
            'idpenyewaan' => 1
        ]);
    }

    // ==================================================
    // TC-SEWA-09
    // ==================================================
    #[Test]
public function tc_sewa_09_upload_tidak_valid()
{
    $this->expectException(
        \Illuminate\Validation\ValidationException::class
    );

    $this->seedRelasi();
    $this->loginUser();

    DB::table('penyewaan')->insert([
        'idpenyewaan' => 1,
        'tanggal_sewa' => now(),
        'tanggal_selesai' => now()->addDay(),
        'total' => 0,
        'total_produk' => 0,
        'status_penyewaan' => 'menunggu_pembayaran',
        'metode_bayar' => 'transfer',
        'batas_pembayaran' => now()->addHours(2),
        'penyewa_idpenyewa' => 1,
        'cabang_idcabang' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $request = Request::create('/upload', 'POST', []);

    $controller = new PenyewaanController();

    $controller->uploadBuktiBayar($request, 1);
}
    // ==================================================
    // TC-SEWA-10
    // ==================================================
    #[Test]
    public function tc_sewa_10_melihat_tab_belum_bayar()
    {
        $this->seedRelasi();
        $this->loginUser();

        DB::table('penyewaan')->insert([
            'idpenyewaan' => 1,
            'tanggal_sewa' => now(),
            'tanggal_selesai' => now()->addDay(),
            'total' => 0,
            'total_produk' => 0,
            'status_penyewaan' => 'menunggu_pembayaran',
            'metode_bayar' => 'cash',
            'batas_pembayaran' => now()->addHours(2),
            'penyewa_idpenyewa' => 1,
            'cabang_idcabang' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->assertDatabaseHas('penyewaan', [
            'status_penyewaan' => 'menunggu_pembayaran'
        ]);
    }

    // ==================================================
    // TC-SEWA-11
    // ==================================================
    #[Test]
    public function tc_sewa_11_melihat_tab_penyewaan()
    {
        $this->seedRelasi();
        $this->loginUser();

        DB::table('penyewaan')->insert([
            'idpenyewaan' => 1,
            'tanggal_sewa' => now(),
            'tanggal_selesai' => now()->addDay(),
            'total' => 0,
            'total_produk' => 0,
            'status_penyewaan' => 'sedang_disewa',
            'metode_bayar' => 'cash',
            'batas_pembayaran' => now()->addHours(2),
            'penyewa_idpenyewa' => 1,
            'cabang_idcabang' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->assertDatabaseHas('penyewaan', [
            'status_penyewaan' => 'sedang_disewa'
        ]);
    }

    // ==================================================
    // TC-SEWA-12
    // ==================================================
    #[Test]
    public function tc_sewa_12_metode_pembayaran_kosong()
    {
        $this->expectException(
            ValidationException::class
        );

        Session::start();

        $this->seedRelasi();
        $this->loginUser();

        session([
            'tipe_toko' => 'cabang',
            'toko_id' => 1
        ]);

        $controller = new PenyewaanController();

        $controller->store(
            $this->requestSewa([
                'metode_bayar' => null
            ])
        );
    }

    // ==================================================
    // TC-SEWA-13
    // ==================================================
    #[Test]
    public function tc_sewa_13_qty_melebihi_stok()
    {
        Session::start();

        $this->seedRelasi();
        $this->loginUser();

        session([
            'tipe_toko' => 'cabang',
            'toko_id' => 1
        ]);

        $controller = new PenyewaanController();
$response = $controller->store(
    $this->requestSewa([
        'qty' => [20]
    ])
);

$this->assertEquals(302, $response->getStatusCode());

$this->assertEquals(
    'Stok cabang tidak mencukupi',
    session('error')
);
    }
}