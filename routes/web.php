<?php

use App\Http\Controllers\AkunController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribusiProdukController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KontrakFranchiseController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PermintaanProdukController;
use App\Http\Controllers\ProdukCabangController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\BagiHasilController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\LandingController;
use App\Models\Cabang; 


Route::get('/', [LandingController::class, 'landing'])
    ->name('landing_penyewa');
Route::get('/landing_page_cabang', function () {
    return view('landing_page_cabang');
})->name('landing_page_cabang');
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

Route::get('/register-admin-cabang', function () {
    $cabang = Cabang::all();
    return view('register_admincabang', compact('cabang'));
})->name('register.admin_cabang.form');


/* ================= PROSES REGISTER (POST) ================= */
Route::post('/register-admin-cabang', [AuthController::class, 'registerAdminCabang'])
    ->name('register.admin_cabang');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

Route::get('/dashboard_cabang', [DashboardController::class, 'dashboardCabang'])
    ->middleware('auth');

Route::get('/dashboard_pusat', [DashboardController::class, 'dashboardPusat'])
    ->middleware('auth');

// klik card cabang
Route::get('/pilih-cabang/{id}', 
    [KatalogController::class, 'pilihCabang']
)->name('pilih.cabang');

Route::get('/pilih-pusat/{id}', 
    [KatalogController::class, 'pilihPusat']
)->name('pilih.pusat');

// halaman katalog produk
Route::get('/katalog-cabang', 
    [KatalogController::class, 'katalogCabang']
)->name('katalog_produk');

// Katalog produk berdasarkan cabang
Route::get('/katalog/{cabang}', [App\Http\Controllers\PenyewaController::class, 'katalog'])
     ->name('katalog_produk');

// halaman katalog produk
Route::get('/katalog-pusat', 
    [KatalogController::class, 'katalogPusat']
)->name('katalog_pusat');

//keranjang cabang
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'update']);
Route::post('/cart/delete', [CartController::class, 'delete']);

Route::post('/cart/add-paket', [CartController::class, 'addPaket']);
Route::post('/cart/update-paket', [CartController::class, 'updatePaket']);
Route::post('/cart/delete-paket', [CartController::class, 'deletePaket']);

//keranjang pusat
Route::post('/cart/add-pusat', [CartController::class, 'addPusat']);
Route::post('/cart/update-pusat', [CartController::class, 'updatePusat']);
Route::post('/cart/delete-pusat', [CartController::class, 'deletePusat']);

Route::post('/cart/add-paket-pusat', [CartController::class, 'addPaketPusat']);
Route::post('/cart/update-paket-pusat', [CartController::class, 'updatePaketPusat']);
Route::post('/cart/delete-paket-pusat', [CartController::class, 'deletePaketPusat']);

Route::post('/penyewaan/store', [PenyewaanController::class, 'store'])
    ->name('penyewaan.store');

Route::get('/penyewaan/{id}', [PenyewaanController::class, 'detail'])
    ->name('penyewaan.detail');
// CABANG
Route::get('/penyewaan', [PenyewaanController::class, 'riwayat'])
    ->name('item_penyewaan');

// PUSAT
Route::post('/penyewaan-pusat/store', [PenyewaanController::class, 'store'])
    ->name('penyewaan_pusat.store');

Route::get('/penyewaan-pusat/{id}', [PenyewaanController::class, 'detail'])
    ->name('penyewaan_pusat.detail');

Route::get('/penyewaan-pusat', [PenyewaanController::class, 'riwayatPusat'])
    ->name('item_penyewaan_pusat');

// Halaman penyewaan selesai
   // CABANG / UMUM
Route::get('/riwayat-penyewaan', [PenyewaanController::class, 'selesai'])
    ->name('riwayat_penyewaan');

// (kalau mau pisah pusat, opsional)
Route::get('/riwayat-penyewaan-pusat', [PenyewaanController::class, 'selesaiPusat'])
    ->name('riwayat_penyewaan_pusat');

Route::get('/detail-sewa-pusat/{id}', 
    [PenyewaanController::class, 'detailPenyewaPusat']
)->name('detail_sewa_pusat');

    // Detail penyewaan
Route::get('/detail-sewa/{id}', 
    [PenyewaanController::class, 'detailPenyewa']
)->name('detail_sewa');


// Upload Bukti Bayar
// Halaman upload bukti bayar
Route::get('/penyewaan/{id}/upload', [PenyewaanController::class, 'uploadPage'])->name('penyewaan.upload_pembayaran');
Route::post('/penyewaan/{id}/upload', [PenyewaanController::class, 'uploadBuktiBayar'])->name('penyewaan.upload_bukti');
Route::get('/penyewaan/{id}', [PenyewaanController::class, 'detail'])
    ->name('penyewaan.detail');


Route::get('/data_penyewaan', [PenyewaanController::class, 'adminIndex'])
    ->name('data_penyewaan');
