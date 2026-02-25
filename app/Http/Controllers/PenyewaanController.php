<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penyewaan;
use App\Models\ItemPenyewaan;
use App\Models\StokCabang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenyewaanController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $penyewa = $user->penyewa ?? null;

        if (!$user || !$penyewa) {
            abort(403, 'Akun ini bukan penyewa');
        }

        $request->validate([
            'tanggal_sewa'    => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_sewa',
            'metode_bayar'    => 'required|in:cash,transfer',
            'produk_cabang'   => 'required|array',
            'qty'             => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            /* ================= TANGGAL ================= */
            $tanggalSewa    = Carbon::parse($request->tanggal_sewa)->startOfDay();
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
           $durasiHari = Carbon::parse($request->tanggal_sewa)
    ->startOfDay()
    ->diffInDays(
        Carbon::parse($request->tanggal_selesai)->startOfDay()
    );


            /* ================= TENTUKAN CABANG ================= */
            $cabangIdFinal = null;

            foreach ($request->produk_cabang as $idstok) {
                $stok = StokCabang::findOrFail($idstok);

                // kalau produk dari cabang → ambil cabangnya
                if ($stok->cabang_idcabang) {
                    $cabangIdFinal = $stok->cabang_idcabang;
                    break;
                }
            }

            /* ================= SIMPAN PENYEWAAN ================= */
            $penyewaan = Penyewaan::create([
                'tanggal_sewa'    => $tanggalSewa,
                'tanggal_selesai' => $tanggalSelesai,
                'total'           => 0,
                'total_produk'    => 0,
                'status_penyewaan'=> 'menunggu_pembayaran',
                'metode_bayar'    => $request->metode_bayar,
                'batas_pembayaran'=> now()->addHours(2),

                'penyewa_idpenyewa' => $penyewa->idpenyewa,
                'cabang_idcabang'     => $cabangIdFinal, // ✅ null = pusat
                'admin_pusat_idadmin_pusat' => null,
            ]);

            /* ================= ITEM PENYEWAAN ================= */
            $total = 0;
            $totalProduk = 0;

            foreach ($request->produk_cabang as $i => $idstok) {
                $qty = (int) ($request->qty[$i] ?? 0);
                if ($qty < 1) continue;

                $stokCabang = StokCabang::findOrFail($idstok);

                if ($qty > $stokCabang->jumlah) {
                    throw new \Exception('Stok tidak mencukupi');
                }

                $harga    = $stokCabang->produk->harga;
                $subtotal = $harga * $qty * $durasiHari;

                ItemPenyewaan::create([
    'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
    'produk_idproduk'       => $stokCabang->produk_idproduk,
    'harga'                 => $harga,
    'qty'                   => $qty,
    'subtotal'              => $subtotal,
]);

// 🔥 KURANGI STOK CABANG
$stokCabang->decrement('jumlah', $qty);


                $total += $subtotal;
                $totalProduk += $qty;
            }

            /* ================= UPDATE TOTAL ================= */
            $penyewaan->update([
                'total'        => $total,
                'total_produk' => $totalProduk,
            ]);

            DB::commit();

            return redirect()
                ->route('item_penyewaan', $penyewaan->idpenyewaan);

        }  catch (\Exception $e) {
    DB::rollBack();
    dd($e->getMessage());

        }
    }

public function detailPenyewa($id)
{
    if (!auth()->check()) {
        abort(403);
    }

    $penyewaan = Penyewaan::with([
        'penyewa.user',
        'itemPenyewaan.produk',
        'cabang'
    ])
    ->where('idpenyewaan', $id)
    ->whereHas('penyewa.user', function ($q) {
        $q->where('idusers', auth()->user()->idusers);
    })
    ->firstOrFail();

    return view('detail_sewa', compact('penyewaan'));
}

