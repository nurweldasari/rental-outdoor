<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Penyewa;
use App\Models\AdminCabang;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

    DB::beginTransaction();

    try {

        $user = User::create([
            'nama'        => $request->nama,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'no_telepon'  => $request->no_telepon,
            'alamat'      => $request->alamat,
            'status'      => 'penyewa'
        ]);

        // ✅ SIMPAN KE STORAGE
        $path = $request->file('gambar_identitas')
                        ->store('identitas', 'public');

        Penyewa::create([
            'users_idusers'    => $user->idusers,
            'gambar_identitas' => $path, // simpan path, bukan filename saja
            'status_penyewa'   => 'pending'
        ]);

        DB::commit();

        return redirect('/login')
            ->with('success', 'Registrasi berhasil, silakan login');

    } catch (\Exception $e) {

        DB::rollback();

        return back()->withInput()
            ->with('error', 'Terjadi kesalahan saat registrasi');
    }
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

        $path = $request->file('gambar_mou')
                ->store('mou', 'public');

        AdminCabang::create([
            'users_idusers'   => $user->idusers,
            'cabang_idcabang' => $cabang->idcabang,
            'gambar_mou'      => $path
        ]);

        return redirect('/login')
            ->with('success', 'Registrasi berhasil, silakan login');
    }

public function kirimOtp(Request $request)
{
    $otp = rand(100000, 999999);

    $input = $request->no_wa;

    // format untuk WA (62)
    $noWa = $input;
    if (substr($noWa, 0, 1) == "0") {
        $noWa = "62" . substr($noWa, 1);
    }

    // format untuk DB (08)
    $noDb = $input;
    if (substr($noDb, 0, 2) == "62") {
        $noDb = "0" . substr($noDb, 2);
    }

    // cari user
    $user = User::where('no_telepon', $noDb)->first();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Nomor tidak terdaftar'
        ]);
    }

    $nama = $user->nama;

    // simpan session + expired
    session([
        'otp' => $otp,
        'user_id' => $user->idusers,
        'otp_expired' => now()->addMinutes(5)
    ]);

    // format pesan 
    $message = "Halo $nama,\n\n"
             . "Kode OTP untuk reset password akun Outdoorkriss kamu adalah:\n\n"
             . "$otp\n\n"
             . "Jangan berikan kode ini kepada siapa pun.\n\n"
             . "Terima kasih.";

    // kirim WA
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $noWa,
            'message' => $message
        ],
        CURLOPT_HTTPHEADER => [
            "Authorization: " . env('FONNTE_TOKEN')
        ],
    ]);

    curl_exec($curl);
    curl_close($curl);

    return response()->json(['status' => 'success']);
}
public function verifikasiOtp(Request $request)
{
    // cek expired
    if (now()->gt(session('otp_expired'))) {
        return response()->json([
            'status' => 'error',
            'message' => 'OTP sudah kadaluarsa'
        ]);
    }

    // cek kosong
    if (!$request->otp) {
        return response()->json([
            'status' => 'error',
            'message' => 'OTP wajib diisi'
        ]);
    }

    // cek benar
    if ($request->otp == session('otp')) {
        return response()->json([
            'status' => 'success'
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'OTP salah'
    ]);
}
public function resetPassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6|confirmed'
    ]);

    $user = User::find(session('user_id'));

    if (!$user) {
        return back()->with('error', 'User tidak ditemukan');
    }

    $user->password = Hash::make($request->password);
    $user->save();

    // hapus session
    session()->forget(['otp', 'user_id', 'otp_expired']);

    return redirect('/login')->with('success', 'Password berhasil diubah');
}
}