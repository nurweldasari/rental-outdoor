<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Penyewa;
use App\Models\AdminCabang;
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

    if (!Auth::attempt($request->only('username', 'password'))) {
        return back()->withErrors([
            'username' => 'Username atau password salah'
        ]);
    }

    $request->session()->regenerate();
    $user = Auth::user();

    /* ===== CEK KHUSUS ADMIN CABANG ===== */
    if ($user->status === 'admin_cabang') {

        $adminCabang = AdminCabang::with('cabang')
            ->where('users_idusers', $user->idusers)
            ->first();

        if (!$adminCabang || !$adminCabang->cabang) {
            Auth::logout();
            return back()->withErrors('Data cabang tidak ditemukan');
        }

        $status = $adminCabang->cabang->status_cabang;

        if ($status === 'pending') {
            Auth::logout();
            return back()->withErrors('Akun cabang menunggu konfirmasi owner');
        }

        if ($status === 'ditolak') {
            Auth::logout();
            return back()->withErrors('Pengajuan cabang Anda ditolak');
        }

        if ($status === 'nonaktif') {
            Auth::logout();
            return back()->withErrors('Akun cabang dinonaktifkan');
        }
    }

    /* ===== CEK KHUSUS PENYEWA ===== */
    if ($user->status === 'penyewa') {

        $penyewa = Penyewa::where('users_idusers', $user->idusers)->first();

        if (!$penyewa) {
            Auth::logout();
            return back()->withErrors('Data penyewa tidak ditemukan');
        }

        if ($penyewa->status_penyewa === 'pending') {
            Auth::logout();
            return back()->withErrors('Akun Anda menunggu konfirmasi owner');
        }

        if ($penyewa->status_penyewa === 'ditolak') {
            Auth::logout();
            return back()->withErrors('Pengajuan Anda ditolak');
        }

        if ($penyewa->status_penyewa === 'nonaktif') {
            Auth::logout();
            return back()->withErrors('Akun Anda dinonaktifkan');
        }
    }

    return redirect()->route('dashboard');
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

        $user = User::create([
            'nama'        => $request->nama,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'no_telepon'  => $request->no_telepon,
            'alamat'      => $request->alamat,
            'status'      => 'penyewa'
        ]);

        $file = $request->file('gambar_identitas');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('assets/uploads/identitas'), $filename);

        Penyewa::create([
            'users_idusers'    => $user->idusers,
            'gambar_identitas' => $filename,
            'status_penyewa'    => 'pending'

        ]);

        return redirect('/login')
            ->with('success', 'Registrasi berhasil, silakan login');
    }

    /* ================= REGISTER ADMIN CABANG ================= */
    public function registerAdminCabang(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'lokasi'      => 'required|string|max:500',
            'nama'        => 'required|string|max:255',
            'username'    => 'required|unique:users,username',
            'password'    => 'required|min:6',
            'no_telepon'  => 'required|string|max:20',
            'alamat'      => 'required|string',
            'gambar_mou'  => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // CABANG → PENDING (MENUNGGU OWNER)
        $cabang = Cabang::create([
            'nama_cabang'   => $request->nama_cabang,
            'lokasi'        => $request->lokasi,
            'status_cabang' => 'pending'
        ]);

        $user = User::create([
            'nama'       => $request->nama,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'no_telepon' => $request->no_telepon,
            'alamat'     => $request->alamat,
            'status'     => 'admin_cabang'
        ]);

        $file = $request->file('gambar_mou');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('assets/uploads/mou'), $filename);

        AdminCabang::create([
            'users_idusers'   => $user->idusers,
            'cabang_idcabang' => $cabang->idcabang,
            'gambar_mou'      => $filename
        ]);

        return redirect('/login')
            ->with('success', 'Registrasi berhasil, silakan login');
    }
}
