<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
{
   
    // HANYA OWNER DAN ADMIN PUSAT
    if(
        auth()->user()->status != 'owner' &&
        auth()->user()->status != 'admin_pusat'
    ){
        abort(403,'Akses ditolak');
    }
    $perPage = $request->get('per_page', 10);

    $kategori = Kategori::withSum('produk', 'stok_pusat')
                    ->paginate($perPage)
                    ->withQueryString();

    return view('data_kategori', compact('kategori'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|max:45|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|max:45|unique:kategori,nama_kategori,' . $id . ',idkategori',
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('data_kategori')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }
}