Route::get('/admin/penyewaan/{id}',
    [PenyewaanController::class, 'adminDetail']
)->name('admin.penyewaan.detail');


     // Konfirmasi pembayaran (untuk status menunggu_pembayaran)
    // Konfirmasi pembayaran (POST) → hanya untuk status menunggu_pembayaran
Route::post('/admin/penyewaan/konfirmasi/{id}', [PenyewaanController::class, 'konfirmasiBayar'])
    ->name('admin.konfirmasi_bayar');
Route::post('/admin/penyewaan/selesai/{id}',
    [PenyewaanController::class, 'selesaiAdmin']
)->name('admin.penyewaan.selesai');

Route::get('/admin/riwayat', 
    [PenyewaanController::class, 'adminRiwayat']
)->name('data_riwayat');

// Cancel penyewaan (POST) → hanya untuk status menunggu_pembayaran
Route::post('/admin/penyewaan/{id}/cancel', [PenyewaanController::class, 'cancel'])
    ->name('admin.penyewaan.cancel');

// ================= PUSAT =================

// halaman data penyewaan pusat
Route::get('/data_penyewaan_pusat', [PenyewaanController::class, 'pusatIndex'])
    ->name('data_penyewaan_pusat');

// detail penyewaan pusat
Route::get('/admin/penyewaan_pusat/{id}',
    [PenyewaanController::class, 'pusatDetail']
)->name('pusat.penyewaan.detail');

// konfirmasi pembayaran pusat
Route::post('/penyewaan_pusat/konfirmasi/{id}', 
    [PenyewaanController::class, 'konfirmasiPusat']
)->name('pusat.konfirmasi_bayar');

// selesai penyewaan pusat
Route::post('/penyewaan_pusat/selesai/{id}',
    [PenyewaanController::class, 'selesaiAdminPusat']
)->name('pusat.penyewaan.selesai');

// riwayat pusat
Route::get('/riwayat_pusat', 
    [PenyewaanController::class, 'pusatRiwayat']
)->name('data_riwayat_pusat');

// cancel pusat
Route::post('/penyewaan_pusat/{id}/cancel', 
    [PenyewaanController::class, 'cancelPusat']
)->name('pusat.penyewaan.cancel');

Route::get('/penyewaan_pusat/{id}/upload', [PenyewaanController::class, 'uploadPusat'])->name('penyewaan_pusat.upload_pembayaran');
Route::post('/penyewaan_pusat/{id}/upload', [PenyewaanController::class, 'uploadBuktiBayarPusat'])->name('penyewaan_pusat.upload_bukti');
Route::get('/penyewaan_pusat/{id}', [PenyewaanController::class, 'detailPenyewaPusat'])
    ->name('penyewaan_pusat.detail');

Route::middleware('auth')->group(function () {

    // ADMIN CABANG
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->name('laporan');

    // OWNER
    Route::get('/laporan-cabang', [LaporanController::class, 'index'])
        ->name('laporan_cabang');

     // ADMIN CABANG
    Route::get('/laporan-pusat', [LaporanController::class, 'laporanPusat'])
        ->name('laporan_pusat');

});

Route::get('/data_penyewa', [PenyewaController::class, 'index'])
    ->middleware('auth');

// tampilkan form tambah penyewa
Route::get('/tambah_penyewa', [PenyewaController::class, 'create'])
    ->name('tambah.penyewa.form');

// simpan data penyewa
Route::post('/tambah_penyewa', [PenyewaController::class, 'store'])
    ->name('tambah_penyewa.store');

  Route::get('/admin/reservasi/{idpenyewa}', 
        [PenyewaanController::class, 'createReservasi']
    )->name('reservasi');

    // 🔥 SIMPAN RESERVASI (POST)
    Route::post('/admin/reservasi/{idpenyewa}', 
        [PenyewaanController::class, 'reservasi']
    )->name('reservasi.store');

//pusat
Route::get('/data_penyewa_pusat', [PenyewaController::class, 'indexPusat'])
    ->middleware('auth');

// tampilkan form tambah penyewa
Route::get('/tambah_penyewa_pusat', [PenyewaController::class, 'createPusat'])
    ->name('tambah.penyewa.pusat');

// simpan data penyewa
Route::post('/tambah_penyewa_pusat', [PenyewaController::class, 'storePusat'])
    ->name('tambah_penyewa.store');

  Route::get('/admin/reservasi_pusat/{idpenyewa}', 
        [PenyewaanController::class, 'createReservasiPusat']
    )->name('reservasi_pusat');

    // 🔥 SIMPAN RESERVASI (POST)
    Route::post('/admin/reservasi_pusat/{idpenyewa}', 
        [PenyewaanController::class, 'reservasiPusat']
    )->name('reservasi_pusat.store');


// Konfirmasi
Route::post('/penyewa/terima/{id}', [PenyewaController::class, 'terimaPusat'])->name('penyewa.terima');
Route::post('/penyewa/tolak/{id}', [PenyewaController::class, 'tolakPusat'])->name('penyewa.tolak');

// Toggle status (reload page)
Route::post('/penyewa/toggle/{id}', [PenyewaController::class, 'toggleStatusPusat'])->name('penyewa.toggle');



