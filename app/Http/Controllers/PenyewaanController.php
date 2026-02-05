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

    if (!$user || !$user->penyewa) {
        abort(403, 'Akun ini bukan penyewa');
    }

    $produkCabang = $request->input('produk_cabang', []);
    $qtyList = $request->input('qty', []);

    if (empty($produkCabang) || empty($qtyList)) {
        return back()->with('error', 'Keranjang masih kosong!');
    }

    if (!session('cabang_id')) {
        return back()->with('error', 'Cabang belum dipilih!');
    }

    DB::beginTransaction();
    try {
        $penyewaan = Penyewaan::create([
            'tanggal_sewa' => Carbon::parse($request->tanggal_sewa)->startOfDay(),
            'tanggal_selesai' => Carbon::parse($request->tanggal_selesai)->endOfDay(),
            'total' => 0,
            'total_produk' => 0,
            'status_penyewaan' => 'menunggu_pembayaran',
            'metode_bayar' => $request->metode_bayar,
            'batas_pembayaran' => Carbon::now()->addHours(2),
            'penyewa_idpenyewa' => $user->penyewa->idpenyewa,
            'cabang_idcabang' => session('idcabang'),
            'admin_pusat_idadmin_pusat' => 1,
        ]);

        $total = 0;
        $totalProduk = 0;

        foreach ($produkCabang as $i => $idstok) {
            $qty = $qtyList[$i] ?? 0;
            if ($qty < 1) continue;

            $stokCabang = StokCabang::findOrFail($idstok);
            $harga = $stokCabang->produk->harga;
            $subtotal = $harga * $qty;

            ItemPenyewaan::create([
                'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
                'produk_idproduk' => $stokCabang->produk_idproduk,
                'harga' => $harga,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
            $totalProduk += $qty;
        }

        $penyewaan->update([
            'total' => $total,
            'total_produk' => $totalProduk,
        ]);

        session()->forget('cart');

        DB::commit();

        return redirect()->route('penyewaan.detail', $penyewaan->idpenyewaan);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal membuat penyewaan: '.$e->getMessage());
    }
}

}
