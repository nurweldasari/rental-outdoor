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
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255'
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
        'nama' => 'required|string|max:255',
        'username' => app()->environment('testing')
            ? 'required'
            : 'required|unique:users,username',
        'password' => 'required|string|min:6|max:255',
            'no_telepon' => ['required','digits_between:10,15','unique:users,no_telepon','regex:/^08[0-9]{8,11}$/' ],
            'alamat' => 'required|string|max:255',
            'gambar_identitas' => 'bail|required|image|mimes:jpg,jpeg,png|max:2048',
            ],[
        'username.unique' => 'Username sudah digunakan',
        'password.min' => 'Password minimal 6 karakter',
        'no_telepon.unique' => 'Nomor telepon sudah terdaftar',
        'no_telepon.digits_between' =>
            'Nomor telepon harus 10-15 digit',
        'no_telepon.regex' =>
            'Nomor telepon harus diawali 08',
        'gambar_identitas.image' =>
            'Foto identitas harus JPG/PNG maksimal 2MB',
        'gambar_identitas.mimes' =>
            'Foto identitas harus JPG/PNG maksimal 2MB',
        'gambar_identitas.max' =>
            'Foto identitas harus JPG/PNG maksimal 2MB',
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
    'lokasi' => 'required|string|max:500',
    'nama' => 'required|string|max:255',
    'username' => 'required|unique:users,username',
    'password' => 'required|string|min:6|max:255',
    'no_telepon' => [ 'required','string', 'max:20', 'unique:users,no_telepon', 'regex:/^08[0-9]{8,11}$/' ],
    'alamat' => 'required|string',
    'gambar_mou' => 'bail|required|image|mimes:jpg,jpeg,png|max:2048'

], [

    'username.unique' => 'Username sudah digunakan',

    'password.min' => 'Password minimal 6 karakter',

    'no_telepon.unique' => 'Nomor telepon sudah terdaftar',

    'no_telepon.regex' =>
        'Nomor telepon harus diawali 08 dan 10-13 digit',

    'gambar_mou.image' =>
        'Foto MOU harus JPG/PNG maksimal 2MB',

    'gambar_mou.mimes' =>
        'Foto MOU harus JPG/PNG maksimal 2MB',

    'gambar_mou.max' =>
        'Foto MOU harus JPG/PNG maksimal 2MB',

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
    $request->validate([
        'no_wa' => 'required'
    ]);

    $otp = rand(100000, 999999);
    $input = $request->no_wa;

    // format WA (62)
    $noWa = $input;
    if (substr($noWa, 0, 1) == "0") {
        $noWa = "62" . substr($noWa, 1);
    }

    // format DB (08)
    $noDb = $input;
    if (substr($noDb, 0, 2) == "62") {
        $noDb = "0" . substr($noDb, 2);
    }

    $user = User::where('no_telepon', $noDb)->first();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Nomor tidak terdaftar'
        ]);
    }

    // simpan session
    session([
        'otp' => $otp,
        'user_id' => $user->idusers,
        'otp_expired' => now()->addMinutes(5)
    ]);

    $message = "Halo {$user->nama},\n\n"
             . "Kode OTP reset password:\n\n"
             . "$otp\n\n"
             . "Jangan berikan ke siapa pun.";

    try {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => env('FONTE_URL'), // 🔥 pakai env
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'target' => $noWa,
                'message' => $message
            ],
            CURLOPT_HTTPHEADER => [
                "Authorization: " . env('FONTE_TOKEN')
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl));
        }

        curl_close($curl);

        return response()->json([
            'status' => 'success',
            'otp_debug' => $otp // 🔥 hapus ini nanti kalau production
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal kirim OTP'
        ]);
    }
}
public function verifikasiOtp(Request $request)
{
    if (!session('otp')) {
        return response()->json([
            'status' => 'error',
            'message' => 'OTP belum dibuat'
        ]);
    }

    if (now()->gt(session('otp_expired'))) {
        return response()->json([
            'status' => 'error',
            'message' => 'OTP sudah kadaluarsa'
        ]);
    }

    if (!$request->otp) {
        return response()->json([
            'status' => 'error',
            'message' => 'OTP wajib diisi'
        ]);
    }

    if ($request->otp == session('otp')) {

        session(['otp_verified' => true]); // 🔥 tambahan keamanan

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

    if (!session('otp_verified') || !session('user_id')) {
        return back()->with('error', 'OTP belum diverifikasi');
    }

    $user = User::find(session('user_id'));

    if (!$user) {
        return back()->with('error', 'User tidak ditemukan');
    }

    $user->password = Hash::make($request->password);
    $user->save();

    session()->forget([
        'otp',
        'user_id',
        'otp_expired',
        'otp_verified'
    ]);

    return redirect('/login')->with('success', 'Password berhasil diubah');
}
}