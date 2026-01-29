<?php

use App\Http\Controllers\AkunController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardCabangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribusiProdukController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PermintaanProdukController;
use App\Http\Controllers\ProdukCabangController;
use App\Http\Controllers\ProdukController;
use App\Models\Cabang; 


/* ========== AUTH ========== */
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register_penyewa', function () {
    return view('register_penyewa');
})->name('register.penyewa.form');

/* ================= PROSES REGISTER (POST) ================= */
Route::post('/register_penyewa', [AuthController::class, 'registerPenyewa'])
    ->name('register.penyewa');

/* ================= HALAMAN FORM (GET) ================= */

Route::get('/register_admincabang', function () {
    $cabang = Cabang::all();
    return view('register_admincabang', compact('cabang'));
})->name('register.admin_cabang.form');


/* ================= PROSES REGISTER (POST) ================= */
Route::post('/register_admincabang', [AuthController::class, 'registerAdminCabang'])
    ->name('register.admin_cabang');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

Route::get('/dashboard_cabang', [DashboardController::class, 'dashboardCabang'])
    ->middleware('auth');

// Katalog produk berdasarkan cabang
Route::get('/katalog/{cabang}', [App\Http\Controllers\PenyewaController::class, 'katalog'])
     ->name('katalog_produk');
     
Route::get('/data_penyewa', [PenyewaController::class, 'index'])
    ->middleware('auth');

// tampilkan form tambah penyewa
Route::get('/tambah_penyewa', [PenyewaController::class, 'create'])
    ->name('tambah.penyewa.form');

// simpan data penyewa
Route::post('/tambah_penyewa', [PenyewaController::class, 'store'])
    ->name('tambah_penyewa.store');

Route::get('/profil_cabang', [AkunController::class, 'editcabang'])
    ->name('profil_cabang');

Route::post('/profil_cabang', [AkunController::class, 'profilcabang'])
    ->name('profil.cabang.update');

Route::get('/profil_penyewa', [AkunController::class, 'editpenyewa']);
Route::post('/profil_penyewa', [AkunController::class, 'profilpenyewa'])
    ->name('profil.penyewa.update');

Route::get('/ganti_password', function () {
    return view('ganti_password');
})->name('ganti.password');


/* ========== Kategori ========== */

Route::get('/data_kategori', [KategoriController::class, 'index'])->name('data_kategori');
Route::post('/data_kategori', [KategoriController::class, 'store'])->name('kategori.store');
Route::put('/data_kategori/{id}', [KategoriController::class, 'update'])->name('data_kategori');
Route::delete('/data_kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

/* ========== Produk ========== */
/* ========== PRODUK PUSAT ========== */

// halaman list produk
Route::get('/data_produk', [ProdukController::class, 'index'])
    ->name('data_produk');

// halaman form tambah produk
Route::get('/tambah_produk', [ProdukController::class, 'create'])
    ->name('tambah_produk');

// simpan produk baru
Route::post('/tambah_produk', [ProdukController::class, 'store'])
    ->name('produk.store');

// update produk
// Tampilkan halaman edit produk
Route::get('/edit_produk/{id}', [ProdukController::class, 'edit'])
    ->name('produk.edit');

// Update produk (PUT)
Route::put('/edit_produk/{id}', [ProdukController::class, 'update'])
    ->name('produk.update');

// hapus produk
Route::delete('/data_produk/{id}', [ProdukController::class, 'destroy'])
    ->name('produk.destroy');


// Tampilkan form permintaan
Route::get('/permintaan_alat', [PermintaanProdukController::class, 'create'])
    ->name('permintaan_produk.create')
    ->middleware('auth');

// Simpan permintaan produk
Route::post('/permintaan_alat', [PermintaanProdukController::class, 'store'])
    ->name('permintaan_produk.store')
    ->middleware('auth');

Route::get('/data_permintaan', 
    [PermintaanProdukController::class, 'riwayat']
)->name('data_permintaan');

Route::get('/distribusi_produk', [DistribusiProdukController::class, 'index'])
    ->name('distribusi_produk');

Route::post('/distribusi_produk/kirim', [DistribusiProdukController::class, 'kirimPermintaan'])
    ->name('distribusi_produk.kirim');

Route::match(['get','post'], '/distribusi_produk/terima/{id}',
    [DistribusiProdukController::class, 'terima']
)->name('distribusi_produk.terima');


Route::get('/produk_cabang', [ProdukCabangController::class, 'index'])
     ->name('produk_cabang')
     ->middleware('auth'); // hanya user login (cabang)

// ===============================
// PROSES UPDATE PASSWORD
// ===============================
Route::post(
    '/ganti_password',
    [AkunController::class, 'updatePassword']
)->name('ganti.password.update');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

