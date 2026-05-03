<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\StokCabang;
use App\Models\Paket;
use App\Models\PaketDetail;
use App\Models\Produk;

class PaketController extends Controller
{
    // ===================== CABANG =====================

    public function create()
    {
        $user = auth()->user();

        if (!$user->adminCabang) {
            abort(403, 'User tidak terhubung ke cabang');
        }

        $cabangId = $user->adminCabang->cabang_idcabang;

        $stokCabang = StokCabang::with('produk')
            ->where('cabang_idcabang', $cabangId)
            ->where('jumlah', '>', 0)
            ->get();

        return view('paket_cabang', compact('stokCabang'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            $cabangId = $user->adminCabang->cabang_idcabang;

            // 🔥 upload gambar (FIXED)
            $path = null;
            if ($request->hasFile('gambar_paket')) {
                $path = $request->file('gambar_paket')
                                ->store('paket', 'public');
            }

            $paket = Paket::create([
                'nama_paket' => $request->nama_paket,
                'harga_paket' => $request->harga_paket,
                'cabang_id' => $cabangId,
                'gambar_paket' => $path
            ]);

            foreach ($request->produk_cabang_id as $index => $stokId) {
                if (!$stokId) continue;

                PaketDetail::create([
                    'paket_id' => $paket->id,
                    'stok_cabang_id' => $stokId,
                    'qty' => $request->qty[$index]
                ]);
            }

            DB::commit();
            return back()->with('success', 'Paket berhasil dibuat');

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

            $paket->nama_paket = $request->nama_paket;
            $paket->harga_paket = $request->harga_paket;

            // 🔥 FIXED IMAGE UPDATE
            if ($request->hasFile('gambar_paket')) {

                if ($paket->gambar_paket) {
                    Storage::disk('public')->delete($paket->gambar_paket);
                }

                $paket->gambar_paket = $request->file('gambar_paket')
                    ->store('paket', 'public');
            }

            $paket->save();

            // detail ulang
            PaketDetail::where('paket_id', $paket->id)->delete();

            foreach ($request->produk_cabang_id as $index => $stokId) {
                if (!$stokId) continue;

                PaketDetail::create([
                    'paket_id' => $paket->id,
                    'stok_cabang_id' => $stokId,
                    'qty' => $request->qty[$index]
                ]);
            }

            DB::commit();
            return redirect()->route('produk_cabang')->with('success', 'Paket berhasil diupdate');

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

            PaketDetail::where('paket_id', $paket->id)->delete();
            $paket->delete();

            DB::commit();
            return back()->with('success', 'Paket berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // ===================== PUSAT =====================

    public function createPusat()
    {
        $user = Auth::user();

        if (!$user->adminPusat && $user->status !== 'owner') {
            abort(403);
        }

        $produk = Produk::where('stok_pusat', '>', 0)->get();

        return view('paket_pusat', compact('produk'));
    }

    public function storePusat(Request $request)
    {
        $user = Auth::user();

        if (!$user->adminPusat && $user->status !== 'owner') {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $path = null;

            if ($request->hasFile('gambar_paket')) {
                $path = $request->file('gambar_paket')
                                ->store('paket', 'public');
            }

            $paket = Paket::create([
                'nama_paket' => $request->nama_paket,
                'harga_paket' => $request->harga_paket,
                'cabang_id' => null,
                'gambar_paket' => $path
            ]);

            foreach ($request->produk_id as $index => $produkId) {
                if (!$produkId) continue;

                PaketDetail::create([
                    'paket_id' => $paket->id,
                    'produk_idproduk' => $produkId,
                    'stok_cabang_id' => null,
                    'qty' => $request->qty[$index]
                ]);
            }

            DB::commit();
            return redirect()->route('data_produk')->with('success', 'Paket pusat berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function editPusat($id)
    {
        $user = Auth::user();

        if (!$user->adminPusat && $user->status !== 'owner') {
            abort(403);
        }

        $paket = Paket::with('detail.produk')->findOrFail($id);
        $produk = Produk::where('stok_pusat', '>', 0)->get();

        return view('paket_edit_pusat', compact('paket', 'produk'));
    }

    public function updatePusat(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->adminPusat && $user->status !== 'owner') {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $paket = Paket::findOrFail($id);

            $paket->nama_paket = $request->nama_paket;
            $paket->harga_paket = $request->harga_paket;

            // FIX IMAGE
            if ($request->hasFile('gambar_paket')) {

                if ($paket->gambar_paket) {
                    Storage::disk('public')->delete($paket->gambar_paket);
                }

                $paket->gambar_paket = $request->file('gambar_paket')
                    ->store('paket', 'public');
            }

            $paket->save();

            PaketDetail::where('paket_id', $paket->id)->delete();

            foreach ($request->produk_id as $index => $produkId) {
                if (!$produkId) continue;

                PaketDetail::create([
                    'paket_id' => $paket->id,
                    'produk_idproduk' => $produkId,
                    'stok_cabang_id' => null,
                    'qty' => $request->qty[$index]
                ]);
            }

            DB::commit();
            return redirect()->route('data_produk')->with('success', 'Paket berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyPusat($id)
    {
        $user = Auth::user();

        if (!$user->adminPusat && $user->status !== 'owner') {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $paket = Paket::findOrFail($id);

            PaketDetail::where('paket_id', $paket->id)->delete();
            $paket->delete();

            DB::commit();
            return back()->with('success', 'Paket berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}