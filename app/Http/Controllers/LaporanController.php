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

            // LIST CARD CABANG
            if (!$request->cabang) {
                $cabangList = Cabang::paginate(12)->withQueryString();

                return view('laporan_cabang', compact('cabangList'));
            }

            // DETAIL CABANG
            $cabang = Cabang::findOrFail($request->cabang);

            $penyewaan = Penyewaan::with([
                'itemPenyewaan.produk',
                'itemPenyewaan.paket.detail.stokCabang.produk'
            ])
            ->where('cabang_idcabang', $cabang->idcabang)
            ->where('status_penyewaan', 'selesai')
            ->when($request->bulan, function ($q) use ($request) {
                $q->whereMonth('tanggal_sewa', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal_sewa', date('Y', strtotime($request->bulan)));
            })
            ->orderBy('tanggal_kembali', 'desc')
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
        if ($request->has('cabang')) {
                abort(403, 'Akses ditolak');
            }

            $adminCabang = $user->adminCabang;

            if (!$adminCabang) {
                abort(404, 'Admin cabang belum terhubung ke cabang.');
            }

            $cabang = Cabang::findOrFail($adminCabang->cabang_idcabang);

            $penyewaan = Penyewaan::where('cabang_idcabang', $cabang->idcabang)
                ->where('status_penyewaan', 'selesai')
                ->when($request->bulan, function ($q) use ($request) {
                    $q->whereMonth('tanggal_sewa', date('m', strtotime($request->bulan)))
                      ->whereYear('tanggal_sewa', date('Y', strtotime($request->bulan)));
                })
                ->orderBy('tanggal_kembali', 'desc')
                ->get();

            $totalPendapatan = $penyewaan->sum('total');

            return view('laporan', compact(
                'penyewaan',
                'totalPendapatan',
                'cabang'
            ));
        }

        // SELAIN OWNER DITOLAK
        abort(403, 'Tidak punya akses laporan cabang');
    }

    public function laporanPusat(Request $request)
    {
        $user = auth()->user();

        // HANYA OWNER & ADMIN PUSAT
        if (!in_array($user->status, ['admin_pusat', 'owner'])) {
            abort(403, 'Tidak punya akses laporan pusat');
        }

        $penyewaan = Penyewaan::whereNotNull('admin_pusat_idadmin_pusat')
            ->where('status_penyewaan', 'selesai')
            ->when($request->bulan, function ($q) use ($request) {
                $q->whereMonth('tanggal_sewa', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal_sewa', date('Y', strtotime($request->bulan)));
            })
            ->orderBy('tanggal_kembali', 'desc')
            ->get();

        $totalPendapatan = $penyewaan->sum('total');

        return view('laporan_pusat', compact(
            'penyewaan',
            'totalPendapatan'
        ));
    }
}