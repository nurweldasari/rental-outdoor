<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;     

class ProdukController extends Controller
{
    // ================== INDEX ==================
    public function index(Request $request)
{
    $kategori = Kategori::all();

    $produk = Produk::with('kategori')
    ->when($request->kategori, function ($query) use ($request) {
        $query->where('kategori_idkategori', $request->kategori);
    })
    ->when($request->skala, function ($query) use ($request) {
        $query->where('jenis_skala', $request->skala);
    }) 
    ->get();

    return view('data_produk', compact('produk', 'kategori'));
}

    // ================== CREATE ==================
    public function create()
    {
        $kategori = Kategori::all();   // ✅ INI YANG KEMARIN HILANG

        return view('tambah_produk', compact('kategori'));
    }

    // ================== STORE ==================
    public function store(Request $request)
{
    // Validasi
    $request->validate([
        'nama_produk' => 'required|max:45',
        'stok_pusat'  => 'required|integer',
        'harga'       => 'required|integer',
        'jenis_skala' => 'required|max:45',
        'kategori_idkategori' => 'required|exists:kategori,idkategori',
        'gambar_produk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'admin_pusat_idadmin_pusat' => 'required',
    ]);

    // Upload gambar ke folder public/assets/uploads/produk
    $filename = null;
    if ($request->hasFile('gambar_produk')) {
        $file = $request->file('gambar_produk');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('assets/uploads/produk'), $filename);
    }

    // Simpan ke database
    Produk::create([
        'nama_produk' => $request->nama_produk,
        'stok_pusat'  => $request->stok_pusat,
        'harga'       => $request->harga,
        'jenis_skala' => $request->jenis_skala,
        'kategori_idkategori' => $request->kategori_idkategori,
        'gambar_produk' => $filename, // simpan nama file
        'admin_pusat_idadmin_pusat' => $request->admin_pusat_idadmin_pusat,
    ]);

    return redirect()->route('data_produk')
                     ->with('success', 'Produk berhasil ditambahkan');
}

public function edit($id)
{
    $produk = Produk::findOrFail($id); // ambil produk berdasarkan id
    $kategori = Kategori::all();       // ambil semua kategori untuk dropdown

    return view('edit_produk', compact('produk', 'kategori'));
}

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

    // Handle upload gambar baru
    if ($request->hasFile('gambar_produk')) {
        // Hapus file lama jika ada
        if ($produk->gambar_produk && file_exists(public_path('assets/uploads/produk/'.$produk->gambar_produk))) {
            unlink(public_path('assets/uploads/produk/'.$produk->gambar_produk));
        }

        $file = $request->file('gambar_produk');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('assets/uploads/produk'), $filename);
        $produk->gambar_produk = $filename;
    }

    // Update field lain
    $produk->nama_produk = $request->nama_produk;
    $produk->stok_pusat  = $request->stok_pusat;
    $produk->harga       = $request->harga;
    $produk->jenis_skala = $request->jenis_skala;
    $produk->kategori_idkategori = $request->kategori_idkategori;
    $produk->admin_pusat_idadmin_pusat = $request->admin_pusat_idadmin_pusat;

    $produk->save();

    return redirect()->route('data_produk')
                     ->with('success', 'Produk berhasil diperbarui');
}

public function destroy($id)
{
    $produk = Produk::findOrFail($id);

    // Hapus gambar lama jika ada
    if ($produk->gambar_produk && file_exists(public_path('assets/uploads/produk/'.$produk->gambar_produk))) {
        unlink(public_path('assets/uploads/produk/'.$produk->gambar_produk));
    }

    $produk->delete();

    return redirect()->route('data_produk')
                     ->with('success', 'Produk berhasil dihapus');
}

}
