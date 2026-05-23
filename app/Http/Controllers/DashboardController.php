<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\User;
use App\Models\Penyewaan;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use App\Models\StokCabang;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   
public function index(Request $request)
{
    $user = Auth::user();

    /* ================= PENYEWA ================= */
    if ($user->status === 'penyewa') {

        $perPage = $request->get('per_page', 7); // jumlah card per halaman

        $cabang = Cabang::where('status_cabang', 'aktif')
            ->paginate($perPage)
            ->withQueryString();

        $adminpusat = User::where('status', 'admin_pusat')
            ->select('idusers', 'nama', 'no_telepon', 'alamat')
            ->first();

        return view('dashboard_penyewa', compact('cabang', 'adminpusat'));
    }


        /* ================= ADMIN CABANG ================= */
        if ($user->status === 'admin_cabang') {

    $cabangId = $user->adminCabang->cabang_idcabang ?? null;

    if (!$cabangId) {
        abort(404, 'Cabang tidak ditemukan');
    }

        // ================= FILTER TAHUN =================
    $tahun = $request->tahun ?? date('Y');

    // ================= CARD =================
    $totalPenyewa = Penyewaan::where('cabang_idcabang', $cabangId)
        ->whereYear('tanggal_sewa', $tahun)
        ->distinct('penyewa_idpenyewa')
        ->count('penyewa_idpenyewa');

    $totalPenyewaan = Penyewaan::where('cabang_idcabang', $cabangId)
        ->whereYear('tanggal_sewa', $tahun)
        ->count();

    $totalAlat = StokCabang::where('cabang_idcabang', $cabangId)
        ->whereYear('created_at', $tahun)
        ->count();

    $totalKategori = Kategori::whereHas('produk.stokCabang', function ($q) use ($cabangId) {
        $q->where('cabang_idcabang', $cabangId);
    })
    ->whereYear('created_at', $tahun)
    ->count();

    $pendapatan = Penyewaan::selectRaw('MONTH(tanggal_sewa) as bulan, SUM(total) as total')
        ->where('cabang_idcabang', $cabangId)
        ->where('status_penyewaan', 'selesai')
        ->whereYear('tanggal_sewa', $tahun)
        ->groupBy('bulan')
        ->pluck('total','bulan');

    $dataBulanan = [];
    for ($i=1; $i<=12; $i++) {
        $dataBulanan[] = $pendapatan[$i] ?? 0;
    }

    // ================= TOP PRODUK =================
    $alat = DB::table('item_penyewaan')
    ->join('produk', 'produk.idproduk', '=', 'item_penyewaan.produk_idproduk')
    ->join('penyewaan', 'penyewaan.idpenyewaan', '=', 'item_penyewaan.penyewaan_idpenyewaan')
    ->where('penyewaan.cabang_idcabang', $cabangId)
    ->where('penyewaan.status_penyewaan', 'selesai')
    ->whereYear('penyewaan.tanggal_sewa', $tahun) 
    ->select(
        'produk.nama_produk',
        DB::raw('SUM(item_penyewaan.qty) as total')
    )
    ->groupBy('produk.nama_produk')
    ->orderByDesc('total')
    ->limit(3)
    ->get();

    $totalQty = $alat->sum('total');

    $alatPersen = $alat->map(function($a) use ($totalQty){
        return [
            'nama' => $a->nama_produk,
            'persen' => $totalQty > 0 ? round(($a->total / $totalQty) * 100) : 0
        ];
    });

    return view('/dashboard_cabang', compact(
        'totalPenyewa',
        'totalPenyewaan',
        'totalAlat',
        'totalKategori',
        'dataBulanan',
        'alatPersen',
        'tahun'
    ));
}

       /* ================= ADMIN PUSAT ================= */
if ($user->status === 'admin_pusat') {

    $pusatId = $user->adminPusat->idadmin_pusat ?? null;

    if (!$pusatId) {
        abort(404, 'Pusat tidak ditemukan');
    }

   // ================= FILTER TAHUN =================
    $tahun = $request->tahun ?? date('Y');

    $totalPenyewa = Penyewaan::where('admin_pusat_idadmin_pusat', $pusatId)
        ->whereYear('tanggal_sewa', $tahun)
        ->distinct('penyewa_idpenyewa')
        ->count('penyewa_idpenyewa');

    $totalPenyewaan = Penyewaan::where('admin_pusat_idadmin_pusat', $pusatId)
        ->whereYear('tanggal_sewa', $tahun)
        ->count();

    $totalAlat = Produk::where('admin_pusat_idadmin_pusat', $pusatId)
        ->whereYear('created_at', $tahun)
        ->count();

    $totalKategori = Kategori::whereYear('created_at', $tahun)
        ->count();

    $pendapatan = Penyewaan::selectRaw('MONTH(tanggal_sewa) as bulan, SUM(total) as total')
        ->where('admin_pusat_idadmin_pusat', $pusatId)
        ->where('status_penyewaan', 'selesai')
        ->whereYear('tanggal_sewa', $tahun)
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

    $dataBulanan = [];
    for ($i = 1; $i <= 12; $i++) {
        $dataBulanan[] = $pendapatan[$i] ?? 0;
    }

    // ================= TOP PRODUK PUSAT =================
    $alat = DB::table('item_penyewaan')
    ->join('produk', 'produk.idproduk', '=', 'item_penyewaan.produk_idproduk')
    ->join('penyewaan', 'penyewaan.idpenyewaan', '=', 'item_penyewaan.penyewaan_idpenyewaan')
    ->where('penyewaan.admin_pusat_idadmin_pusat', $pusatId)
    ->where('penyewaan.status_penyewaan', 'selesai')
    ->whereYear('penyewaan.tanggal_sewa', $tahun) 
    ->select(
        'produk.nama_produk',
        DB::raw('SUM(item_penyewaan.qty) as total')
    )
    ->groupBy('produk.nama_produk')
    ->orderByDesc('total')
    ->limit(3)
    ->get();

    $totalQty = $alat->sum('total');

    $alatPersen = $alat->map(function ($a) use ($totalQty) {
        return [
            'nama' => $a->nama_produk,
            'persen' => $totalQty > 0 ? round(($a->total / $totalQty) * 100) : 0
        ];
    });

    return view('dashboard_pusat', compact(
        'totalPenyewa',
        'totalPenyewaan',
        'totalAlat',
        'totalKategori',
        'dataBulanan',
        'alatPersen',
        'tahun'
    ));
}
        /* ================= OWNER ================= */
if ($user->status === 'owner') {

    // ================= FILTER =================
    $tahunCard = $request->tahun_card ?? date('Y');

    $tahunPendapatan = $request->tahun_pendapatan ?? date('Y');
    $bulanPendapatan = $request->bulan_pendapatan;

    // ================= QUERY PENDAPATAN =================
    $pendapatanQuery = Penyewaan::where('status_penyewaan', 'selesai');

    $pendapatanQuery->whereYear('tanggal_sewa', $tahunPendapatan);

    if ($bulanPendapatan) {
        $pendapatanQuery->whereMonth('tanggal_sewa', $bulanPendapatan);
    }

    // ================= QUERY DONUT =================
    $itemQuery = DB::table('item_penyewaan')
        ->join('produk', 'produk.idproduk', '=', 'item_penyewaan.produk_idproduk')
        ->join('penyewaan', 'penyewaan.idpenyewaan', '=', 'item_penyewaan.penyewaan_idpenyewaan')
        ->where('penyewaan.status_penyewaan', 'selesai')
        ->whereYear('penyewaan.tanggal_sewa', $tahunCard);

    // ================= PENDAPATAN =================
   $pendapatanList = Penyewaan::leftJoin('cabang', 'penyewaan.cabang_idcabang', '=', 'cabang.idcabang')
    ->selectRaw('
        COALESCE(cabang.nama_cabang, "OutdoorKriss Tegalsari (Pusat)") as nama,
        SUM(penyewaan.total) as total
    ')
    ->where('status_penyewaan', 'selesai')
    ->whereYear('tanggal_sewa', $tahunPendapatan)
    ->groupBy('cabang.idcabang', 'cabang.nama_cabang')
    ->get();

    $grandTotal = $pendapatanList->sum('total');

    $pendapatanList = $pendapatanList->map(function ($item) use ($grandTotal) {
        return [
            'nama' => $item->nama,
            'total' => $item->total,
            'persen' => $grandTotal > 0
                ? round(($item->total / $grandTotal) * 100)
                : 0
        ];
    });

    // ================= DONUT =================
    $alat = (clone $itemQuery)
        ->select('produk.nama_produk', DB::raw('SUM(item_penyewaan.qty) as total'))
        ->groupBy('produk.nama_produk')
        ->orderByDesc('total')
        ->limit(3)
        ->get();

    $totalQty = $alat->sum('total');

    $alatPersen = $alat->map(function ($a) use ($totalQty) {
        return [
            'nama' => $a->nama_produk,
            'persen' => $totalQty > 0
                ? round(($a->total / $totalQty) * 100)
                : 0
        ];
    });

    // ================= CARD GLOBAL =================
    $totalCabang = Cabang::whereYear('created_at', $tahunCard)->count();
    $totalPenyewaan = Penyewaan::whereYear('tanggal_sewa', $tahunCard)->count();
    $totalAlat = Produk::whereYear('created_at', $tahunCard)->count();
    $totalKategori = Kategori::whereYear('created_at', $tahunCard)->count();

    return view('dashboard_owner', compact(
        'pendapatanList',
        'totalCabang',
        'totalPenyewaan',
        'totalAlat',
        'totalKategori',
        'tahunCard',
        'tahunPendapatan',
        'bulanPendapatan',
        'alatPersen'
    ));
}
}
}
