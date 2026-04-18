<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StokCabang;
use App\Models\Paket;
use App\Models\PaketDetail;

class PaketController extends Controller
{
    

    public function create()
{
    
    $user = auth()->user();

    if (!$user->adminCabang) {
        abort(403, 'User tidak terhubung ke cabang');
    }

    $cabangId = $user->adminCabang->cabang_idcabang;

    $stokCabang = StokCabang::with('produk')
        ->where('cabang_idcabang', $cabangId)
        ->where('jumlah', '>', 0) // optional biar cuma yang ada stok
        ->get();

    return view('paket_cabang', compact('stokCabang'));
}

public function store(Request $request)
{
    $user = auth()->user();

    DB::beginTransaction();

    try {
        $cabangId = $user->adminCabang->cabang_idcabang;

$namaFile = null;

if ($request->hasFile('gambar_paket')) {
    $file = $request->file('gambar_paket');
    $namaFile = time() . '.' . $file->getClientOriginalExtension();
    $file->move(public_path('paket'), $namaFile);
}

$paket = Paket::create([
    'nama_paket' => $request->nama_paket,
    'harga_paket' => $request->harga_paket,
    'cabang_id' => $cabangId,
    'gambar_paket' => $namaFile
]);
        // 2. Simpan detail paket
        foreach ($request->produk_cabang_id as $index => $stokId) {

            if (!$stokId) continue;

            PaketDetail::create([
                'paket_id' => $paket->id,
                'stok_cabang_id' => $stokId,
                'qty' => $request->qty[$index]
            ]);
        }

        DB::commit();

        return redirect()->back()->with('success', 'Paket berhasil dibuat');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

public function edit($id)
{
    $paket = Paket::with('detail.stokCabang.produk')->findOrFail($id);

    $stokCabang = StokCabang::with('produk')
        ->where('cabang_idcabang', auth()->user()->adminCabang->cabang_idcabang)
        ->get();

    return view('paket_edit', compact('paket', 'stokCabang'));
}

public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $paket = Paket::findOrFail($id);

        // update data utama
        $paket->update([
            'nama_paket' => $request->nama_paket,
            'harga_paket' => $request->harga_paket,
        ]);

        // hapus detail lama
        PaketDetail::where('paket_id', $paket->id)->delete();

        // simpan ulang detail
        foreach ($request->produk_cabang_id as $index => $stokId) {

            if (!$stokId) continue;

            PaketDetail::create([
                'paket_id' => $paket->id,
                'stok_cabang_id' => $stokId,
                'qty' => $request->qty[$index]
            ]);
        }

        DB::commit();

        return redirect()->back()->with('success', 'Paket berhasil diupdate');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

public function destroy($id)
{
    DB::beginTransaction();

    try {
        $paket = Paket::findOrFail($id);

        // hapus detail dulu
        PaketDetail::where('paket_id', $paket->id)->delete();

        // hapus paket
        $paket->delete();

        DB::commit();

        return redirect()->back()->with('success', 'Paket berhasil dihapus');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
}