public function riwayat()
{
    $user = Auth::user();
    $penyewa = $user->penyewa ?? null;

    if (!$penyewa) {
        abort(403, 'Akun ini bukan penyewa');
    }

    // ⏳ BELUM BAYAR
    $belumBayar = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
    ->whereIn('status_penyewaan', ['menunggu_pembayaran'])
    ->with(['cabang', 'cabang.adminCabang.user', 'penyewa.user'])
    ->orderBy('created_at', 'desc')
    ->get();

$penyewaanAktif = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
    ->whereIn('status_penyewaan', ['sedang_disewa'])
    ->with(['cabang', 'cabang.adminCabang.user', 'penyewa.user'])
    ->orderBy('created_at', 'desc')
    ->get();

    return view('item_penyewaan', compact('belumBayar', 'penyewaanAktif'));
}
public function selesai() {
    $user = auth()->user();
    $penyewa = $user->penyewa ?? null;
    if (!$penyewa) abort(403, 'Akun ini bukan penyewa');

    $penyewaanSelesai = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
        ->where('status_penyewaan', 'selesai')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('riwayat_penyewaan', compact('penyewaanSelesai'));
}

public function uploadPage($id)
{
    $penyewaan = Penyewaan::findOrFail($id);

    // Hanya bisa akses jika status menunggu pembayaran
    if ($penyewaan->status_penyewaan !== 'menunggu_pembayaran') {
        return back()->with('error', 'Penyewaan ini tidak bisa diupload bukti bayar');
    }

    return view('upload_pembayaran', compact('penyewaan'));
}

