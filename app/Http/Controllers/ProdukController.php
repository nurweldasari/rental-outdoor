<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    // ================== INDEX ==================
    public function index(Request $request)
    {
        // HANYA OWNER DAN ADMIN PUSAT
        if(
            auth()->user()->status != 'owner' &&
            auth()->user()->status != 'admin_pusat'
        ){
            abort(403,'Akses ditolak');
        }
        $kategori = Kategori::all();

        $produk = Produk::with(['kategori', 'hargaAktif'])
            ->when($request->kategori, function ($query) use ($request) {
                $query->where('kategori_idkategori', $request->kategori);
            })
            ->when($request->skala, function ($query) use ($request) {
                $query->where('jenis_skala', $request->skala);
            })
            ->paginate(10);

        $paketList = Paket::with(['detail.produk', 'hargaTerbaru'])
            ->whereNull('cabang_id')
            ->get();

        return view('data_produk', compact('produk', 'kategori', 'paketList'));
    }

    // ================== CREATE ==================
    public function create()
    {
        // HANYA OWNER DAN ADMIN PUSAT
        if(
            auth()->user()->status != 'owner' &&
            auth()->user()->status != 'admin_pusat'
        ){
            abort(403,'Akses ditolak');
        }
        $kategori = Kategori::all();
        return view('tambah_produk', compact('kategori'));
    }

    // ================== STORE ==================
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|max:45',
            'stok_pusat'  => 'required|integer',
            'harga'       => 'required|integer',
            'jenis_skala' => 'required|max:45',
            'kategori_idkategori' => 'required|exists:kategori,idkategori',
            'gambar_produk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'admin_pusat_idadmin_pusat' => 'required',
        ]);

        $path = null;

        if ($request->hasFile('gambar_produk')) {
            $path = $request->file('gambar_produk')->store('produk', 'public');
        }

        // 🔥 SIMPAN PRODUK
        $produk = Produk::create([
            'nama_produk' => $request->nama_produk,
            'stok_pusat'  => $request->stok_pusat,
            'jenis_skala' => $request->jenis_skala,
            'kategori_idkategori' => $request->kategori_idkategori,
            'gambar_produk' => $path,
            'admin_pusat_idadmin_pusat' => $request->admin_pusat_idadmin_pusat,
        ]);

        // 🔥 SIMPAN HARGA AWAL
        $produk->harga()->create([
    'type' => 'produk',
    'harga' => $request->harga,
    'tanggal_berlaku' => now(),
]);

        return redirect()->route('data_produk')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    // ================== EDIT ==================
    public function edit($id)
    {
        $user = auth()->user();
        if (!$user->adminPusat && $user->status !== 'owner') {
            abort(403, 'Akses ditolak');
        }
        $produk = Produk::with('hargaAktif')->findOrFail($id);
        $kategori = Kategori::all();

        return view('edit_produk', compact('produk', 'kategori'));
    }

    // ================== UPDATE ==================
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|max:45',
            'stok_pusat'  => 'required|integer',
            'harga'       => 'required|integer',
            'jenis_skala' => 'required|max:45',
            'kategori_idkategori' => 'required|exists:kategori,idkategori',
            'gambar_produk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'admin_pusat_idadmin_pusat' => 'required',
        ]);

        // 🔥 upload gambar baru
        if ($request->hasFile('gambar_produk')) {
            if ($produk->gambar_produk) {
                Storage::disk('public')->delete($produk->gambar_produk);
            }

            $produk->gambar_produk = $request->file('gambar_produk')
                ->store('produk', 'public');
        }

        // update data
        $produk->nama_produk = $request->nama_produk;
        $produk->stok_pusat  = $request->stok_pusat;
        $produk->jenis_skala = $request->jenis_skala;
        $produk->kategori_idkategori = $request->kategori_idkategori;
        $produk->admin_pusat_idadmin_pusat = $request->admin_pusat_idadmin_pusat;

        // 🔥 kalau harga diubah → tambah history baru
        if ($request->filled('harga')) {
           $produk->harga()->create([
    'type' => 'produk',
    'harga' => $request->harga,
    'tanggal_berlaku' => now(),
]);
        }

        $produk->save();

        return redirect()->route('data_produk')
            ->with('success', 'Produk berhasil diperbarui');
    }

    // ================== DELETE ==================
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->gambar_produk) {
            Storage::disk('public')->delete($produk->gambar_produk);
        }

        $produk->delete();

        return redirect()->route('data_produk')
            ->with('success', 'Produk berhasil dihapus');
    }
}