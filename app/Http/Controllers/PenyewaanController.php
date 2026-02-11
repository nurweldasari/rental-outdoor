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
    ) + 1;

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

    public function detail($id)
{
    $penyewaan = Penyewaan::with(['itemPenyewaan.produk'])
        ->where('idpenyewaan', $id)
        ->firstOrFail();

    return view('item_penyewaan', compact('penyewaan'));
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
        ->where('status_penyewaan', 'menunggu_pembayaran')
        ->orderBy('created_at', 'desc')
        ->get();

    // ✅ SEDANG / SUDAH DISEWA
    $penyewaanAktif = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
        ->whereIn('status_penyewaan', ['sedang_disewa'])
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

    // Query semua penyewaan khusus cabang admin
    $query = Penyewaan::with('penyewa', 'cabang')
        ->where('cabang_idcabang', $cabangId)
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




// Konfirmasi pembayaran (admin)
public function konfirmasiBayar($id)
{
    $penyewaan = Penyewaan::findOrFail($id);

    if($penyewaan->status_penyewaan === 'menunggu_pembayaran'){
        $penyewaan->update([
            'status_penyewaan' => 'sedang_disewa',
            'tanggal_sewa'     => now(),
        ]);
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Tidak bisa dikonfirmasi']);
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

}