public function uploadBuktiBayar(Request $request, $idpenyewaan)
{
    $request->validate([
        'bukti_bayar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $penyewaan = Penyewaan::findOrFail($idpenyewaan);

    if($request->hasFile('bukti_bayar')){
        $file = $request->file('bukti_bayar');
        $filename = 'bukti_'.$idpenyewaan.'_'.time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/bukti_bayar'), $filename);

        $penyewaan->bukti_bayar = 'uploads/bukti_bayar/'.$filename;
        $penyewaan->save(); // status tetap menunggu konfirmasi
    }

    return redirect()->route('penyewaan.riwayat')
                     ->with('success', 'Bukti bayar berhasil diupload, tunggu konfirmasi admin.');
}

public function adminIndex(Request $request)
{
    $user = auth()->user();

    // Ambil relasi adminCabang
    $adminCabang = $user->adminCabang;
    $cabangId = $adminCabang->cabang_idcabang ?? null;

    if (!$cabangId) {
        abort(403, 'Admin belum terkait cabang');
    }

    $query = Penyewaan::with(['penyewa.user', 'cabang'])
    ->where('cabang_idcabang', $cabangId)
    ->whereIn('status_penyewaan', [
        'menunggu_pembayaran',
        'sedang_disewa',
        'dibatalkan'
    ])
    ->orderBy('tanggal_sewa', 'desc');

    // Filter pencarian nama / no telepon penyewa
    if ($request->search) {
        $query->whereHas('penyewa', function($q) use ($request) {
            $q->where('nama', 'like', '%' . $request->search . '%')
              ->orWhere('no_telepon', 'like', '%' . $request->search . '%');
        });
    }

    $penyewaanList = $query->paginate($request->get('per_page', 10))
                            ->withQueryString();

    return view('data_penyewaan', compact('penyewaanList'));
}

public function adminDetail($id)
{
    $user = auth()->user();
    $adminCabang = $user->adminCabang;

    if (!$adminCabang) {
        abort(403, 'Bukan admin cabang');
    }

    $penyewaan = Penyewaan::with([
        'penyewa.user',
        'itemPenyewaan.produk',
        'cabang'
    ])
    ->where('idpenyewaan', $id)
    ->where('cabang_idcabang', $adminCabang->cabang_idcabang) // 🔥 hanya cabangnya sendiri
    ->firstOrFail();

    return view('detail_penyewaan', compact('penyewaan'));
}

public function konfirmasiBayar($id)
{
    $user = auth()->user();
    $adminCabang = $user->adminCabang;

    if (!$adminCabang) {
        abort(403, 'Bukan admin cabang');
    }

    $penyewaan = Penyewaan::where('idpenyewaan', $id)
        ->where('cabang_idcabang', $adminCabang->cabang_idcabang)
        ->firstOrFail();

    if ($penyewaan->status_penyewaan === 'menunggu_pembayaran') {

        $penyewaan->update([
            'status_penyewaan' => 'sedang_disewa'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pembayaran berhasil dikonfirmasi');
    }

    return redirect()
        ->back()
        ->with('error', 'Tidak bisa dikonfirmasi');
}
public function selesaiAdmin($id)
{
    $user = auth()->user();
    $adminCabang = $user->adminCabang;

    if (!$adminCabang) {
        abort(403, 'Bukan admin cabang');
    }

    $penyewaan = Penyewaan::with('itemPenyewaan')
        ->where('idpenyewaan', $id)
        ->where('cabang_idcabang', $adminCabang->cabang_idcabang)
        ->firstOrFail();

    // ❌ Hanya bisa jika sedang_disewa
    if ($penyewaan->status_penyewaan !== 'sedang_disewa') {
        return redirect()->back()
            ->with('error', 'Tidak bisa diselesaikan');
    }

    DB::beginTransaction();

    try {

        // 🔥 1. Kembalikan stok ke cabang
        foreach ($penyewaan->itemPenyewaan as $item) {

            StokCabang::where('produk_idproduk', $item->produk_idproduk)
                ->where('cabang_idcabang', $penyewaan->cabang_idcabang)
                ->increment('jumlah', $item->qty);
        }

        // 🔥 2. Update status & tanggal kembali
        $penyewaan->update([
            'status_penyewaan' => 'selesai',
            'tanggal_kembali'  => now(),
        ]);

        DB::commit();

        // 🔥 3. Redirect ke halaman RIWAYAT (bukan back lagi)
        return redirect()
            ->route('admin.data_riwayat')
            ->with('success', 'Penyewaan berhasil diselesaikan');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()->back()
            ->with('error', 'Gagal menyelesaikan penyewaan');
    }
}

public function adminRiwayat(Request $request)
{
    $user = auth()->user();
    $adminCabang = $user->adminCabang;

    if (!$adminCabang) {
        abort(403, 'Bukan admin cabang');
    }

    $perPage = $request->get('per_page', 10);
    $search  = $request->get('search');

    $query = Penyewaan::with(['penyewa.user'])
        ->where('cabang_idcabang', $adminCabang->cabang_idcabang)
        ->where('status_penyewaan', 'selesai');

    if ($search) {
        $query->whereHas('penyewa.user', function ($q) use ($search) {
            $q->where('nama', 'like', "%$search%")
              ->orWhere('no_telepon', 'like', "%$search%");
        });
    }

    $riwayatList = $query->orderBy('tanggal_kembali', 'desc')
                         ->paginate($perPage)
                         ->withQueryString();

    return view('data_riwayat', compact('riwayatList'));
}


public function cancel($id)
{
    $penyewaan = Penyewaan::with('itemPenyewaan')->findOrFail($id);

    // hanya boleh cancel jika masih menunggu pembayaran
    if ($penyewaan->status_penyewaan !== 'menunggu_pembayaran') {
        return response()->json([
            'success' => false,
            'message' => 'Tidak bisa dibatalkan'
        ]);
    }

    DB::beginTransaction();

    try {
        // 🔥 KEMBALIKAN STOK
        foreach ($penyewaan->itemPenyewaan as $item) {
            StokCabang::where('produk_idproduk', $item->produk_idproduk)
                ->where('cabang_idcabang', $penyewaan->cabang_idcabang)
                ->increment('jumlah', $item->qty);
        }

        // ubah status
        $penyewaan->update([
            'status_penyewaan' => 'dibatalkan'
        ]);

        DB::commit();

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal membatalkan penyewaan'
        ]);
    }
}

public function laporan(Request $request)
{
    $query = Penyewaan::with([
        'penyewa.user',
        'itemPenyewaan.produk'
    ])
    ->where('status_penyewaan', 'selesai')
    ->orderBy('tanggal_sewa', 'desc');

    // FILTER PERIODE
    if ($request->start && $request->end) {
        $query->whereBetween('tanggal_sewa', [
            $request->start,
            $request->end
        ]);
    }

    $penyewaan = $query->get();

    $totalPendapatan = $penyewaan->sum('total');

    return view('laporan', compact(
        'penyewaan',
        'totalPendapatan'
    ));
}

}
