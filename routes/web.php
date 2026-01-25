<?php

use App\Http\Controllers\AkunController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardCabangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenyewaController;
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

Route::get('/data_penyewa', [PenyewaController::class, 'index'])
    ->middleware('auth');

// tampilkan form tambah penyewa
Route::get('/tambah_penyewa', [PenyewaController::class, 'create'])
    ->name('tambah.penyewa.form');

// simpan data penyewa
Route::post('/tambah_penyewa', [PenyewaController::class, 'store'])
    ->name('tambah_penyewa.store');

Route::get('/profil_cabang', [AkunController::class, 'editcabang'])
    ->name('profil.cabang');

Route::post('/profil_cabang', [AkunController::class, 'profilcabang'])
    ->name('profil.cabang.update');

Route::get('/profil_penyewa', [AkunController::class, 'editpenyewa']);
Route::post('/profil_penyewa', [AkunController::class, 'profilpenyewa'])
    ->name('profil.penyewa.update');

Route::get('/ganti_password', function () {
    return view('ganti_password');
})->name('ganti.password');


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