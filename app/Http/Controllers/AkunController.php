<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AkunController extends Controller
{
    public function editcabang()
    {
        $user = Auth::user();

        $cabang = DB::table('admin_cabang')
            ->join('cabang', 'admin_cabang.cabang_idcabang', '=', 'cabang.idcabang')
            ->where('admin_cabang.users_idusers', $user->idusers)
            ->select('cabang.*')
            ->first();

        return view('profil_cabang', compact('user', 'cabang'));
    }

    public function profilcabang(Request $request)
    {
        $request->validate([
    'nama_cabang' => 'required|string|max:255',
    'lokasi'      => 'required|string|max:255',

    'nama'        => 'required|string|max:255',
    'username'    => 'required|string|max:255|unique:users,username,' 
                     . Auth::user()->idusers . ',idusers',
    'no_telepon'  => 'required|string|max:20',
    'alamat'      => 'required|string',
]);


        DB::beginTransaction();

        try {

            // ================= UPDATE USERS =================
            DB::table('users')
                ->where('idusers', Auth::user()->idusers)
                ->update([
                    'nama'       => $request->nama,
                    'username'   => $request->username,
                    'no_telepon' => $request->no_telepon,
                    'alamat'     => $request->alamat,
                    'updated_at' => now(),
                ]);

            // ================= UPDATE CABANG =================
            DB::table('admin_cabang')
                ->join('cabang', 'admin_cabang.cabang_idcabang', '=', 'cabang.idcabang')
                ->where('admin_cabang.users_idusers', Auth::user()->idusers)
                ->update([
                    'cabang.nama_cabang' => $request->nama_cabang,
                    'cabang.lokasi'      => $request->lokasi,
                    'cabang.updated_at'  => now(),
                ]);

            DB::commit();

            return back()->with('success', 'Profil cabang berhasil diperbarui');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit()
{
    $user = Auth::user();

    return view('profil', compact('user'));
}

public function profil(Request $request)
{
    $request->validate([
        'nama'       => 'required|string|max:255',
        'username'   => 'required|string|max:255|unique:users,username,' 
                        . Auth::user()->idusers . ',idusers',
        'no_telepon' => 'required|string|max:20',
        'alamat'     => 'required|string',
    ]);

    try {

        DB::table('users')
            ->where('idusers', Auth::user()->idusers)
            ->update([
                'nama'       => $request->nama,
                'username'   => $request->username,
                'no_telepon' => $request->no_telepon,
                'alamat'     => $request->alamat,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Profil berhasil diperbarui');

    } catch (\Exception $e) {

        return back()->with('error', $e->getMessage());
    }
}

public function updatePassword(Request $request)
{
    $request->validate([
        'password_lama' => 'required',
        'password_baru' => ['required', 'confirmed', Password::defaults()]
    ]);

    $user = Auth::user();

    // cek password lama
    if (!Hash::check($request->password_lama, $user->password)) {
        return back()->withErrors([
            'password_lama' => 'Password lama tidak sesuai'
        ]);
    }

    // update password baru
    $user->password = Hash::make($request->password_baru);
    $user->save();

    return back()->with('success', 'Password berhasil diperbarui');
}

public function editrekening()
    {
        $user = Auth::user();

        if (!$user->adminCabang) {
            abort(403, 'User tidak terhubung ke cabang');
        }

        $cabangId = DB::table('admin_cabang')
            ->where('users_idusers', $user->idusers)
            ->value('cabang_idcabang');

        $rekening = DB::table('rekening')
            ->where('cabang_idcabang', $cabangId)
            ->first();

        return view('rekening', compact('user', 'rekening'));
    }

    public function updateRekening(Request $request)
{
    $request->validate([
        'nama_bank'   => 'required|max:45',
        'no_rekening' => 'required|max:45',
        'atas_nama'   => 'required|max:45',
    ]);

    DB::beginTransaction();

    try {

        $user = Auth::user();

        $cabangId = DB::table('admin_cabang')
            ->where('users_idusers', $user->idusers)
            ->value('cabang_idcabang');

        $rekening = DB::table('rekening')
            ->where('cabang_idcabang', $cabangId)
            ->first();

        if ($rekening) {

            DB::table('rekening')
                ->where('idrekening', $rekening->idrekening)
                ->update([
                    'nama_bank'   => $request->nama_bank,
                    'no_rekening' => $request->no_rekening,
                    'atas_nama'   => $request->atas_nama,
                    'updated_at'  => now(),
                ]);

        } else {

            DB::table('rekening')->insert([
                'nama_bank'       => $request->nama_bank,
                'no_rekening'     => $request->no_rekening,
                'atas_nama'       => $request->atas_nama,
                'cabang_idcabang' => $cabangId,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        DB::commit();

        return back()->with('success', 'Rekening berhasil diperbarui');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->withErrors([
            'error' => 'Terjadi kesalahan saat memperbarui rekening. Silakan coba lagi.'
        ]);
    }
}
}
