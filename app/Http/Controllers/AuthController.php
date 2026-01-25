<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Penyewa;
use App\Models\AdminCabang;
use App\Models\AdminPusat;
use App\Models\Owner;
use App\Models\Cabang; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* ================= LOGIN ================= */
    public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    if (Auth::attempt($request->only('username','password'))) {

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'username' => 'Username atau password salah'
    ]);
}


    /* ================= REGISTER PENYEWA ================= */
    public function registerPenyewa(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'no_telepon' => 'required',
            'alamat' => 'required',
            'gambar_identitas' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

       /* ================= SIMPAN USER ================= */
    $user = User::create([
        'nama'        => $request->nama,
        'username'    => $request->username,
        'password'    => Hash::make($request->password),
        'no_telepon'  => $request->no_telepon,
        'alamat'      => $request->alamat,
        'status'      => 'penyewa'
    ]);

    /* ================= UPLOAD GAMBAR ================= */
    $file     = $request->file('gambar_identitas');
    $filename = time() . '_' . $file->getClientOriginalName();

    $file->move(public_path('assets/uploads/identitas'), $filename);

    /* ================= SIMPAN PENYEWA ================= */
    Penyewa::create([
        'users_idusers'    => $user->idusers,
        'gambar_identitas' => $filename
    ]);

    return redirect('/login')
    ->with('success', 'Registrasi berhasil, silakan login');

}

public function registerAdminCabang(Request $request)
{
    $request->validate([
        'nama_cabang' => 'required|string|max:255',
        'lokasi'      => 'required|string|max:255',
        'nama'        => 'required|string|max:255',
        'username'    => 'required|unique:users,username',
        'password'    => 'required|min:6',
        'no_telepon'  => 'required|string|max:20',
        'alamat'      => 'required|string',
        'gambar_mou'  => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    // ===== SIMPAN CABANG BARU =====
    $cabang = Cabang::create([
    'nama_cabang'   => $request->nama_cabang,
    'lokasi'        => $request->lokasi,
    'status_cabang' => 'aktif'
  ]);


    // ===== SIMPAN USER =====
    $user = User::create([
        'nama'       => $request->nama,
        'username'   => $request->username,
        'password'   => Hash::make($request->password),
        'no_telepon' => $request->no_telepon,
        'alamat'     => $request->alamat,
        'status'     => 'admin_cabang'
    ]);

    // ===== UPLOAD GAMBAR MOU =====
    $file     = $request->file('gambar_mou');
    $filename = time() . '_' . $file->getClientOriginalName();
    $file->move(public_path('assets/uploads/mou'), $filename);

    // ===== SIMPAN ADMIN CABANG =====
    AdminCabang::create([
        'users_idusers'   => $user->idusers,
        'cabang_idcabang' => $cabang->idcabang, // pakai cabang baru
        'gambar_mou'      => $filename
    ]);

    return redirect('/login')
    ->with('success', 'Registrasi berhasil, silakan login');
}

}
