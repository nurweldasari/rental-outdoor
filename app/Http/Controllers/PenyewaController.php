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

        // ================= SIMPAN USERS =================
        $user = DB::table('users')->insertGetId([
            'nama'       => $request->nama,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'no_telepon' => $request->no_telepon,
            'alamat'     => $request->alamat,
            'status'     => 'penyewa',
            'created_at' => now(),
            'updated_at' => now(),
        ],'idusers');

        // ================= UPLOAD GAMBAR =================
        $file = $request->file('gambar_identitas');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('assets/uploads/identitas'), $filename);

        // ================= SIMPAN PENYEWA =================
        DB::table('penyewa')->insert([
            'users_idusers' => $user, // ✅ SUDAH BENAR
            'gambar_identitas' => $filename,
            'status_penyewa'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return redirect('/data_penyewa')
            ->with('success', 'Penyewa berhasil ditambahkan');

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with('error', $e->getMessage());
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