Route::get('/profil_cabang', [AkunController::class, 'editcabang'])
    ->name('profil_cabang');

Route::post('/profil_cabang', [AkunController::class, 'profilcabang'])
    ->name('profil.cabang.update');

Route::get('/profil', [AkunController::class, 'edit']);
Route::post('/profil', [AkunController::class, 'profil'])
    ->name('profil.update');

Route::get('/ganti_password', function () {
    return view('ganti_password');
})->name('ganti.password');

Route::get('/rekening', [AkunController::class, 'editrekening'])
    ->name('rekening');

Route::post('/rekening', [AkunController::class, 'updateRekening'])
    ->name('rekening.update');

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
    [DistribusiProdukController::class, 'terima'])->name('distribusi_produk.terima');

Route::get('/distribusi_produk', [DistribusiProdukController::class, 'index'])
    ->name('distribusi_produk');


Route::get('/produk_cabang', [ProdukCabangController::class, 'index'])
     ->name('produk_cabang')
     ->middleware('auth'); // hanya user login (cabang)

Route::post(
    '/produk_cabang/toggle/{idstok}',
    [ProdukCabangController::class, 'toggleStatus']
)->name('produk_cabang.toggle');


//Cabang
Route::get('/cabang', [CabangController::class, 'index'])
    ->middleware('auth');

//Kontrak_Franchise
Route::get('/kontrak_franchise', [KontrakFranchiseController::class, 'index'])->middleware('auth');

// Konfirmasi
Route::post('/cabang/terima/{id}', [CabangController::class, 'terima'])->name('cabang.terima');
Route::post('/cabang/tolak/{id}', [CabangController::class, 'tolak'])->name('cabang.tolak');

// Toggle status (reload page)
Route::post('/cabang/toggle/{id}', [CabangController::class, 'toggleStatus'])->name('cabang.toggle');

// Konfirmasi
Route::post('/penyewa/terima/{id}', [PenyewaController::class, 'terima'])->name('penyewa.terima');
Route::post('/penyewa/tolak/{id}', [PenyewaController::class, 'tolak'])->name('penyewa.tolak');

// Toggle status (reload page)
Route::post('/penyewa/toggle/{id}', [PenyewaController::class, 'toggleStatus'])->name('penyewa.toggle');

Route::get('/tambah_rekening', function () {
   return view('tambah_rekening');
});

Route::post('/tambah_rekening', [RekeningController::class, 'store'])
    ->name('rekening.store');

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

Route::get('/cek-waktu', function () {
    dd(
        now()->toDateTimeString(),
        now()->timezoneName
    );
});

// ===== OWNER BAGI HASIL =====

// Halaman utama + menu view (list / bukti / riwayat / pengaturan)
Route::get('/bagi-hasil', [BagiHasilController::class, 'index'])
    ->name('bagi_hasil');

// Detail hitung per cabang
Route::get('/bagi-hasil/detail/{id}', [BagiHasilController::class, 'show'])
    ->name('bagi_hasil.detail');

// Simpan hasil perhitungan
Route::post('/bagi-hasil/store', [BagiHasilController::class, 'store'])
    ->name('bagi_hasil.store');

// ================= ADMIN CABANG =================
Route::get('/bagi-hasil-cabang', [BagiHasilController::class,'cabangIndex'])
    ->name('bagi_hasil.cabang');

Route::post('/bagi-hasil/upload/{id}', [BagiHasilController::class,'uploadBukti'])
    ->name('bagi_hasil.upload');

Route::post('/bagi-hasil/{id}/konfirmasi',
[BagiHasilController::class,'konfirmasi'])
->name('bagi_hasil.konfirmasi');

Route::post('/bagi-hasil/{id}/tolak',
[BagiHasilController::class,'tolak'])
->name('bagi_hasil.tolak');

Route::post('/kirim-otp', [AuthController::class, 'kirimOtp']);
Route::post('/verifikasi-otp', [AuthController::class, 'verifikasiOtp']);

Route::get('/reset-password', function () {
    return view('login');
});

Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::get('/paket_cabang', [PaketController::class, 'create'])->name('paket_cabang');
Route::post('/paket/store', [PaketController::class, 'store'])->name('paket.store');
Route::get('/paket/{id}/edit', [PaketController::class, 'edit'])->name('paket.edit');
Route::put('/paket/{id}', [PaketController::class, 'update'])->name('paket.update');
Route::delete('/paket/{id}', [PaketController::class, 'destroy'])->name('paket.destroy');

Route::get('/paket_pusat', [PaketController::class, 'createPusat'])->name('paket_pusat');
Route::post('/paket_pusat/store', [PaketController::class, 'storePusat'])->name('paket_pusat.store');
Route::get('/paket_pusat/{id}/edit', [PaketController::class, 'editPusat'])->name('paket_pusat.edit');
Route::put('/paket_pusat/{id}', [PaketController::class, 'updatePusat'])->name('paket_pusat.update');
Route::delete('/paket_pusat/{id}', [PaketController::class, 'destroyPusat'])->name('paket_pusat.destroy');