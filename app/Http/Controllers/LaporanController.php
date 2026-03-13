<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penyewaan;
use App\Models\Cabang;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();

    // ================= OWNER =================
    if ($user->status === 'owner') {

        // MODE LIST CARD
        if (!$request->cabang) {
            $cabangList = Cabang::all();
            return view('laporan_cabang', compact('cabangList'));
        }

        // MODE DETAIL → pakai blade laporan (yang ada tabel)
        $cabang = Cabang::findOrFail($request->cabang);

        $penyewaan = Penyewaan::where('cabang_idcabang', $cabang->idcabang)
            ->when($request->bulan, function ($q) use ($request) {
                $q->whereMonth('tanggal_sewa', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal_sewa', date('Y', strtotime($request->bulan)));
            })
            ->get();

        $totalPendapatan = $penyewaan->sum('total');

        return view('laporan', compact(
            'penyewaan',
            'totalPendapatan',
            'cabang'
        ));
    }

    // ================= ADMIN CABANG =================
    if ($user->status === 'admin_cabang') {

    $adminCabang = $user->adminCabang;

    if (!$adminCabang) {
        abort(404, 'Admin cabang belum terhubung ke cabang.');
    }

    $cabang = Cabang::findOrFail($adminCabang->cabang_idcabang);

    $penyewaan = Penyewaan::where('cabang_idcabang', $cabang->idcabang)
        ->when($request->bulan, function ($q) use ($request) {
            $q->whereMonth('tanggal_sewa', date('m', strtotime($request->bulan)))
              ->whereYear('tanggal_sewa', date('Y', strtotime($request->bulan)));
        })
        ->get();

    $totalPendapatan = $penyewaan->sum('total');

    return view('laporan', compact(
        'penyewaan',
        'totalPendapatan',
        'cabang'
    ));
}
}
}