<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
    public function __construct()
    {
        // Pastikan hanya user login yang bisa akses
        $this->middleware('auth');
    }

    /* ================= INDEX ================= */
    public function index(Request $request)
{
    if (auth()->user()->status != 'owner') {
        abort(403, 'Akses ditolak');
    }

    $tab = $request->get('tab', 'cabang');

    $entriesCabang = min((int) $request->get('entries_cabang', 10), 100);
    $entriesRekening = min((int) $request->get('entries_rekening', 10), 100);

    $cabang = Cabang::with('adminCabang.user')
        ->paginate($entriesCabang)
        ->withQueryString();

    $rekening = Rekening::with('cabang')
        ->paginate($entriesRekening)
        ->withQueryString();

    $listCabang = Cabang::where('status_cabang', 'aktif')->get();

    return view('data_cabang', compact(
    'cabang',
    'rekening',
    'listCabang',
    'entriesCabang',
    'entriesRekening',
    'tab'
))->with('tab', $tab);
}

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_cabang'   => 'required|string|max:255',
            'status_cabang' => 'required|in:aktif,nonaktif,ditolak',
            'lokasi'        => 'required|string|max:255',
        ]);

        // Hindari mass assignment injection
        Cabang::create($validated);

        return redirect()->back()
            ->with('success', 'Data cabang berhasil ditambahkan');
    }

    /* ================= KONFIRMASI TERIMA ================= */
    public function terima($id)
    {
        DB::transaction(function () use ($id) {

            $cabang = Cabang::where('idcabang', $id)->firstOrFail();

            $cabang->update([
                'status_cabang' => 'aktif'
            ]);
        });

        return back()->with('success', 'Cabang disetujui');
    }

    /* ================= KONFIRMASI TOLAK ================= */
    public function tolak($id)
    {
        DB::transaction(function () use ($id) {

            $cabang = Cabang::where('idcabang', $id)->firstOrFail();

            $cabang->update([
                'status_cabang' => 'ditolak'
            ]);
        });

        return back()->with('success', 'Cabang ditolak');
    }

    /* ================= TOGGLE STATUS ================= */
    public function togglestatus($id)
    {
        DB::transaction(function () use ($id) {

            $cabang = Cabang::findOrFail($id);

            $cabang->status_cabang =
                $cabang->status_cabang === 'aktif'
                ? 'nonaktif'
                : 'aktif';

            $cabang->save();
        });

        return back()->with('success', 'Status cabang diubah');
    }
}
