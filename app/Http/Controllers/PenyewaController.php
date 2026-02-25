<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenyewaController extends Controller
{
    public function index()
    {
        $penyewa = DB::table('users')
            ->join('penyewa', 'penyewa.users_idusers', '=', 'users.idusers')
            ->where('users.status', 'penyewa')
            ->select(
                'users.*',
                'penyewa.gambar_identitas',
                'penyewa.status_penyewa'
            )
            ->get();

        return view('data_penyewa', compact('penyewa'));
    }

    // FORM
    public function create()
    {
        return view('tambah_penyewa');
    }

    // SIMPAN
    public function store(Request $request)
{
    $validated = $request->validate([
        'nama'              => 'required|string|max:100',
        'username'          => 'required|string|max:50|unique:users,username',
        'password'          => 'required|string|min:6|max:255',
        'no_telepon'        => 'required|string|max:20',
        'alamat'            => 'required|string|max:255',
        'gambar_identitas'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    DB::beginTransaction();

    try {

        // ================= SIMPAN USERS =================
        $userId = DB::table('users')->insertGetId([
            'nama'       => $validated['nama'],
            'username'   => $validated['username'],
            'password'   => Hash::make($validated['password']),
            'no_telepon' => $validated['no_telepon'],
            'alamat'     => $validated['alamat'],
            'status'     => 'penyewa',
            'created_at' => now(),
            'updated_at' => now(),
        ],'idusers');

        // ================= UPLOAD GAMBAR =================
        $path = $request->file('gambar_identitas')
                        ->store('identitas', 'public');

        // ================= SIMPAN PENYEWA =================
        DB::table('penyewa')->insert([
            'users_idusers'    => $userId,
            'gambar_identitas' => $path,
            'status_penyewa'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return redirect()->route('data_penyewa')
            ->with('success', 'Penyewa berhasil ditambahkan');

    } catch (\Exception $e) {

        DB::rollback();

        return back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan saat menyimpan data');
    }
}
 /* ================= KONFIRMASI TERIMA ================= */
    public function terima($id)
    {
        DB::transaction(function () use ($id) {

            $penyewa = Penyewa::where('users_idusers', $id)->firstOrFail();

            $penyewa->update([
                'status_penyewa' => 'aktif'
            ]);
        });

        return back()->with('success', 'penyewa disetujui');
    }

    /* ================= KONFIRMASI TOLAK ================= */
    public function tolak($id)
    {
        DB::transaction(function () use ($id) {

            $penyewa = Penyewa::where('users_idusers', $id)->firstOrFail();

            $penyewa->update([
                'status_penyewa' => 'ditolak'
            ]);
        });

        return back()->with('success', 'penyewa ditolak');
    }

    /* ================= TOGGLE STATUS ================= */
    public function toggleStatus($id)
    {
        DB::transaction(function () use ($id) {

            $penyewa = Penyewa::where('users_idusers', $id)->firstOrFail();

            $penyewa->status_penyewa =
                $penyewa->status_penyewa === 'aktif'
                ? 'nonaktif'
                : 'aktif';

            $penyewa->save();
        });

        return back()->with('success', 'Status penyewa diubah');
    }
}
