<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Owner;
use App\Models\AdminPusat;
use App\Models\AdminCabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenyewaController extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();

    if ($user->status !== 'admin_cabang') {
        abort(403, 'Hanya admin cabang yang boleh akses');
    }

    $perPage = $request->get('per_page', 10);

    $penyewa = DB::table('users')
        ->join('penyewa', 'penyewa.users_idusers', '=', 'users.idusers')
        ->where('users.status', 'penyewa')
        ->select(
            'users.*',
            'penyewa.gambar_identitas',
            'penyewa.status_penyewa'
        )
        ->paginate($perPage)
        ->withQueryString();

    return view('data_penyewa', compact('penyewa'));
}

    // FORM
    public function create()
    {
        $user = auth()->user();

        if ($user->status !== 'admin_cabang') {
            abort(403, 'Hanya admin cabang yang boleh akses');
        }

        return view('tambah_penyewa');
    }

    // SIMPAN
    public function store(Request $request)
{
    $validated = $request->validate([

    'nama' => 'required|string|max:100',
    'username' => 'required|string|max:50|unique:users,username',
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


//admin pusat
  public function indexPusat(Request $request)
{
    if(
            auth()->user()->status != 'owner' &&
            auth()->user()->status != 'admin_pusat'
        ){
            abort(403,'Akses ditolak');
        }
    $perPage = $request->get('per_page', 10); // optional (biar bisa dinamis)

    $penyewa = DB::table('users')
        ->join('penyewa', 'penyewa.users_idusers', '=', 'users.idusers')
        ->where('users.status', 'penyewa')
        ->select(
            'users.*',
            'penyewa.gambar_identitas',
            'penyewa.status_penyewa'
        )
        ->paginate($perPage) // 🔥 INI YANG PENTING
        ->withQueryString(); // biar parameter tetap kebawa

    return view('data_penyewa_pusat', compact('penyewa'));
}

    // FORM
    public function createPusat()
    {
        // HANYA OWNER DAN ADMIN PUSAT
    if(
        auth()->user()->status != 'owner' &&
        auth()->user()->status != 'admin_pusat'
    ){
        abort(403,'Akses ditolak');
    }
        return view('tambah_penyewa_pusat');
    }

    // SIMPAN
    public function storePusat(Request $request)
{
    $validated = $request->validate([

    'nama' => 'required|string|max:100',
    'username' => 'required|string|max:50|unique:users,username',
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

        return redirect()->route('data_penyewa_pusat')
            ->with('success', 'Penyewa berhasil ditambahkan');

    } catch (\Exception $e) {

        DB::rollback();

        return back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan saat menyimpan data');
    }
}
 /* ================= KONFIRMASI TERIMA ================= */
    public function terimaPusat($id)
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
    public function tolakPusat($id)
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
    public function toggleStatusPusat($id)
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