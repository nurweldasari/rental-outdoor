<?php

namespace App\Http\Controllers;
use App\Models\Cabang;
use App\Models\Rekening;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    /* ================= INDEX ================= */
    public function index(Request $request)
{
    $entries = $request->get('entries', 20);

    $cabang = Cabang::with('adminCabang.user')
                ->paginate($entries)
                ->withQueryString();

    $listCabang = Cabang::where('status_cabang', 'aktif')->get();
    $rekening = Rekening::with('cabang')->get();

    return view('data_cabang', compact('cabang', 'listCabang', 'entries', 'rekening'));
}
    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $request->validate([
            'nama_cabang'   => 'required',
            'status_cabang' => 'required',
            'lokasi'        => 'required',
        ]);

        Cabang::create($request->all());

        return redirect()->back()->with('success', 'Data cabang berhasil ditambahkan');
    }

    /* ================= KONFIRMASI TERIMA ================= */
public function terima($id)
{
    Cabang::where('idcabang', $id)
        ->update(['status_cabang' => 'aktif']);

    return back()->with('success', 'Cabang disetujui');
}

    /* ================= KONFIRMASI TOLAK ================= */
  public function tolak($id)
{
    Cabang::where('idcabang', $id)
        ->update(['status_cabang' => 'ditolak']);

    return back()->with('success', 'Cabang ditolak');
}

/* ================= TOGGLE STATUS ================= */
public function togglestatus($id)
{
    $cabang = Cabang::findOrFail($id);

    $cabang->status_cabang =
        $cabang->status_cabang === 'aktif'
        ? 'nonaktif'
        : 'aktif';

    $cabang->save(); 

    return back()->with('success', 'Status cabang diubah');
}
}