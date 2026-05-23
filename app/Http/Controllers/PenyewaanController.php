<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penyewaan;
use App\Models\ItemPenyewaan;
use App\Models\StokCabang;
use App\Models\Produk;
use App\Models\Penyewa;
use App\Models\Kategori;
use App\Models\Rekening;
use App\Models\Cabang;
use App\Models\Paket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        'produk_cabang'   => 'nullable|array',
        'produk'          => 'nullable|array',
       
        'qty'    => 'required|array',
        'qty.*'  => 'required|integer|min:1',

        'type'   => 'required|array',
        'type.*' => 'required|in:produk,paket',
    ]);

    DB::beginTransaction();

    try {

        /* ================= AMBIL SESSION TOKO ================= */
        $tipe   = session('tipe_toko'); // cabang / pusat
        $tokoId = session('toko_id');

        /* ================= TANGGAL ================= */
        $tanggalSewa    = Carbon::parse($request->tanggal_sewa)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();

        $durasiHari = Carbon::parse($request->tanggal_sewa)
            ->startOfDay()
            ->diffInDays(
                Carbon::parse($request->tanggal_selesai)->startOfDay()
            );

        if ($durasiHari < 1) {
            $durasiHari = 1;
        }

        /* ================= TENTUKAN ASAL TOKO ================= */
        if ($tipe === 'cabang') {
            $cabangIdFinal = $tokoId;
            $adminPusatId  = null;
        } elseif ($tipe === 'pusat') {
            $cabangIdFinal = null;
            $adminPusatId  = $tokoId;
        } else {
            throw new \Exception('Tipe toko tidak valid');
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
            'cabang_idcabang'   => $cabangIdFinal,
            'admin_pusat_idadmin_pusat' => $adminPusatId,
        ]);

        /* ================= ITEM ================= */
        $total = 0;
        $totalProduk = 0;

        $items = $tipe === 'cabang'
    ? ($request->produk_cabang ?? [])
    : ($request->produk ?? []);

foreach ($items as $i => $id) {
    if (!isset($request->qty[$i], $request->type[$i])) continue;

            $qty  = (int) ($request->qty[$i] ?? 0);
            $type = $request->type[$i] ?? 'produk';

            if ($qty < 1) continue;

            /* ================= PAKET ================= */
            if ($type === 'paket') {

    $paket = Paket::findOrFail($id);

    $harga = $paket->hargaTerbaru->harga ?? 0;
    /* ================= PAKET CABANG ================= */
    if ($tipe === 'cabang') {

        $paket->load('detail.stokCabang.produk');

        foreach ($paket->detail as $d) {

            $stok = StokCabang::findOrFail($d->stok_cabang_id);

            $jumlahPotong = $d->qty * $qty;

            if ($stok->jumlah < $jumlahPotong) {
                throw new \Exception('Stok paket tidak mencukupi');
            }

            $stok->decrement('jumlah', $jumlahPotong);
        }

    }

    /* ================= PAKET PUSAT ================= */
    else {

        $paket->load('detail.produk');

        foreach ($paket->detail as $d) {

            $produk = Produk::findOrFail($d->produk_idproduk);

            $jumlahPotong = $d->qty * $qty;

            if ($produk->stok_pusat < $jumlahPotong) {
                throw new \Exception('Stok paket pusat tidak mencukupi');
            }

            $produk->decrement('stok_pusat', $jumlahPotong);
        }
    }

    ItemPenyewaan::create([
        'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
        'produk_idproduk'       => null,
        'paket_id'              => $paket->id,
        'type'                  => 'paket',
        'nama_paket'            => $paket->nama_paket,
        'detail_paket' => json_encode(
            $paket->detail->map(function ($d) use ($tipe) {

                if ($tipe === 'cabang') {
                    return [
                        'produk_id'   => $d->stokCabang->produk->idproduk ?? null,
                        'nama_produk' => $d->stokCabang->produk->nama_produk ?? '-',
                        'qty'         => $d->qty
                    ];
                } elseif ($tipe === 'pusat') {
                    return [
                        'produk_id'   => $d->produk->idproduk ?? null,
                        'nama_produk' => $d->produk->nama_produk ?? '-',
                        'qty'         => $d->qty
                    ];
                }

                return [
                    'produk_id'   => null,
                    'nama_produk' => '-',
                    'qty'         => $d->qty
                ];
            })
        ),
        'harga'                 => $harga,
        'qty'                   => $qty,
        'subtotal'              => $harga * $qty * $durasiHari,
    ]);

    $total += $harga * $qty * $durasiHari;
    $totalProduk += $qty;
}

            /* ================= PRODUK ================= */
            else {

                if ($tipe === 'cabang') {

                    $stok = StokCabang::with('produk')->findOrFail($id);

                    if ($qty > $stok->jumlah) {
                        throw new \Exception('Stok cabang tidak mencukupi');
                    }
                    $produk = $stok->produk;
                    $harga = $produk->hargaAktif->harga ?? 0;

                    $stok->decrement('jumlah', $qty);
                    $produkId = $stok->produk_idproduk;

                } else {

                    $produk = Produk::findOrFail($id);

                    if ($qty > $produk->stok_pusat) {
                        throw new \Exception('Stok pusat tidak mencukupi');
                    }

                    $harga = $produk->hargaAktif->harga ?? 0;
                    $produk->decrement('stok_pusat', $qty);
                    $produkId = $produk->idproduk;
                }

                ItemPenyewaan::create([
                    'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
                    'produk_idproduk'       => $produkId,
                    'paket_id'              => null,
                    'type'                  => 'produk',
                    'nama_produk'           => $produk->nama_produk,
                    'jenis_skala'           => $produk->jenis_skala,
                    'harga'                 => $harga,
                    'qty'                   => $qty,
                    'subtotal'              => $harga * $qty * $durasiHari,
                ]);

                $total += $harga * $qty * $durasiHari;
                $totalProduk += $qty;
            }
        }

        /* ================= UPDATE TOTAL ================= */
        $penyewaan->update([
            'total' => $total,
            'total_produk' => $totalProduk,
        ]);

        DB::commit();
$tipe = session('tipe_toko');

if ($tipe === 'pusat') {
    return redirect()->route('item_penyewaan_pusat', $penyewaan->idpenyewaan);
}
        return redirect()
            ->route('item_penyewaan', $penyewaan->idpenyewaan);

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

public function detailPenyewa($id)
{
    if (!auth()->check()) {
        abort(403);
    }

    $penyewaan = Penyewaan::with([
        'penyewa.user',
        'cabang',

        // 🔥 produk biasa
        'itemPenyewaan.produk',

        // 🔥 paket + isi paket (INI PENTING)
        'itemPenyewaan.paket.detail.stokCabang.produk'
    ])
    ->where('idpenyewaan', $id)
    ->whereHas('penyewa.user', function ($q) {
        $q->where('idusers', auth()->user()->idusers);
    })
    ->firstOrFail();

    return view('detail_sewa', compact('penyewaan'));
}
public function riwayat(Request $request)
{
    $user = Auth::user();
    $penyewa = $user->penyewa ?? null;

    if (!$penyewa) {
        abort(403, 'Akun ini bukan penyewa');
    }
    $perPage = $request->get('per_page', 10);

    // ⏳ BELUM BAYAR
    $belumBayar = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
    ->whereNotNull('cabang_idcabang')
    ->whereIn('status_penyewaan', ['menunggu_pembayaran'])
    ->with(['cabang', 'cabang.adminCabang.user', 'penyewa.user'])
    ->orderBy('created_at', 'desc')
    ->paginate($perPage);

    $penyewaanAktif = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
    ->whereNotNull('cabang_idcabang')
    ->whereIn('status_penyewaan', ['sedang_disewa'])
    ->with(['cabang', 'cabang.adminCabang.user', 'penyewa.user'])
    ->orderBy('created_at', 'desc')
    ->paginate($perPage);

    return view('item_penyewaan', compact('belumBayar', 'penyewaanAktif'));
}
public function selesai(Request $request) {
    $user = Auth::user();
    $penyewa = $user->penyewa ?? null;

    if (!$penyewa) {
        abort(403, 'Akun ini bukan penyewa');
    }
$perPage = $request->get('per_page', 10);
    $penyewaanSelesai = Penyewaan::with(['penyewa.user'])
        ->whereNotNull('cabang_idcabang')
        ->where('penyewa_idpenyewa', $penyewa->idpenyewa) // ✅ FIX DI SINI
        ->where('status_penyewaan', 'selesai')
        
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    return view('riwayat_penyewaan', compact('penyewaanSelesai'));
}

public function uploadPage($id)
{
    $penyewaan = Penyewaan::findOrFail($id);

    // Hanya bisa akses jika status menunggu pembayaran
    if ($penyewaan->status_penyewaan !== 'menunggu_pembayaran') {
        return back()->with('error', 'Penyewaan ini tidak bisa diupload bukti bayar');
    }
    $rekening = $penyewaan->cabang->rekening ?? null;
    if ($penyewaan->batas_pembayaran) {
        $sisaDetik = now()->diffInSeconds($penyewaan->batas_pembayaran, false);
        if ($sisaDetik < 0) {
            $sisaDetik = 0;
        }
    } else {
        $sisaDetik = 0;
    }

    return view('upload_pembayaran', compact('penyewaan','rekening', 'sisaDetik'));
}

public function uploadBuktiBayar(Request $request, $idpenyewaan)
{
    $request->validate([
        'bukti_bayar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $penyewaan = Penyewaan::findOrFail($idpenyewaan);

    if ($request->hasFile('bukti_bayar')) {
    $file = $request->file('bukti_bayar');
    $filename = 'bukti_'.$idpenyewaan.'_'.time().'.'.$file->getClientOriginalExtension();
    $path = $file->storeAs(
        'bukti_bayar',
        $filename,
        'public'
    );
    $penyewaan->bukti_bayar = $path;
    $penyewaan->save();
}

    return redirect()->route('item_penyewaan')
                     ->with('success', 'Bukti bayar berhasil diupload, tunggu konfirmasi admin.');
}

public function adminIndex(Request $request)
{
    $user = Auth::user();

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
    $query->whereHas('penyewa.user', function ($q) use ($request) {
    $q->where('nama', 'like', '%' . $request->search . '%')
      ->orWhere('no_telepon', 'like', '%' . $request->search . '%');
});

    $penyewaanList = $query->paginate($request->get('per_page', 10))
                            ->withQueryString();

    return view('data_penyewaan', compact('penyewaanList'));
}

public function adminDetail($id)
{
    $user = Auth::user();
    $adminCabang = $user->adminCabang;

    if (!$adminCabang) {
        abort(403, 'Bukan admin cabang');
    }

    $penyewaan = Penyewaan::with([
        'penyewa.user',
        'itemPenyewaan.produk',
        // 🔥 paket + isi paket (INI PENTING)
        'itemPenyewaan.paket.detail.stokCabang.produk',
        'cabang'
    ])
    ->where('idpenyewaan', $id)
    ->where('cabang_idcabang', $adminCabang->cabang_idcabang) // 🔥 hanya cabangnya sendiri
    ->firstOrFail();

    return view('detail_penyewaan', compact('penyewaan'));
}

public function konfirmasiBayar($id)
{
    $user = Auth::user();
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
    $user = Auth::user();
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

    // ================= PRODUK =================
    if ($item->type === 'produk' && $item->produk_idproduk) {

        StokCabang::where('produk_idproduk', $item->produk_idproduk)
            ->where('cabang_idcabang', $penyewaan->cabang_idcabang)
            ->increment('jumlah', $item->qty);
    }

    // ================= PAKET =================
    elseif ($item->type === 'paket') {

        if (!$item->detail_paket) continue;

        $details = json_decode($item->detail_paket, true);

        foreach ($details as $d) {

            StokCabang::where('produk_idproduk', $d['produk_id'])
                ->where('cabang_idcabang', $penyewaan->cabang_idcabang)
                ->increment('jumlah', $d['qty'] * $item->qty);
        }
    }
}

        // 🔥 2. Update status & tanggal kembali
        $penyewaan->update([
            'status_penyewaan' => 'selesai',
            'tanggal_kembali'  => now(),
        ]);

        DB::commit();

        // 🔥 3. Redirect ke halaman RIWAYAT (bukan back lagi)
        return redirect()
            ->route('data_riwayat')
            ->with('success', 'Penyewaan berhasil diselesaikan');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()->back()
            ->with('error', 'Gagal menyelesaikan penyewaan');
    }
}

public function adminRiwayat(Request $request)
{
    $user = Auth::user();
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

    // ================= PRODUK =================
    if ($item->type === 'produk' && $item->produk_idproduk) {

        StokCabang::where('produk_idproduk', $item->produk_idproduk)
            ->where('cabang_idcabang', $penyewaan->cabang_idcabang)
            ->increment('jumlah', $item->qty);
    }

    // ================= PAKET =================
    elseif ($item->type === 'paket') {

        if (!$item->detail_paket) continue;

        $details = json_decode($item->detail_paket, true);

        foreach ($details as $d) {

            StokCabang::where('produk_idproduk', $d['produk_id'])
                ->where('cabang_idcabang', $penyewaan->cabang_idcabang)
                ->increment('jumlah', $d['qty'] * $item->qty);
        }
    }
}
        // ubah status
        $penyewaan->update([
            'status_penyewaan' => 'dibatalkan'
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Penyewaan dibatalkan');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal membatalkan');
    }
}

public function createReservasi(Request $request, $id)
{
    $user = Auth::user();
    $adminCabang = $user->adminCabang;

    $rekening = $adminCabang->cabang->rekening ?? null;

    $penyewa = Penyewa::where('users_idusers', $id)->firstOrFail();

    $cabangId = $adminCabang->cabang_idcabang;

    session(['cabang_id' => $cabangId]);

    $kategoriList = Kategori::all();

    // ===================== PRODUK =====================
    $produkList = StokCabang::with('produk.kategori')
        ->where('cabang_idcabang', $cabangId)
        ->where('is_active', 1)
        ->where('jumlah', '>', 0)

        // 🔍 SEARCH
        ->when($request->search, function ($q) use ($request) {
            $q->whereHas('produk', function ($p) use ($request) {
                $p->where('nama_produk', 'like', '%' . $request->search . '%');
            });
        })

        // 📂 FILTER KATEGORI
        ->when($request->kategori, function ($q) use ($request) {
            $q->whereHas('produk', function ($p) use ($request) {
                $p->where('kategori_idkategori', $request->kategori);
            });
        })

        ->paginate(10)
        ->appends($request->query()); // biar filter & search tetap

    // ===================== PAKET =====================
    $paketList = Paket::with('detail.stokCabang.produk')
        ->where('cabang_id', $cabangId)
        ->where('is_active', 1)
        ->whereDoesntHave('detail', function ($q) {
            $q->whereHas('stokCabang', function ($s) {
                $s->whereColumn('stok_cabang.jumlah', '<', 'paket_detail.qty')
                  ->orWhere('stok_cabang.is_active', 0);
            });
        })
        ->get();

    return view('reservasi', compact(
        'penyewa',
        'kategoriList',
        'produkList',
        'paketList',
        'rekening'
    ));
}


public function reservasi(Request $request, $idpenyewa)
{
    $user = Auth::user();
    $adminCabang = $user->adminCabang;

    if (!$adminCabang) {
        abort(403, 'Bukan admin cabang');
    }

    $penyewa = Penyewa::where('users_idusers', $idpenyewa)->firstOrFail();

    $request->validate([
        'tanggal_sewa'    => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_sewa',
        'metode_bayar'    => 'required|in:cash,transfer',
        'produk_cabang'   => 'required|array',
        'qty'             => 'required|array',
    ]);

    DB::beginTransaction();

    try {

        $tanggalSewa    = Carbon::parse($request->tanggal_sewa)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();

         $durasiHari = Carbon::parse($request->tanggal_sewa)
            ->startOfDay()
            ->diffInDays(
                Carbon::parse($request->tanggal_selesai)->startOfDay()
            );

        if ($durasiHari < 1) {
            $durasiHari = 1;
        }

        // 🔥 Buat penyewaan
        $penyewaan = Penyewaan::create([
            'tanggal_sewa'    => $tanggalSewa,
            'tanggal_selesai' => $tanggalSelesai,
            'total'           => 0,
            'total_produk'    => 0,
            'status_penyewaan'=> 'sedang_disewa', // admin langsung aktif
            'metode_bayar'    => $request->metode_bayar,
            'batas_pembayaran'=> null,

            'penyewa_idpenyewa' => $penyewa->idpenyewa,
            'cabang_idcabang'   => $adminCabang->cabang_idcabang,
            'admin_pusat_idadmin_pusat' => null,
        ]);

        $total = 0;
        $totalProduk = 0;

        foreach ($request->produk_cabang as $i => $id) {

    $qty  = (int) ($request->qty[$i] ?? 0);
    $type = $request->type[$i] ?? 'produk';

    if ($qty < 1) continue;

    /* ================= PAKET ================= */
    if ($type === 'paket') {

        $paket = Paket::with('detail.stokCabang.produk')
            ->findOrFail($id);
       
        $harga = $paket->hargaTerbaru->harga ?? 0;

        foreach ($paket->detail as $d) {

            $stok = StokCabang::where('idstok', $d->stok_cabang_id)
                ->where('cabang_idcabang', $adminCabang->cabang_idcabang)
                ->firstOrFail();

            $jumlahPotong = $d->qty * $qty;

            if ($stok->jumlah < $jumlahPotong) {
                throw new \Exception('Stok paket tidak mencukupi');
            }

            $stok->decrement('jumlah', $jumlahPotong);
        }

        ItemPenyewaan::create([
    'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
    'produk_idproduk'       => null,
    'paket_id'              => $paket->id,
    'type'                  => 'paket',

    'nama_paket'            => $paket->nama_paket,

    'detail_paket' => json_encode(
        $paket->detail->map(function ($d) {
            return [
                'produk_id'   => $d->stokCabang->produk->idproduk ?? null,
                'nama_produk' => $d->stokCabang->produk->nama_produk ?? '-',
                'qty'         => $d->qty,
            ];
        })->values()
    ),

    'harga'    => $harga,
    'qty'      => $qty,
    'subtotal' => $harga * $qty * $durasiHari,
]);

        $total += $harga * $qty * $durasiHari;
        $totalProduk += $qty;
    }

    /* ================= PRODUK ================= */
    else {

        $stok = StokCabang::where('idstok', $id)
            ->where('cabang_idcabang', $adminCabang->cabang_idcabang)
            ->firstOrFail();

        if ($qty > $stok->jumlah) {
            throw new \Exception('Stok tidak mencukupi');
        }

        $produk = $stok->produk;
        $harga = $produk->hargaAktif->harga ?? 0;

        $stok->decrement('jumlah', $qty);

        ItemPenyewaan::create([
    'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
    'produk_idproduk'       => $stok->produk_idproduk,
    'paket_id'              => null,
    'type'                  => 'produk',

    'nama_produk'           => $produk->nama_produk,
    'jenis_skala'           => $produk->jenis_skala,

    'harga'                 => $harga,
    'qty'                   => $qty,
    'subtotal'              => $harga * $qty * $durasiHari,
]);

$total += $harga * $qty * $durasiHari;
$totalProduk += $qty;
    }
    }
        $penyewaan->update([
            'total'        => $total,
            'total_produk' => $totalProduk,
        ]);

        DB::commit();

        return redirect()
            ->route('data_penyewaan')
            ->with('success', 'Reservasi berhasil dibuat');

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}


//reservasi pusat
public function createReservasiPusat(Request $request, $id)
{
    $user = Auth::user();
    $adminPusat = $user->adminPusat;

    if (!$adminPusat && $user->status !== 'owner') {
        abort(403);
    }

    $penyewa = Penyewa::where('users_idusers', $id)->firstOrFail();

    $kategoriList = Kategori::all();

    $query = Produk::with('kategori');

    if ($request->search) {
        $query->where('nama_produk', 'like', '%' . $request->search . '%');
    }

    if ($request->skala) {
        $query->where('jenis_skala', $request->skala);
    }

    if ($request->kategori) {
        $query->where('kategori_idkategori', $request->kategori);
    }

    $produkList = $query
    ->where('stok_pusat', '>', 0)
    ->paginate(10)
    ->withQueryString();

// 🔥 hanya paket pusat & semua produk dalam paket harus stok > 0
$paketList = Paket::with('detail.produk')
    ->where('is_active', 1)
    ->whereNull('cabang_id')

    // paket harus punya detail
    ->whereHas('detail')

    // buang paket yang punya produk stok habis
    ->whereDoesntHave('detail.produk', function ($q) {
        $q->where('stok_pusat', '<=', 0);
    })

    ->get();

    return view('reservasi_pusat', compact(
        'penyewa',
        'kategoriList',
        'produkList',
        'paketList'
    ));
}

public function reservasiPusat(Request $request, $idpenyewa)
{
    $user = Auth::user();
    $adminPusat = $user->adminPusat;

    if (!$adminPusat && $user->status !== 'owner') {
        abort(403);
    }

    $penyewa = Penyewa::where('users_idusers', $idpenyewa)->firstOrFail();

    $request->validate([
        'tanggal_sewa'    => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_sewa',
        'metode_bayar'    => 'required|in:cash,transfer',
        'produk'          => 'required|array',
        'qty'             => 'required|array',
        'type'            => 'required|array',
    ]);

    DB::beginTransaction();

    try {

        $tanggalSewa    = Carbon::parse($request->tanggal_sewa)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();

        $durasiHari = Carbon::parse($request->tanggal_sewa)
            ->startOfDay()
            ->diffInDays(
                Carbon::parse($request->tanggal_selesai)->startOfDay()
            );

        if ($durasiHari < 1) {
            $durasiHari = 1;
        }

        $adminPusatId = $adminPusat->idadmin_pusat ?? 1;

        $penyewaan = Penyewaan::create([
            'tanggal_sewa'    => $tanggalSewa,
            'tanggal_selesai' => $tanggalSelesai,
            'total'           => 0,
            'total_produk'    => 0,
            'status_penyewaan'=> 'sedang_disewa',
            'metode_bayar'    => $request->metode_bayar,

            'penyewa_idpenyewa' => $penyewa->idpenyewa,
            'cabang_idcabang'   => null,
            'admin_pusat_idadmin_pusat' => $adminPusatId,
        ]);

        $total = 0;
        $totalProduk = 0;

        foreach ($request->produk as $i => $id) {

            $qty  = (int) ($request->qty[$i] ?? 0);
            $type = $request->type[$i] ?? 'produk';

            if ($qty < 1) continue;

            // ================= PAKET =================
            if ($type === 'paket') {

                $paket = Paket::whereNull('cabang_id')
                    ->with('detail.produk')
                    ->findOrFail($id);

                $harga = $paket->hargaTerbaru->harga ?? 0;
                
                foreach ($paket->detail as $d) {

                    if (!$d->produk_idproduk) {
                        throw new \Exception('Detail paket tidak valid');
                    }

                    $produk = Produk::findOrFail($d->produk_idproduk);

                    $jumlahPotong = $d->qty * $qty;

                    if ($produk->stok_pusat < $jumlahPotong) {
                        throw new \Exception('Stok paket pusat tidak cukup');
                    }

                    $produk->decrement('stok_pusat', $jumlahPotong);
                }

               ItemPenyewaan::create([
    'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
    'produk_idproduk'       => null,
    'paket_id'              => $paket->id,
    'type'                  => 'paket',

    'nama_paket'            => $paket->nama_paket,

    'detail_paket' => json_encode(
        $paket->detail->map(function ($d) {
            return [
                'produk_id'   => $d->produk->idproduk ?? null,
                'nama_produk' => $d->produk->nama_produk ?? '-',
                'qty'         => $d->qty,
            ];
        })->values()
    ),

    'harga'    => $harga,
    'qty'      => $qty,
    'subtotal' => $harga * $qty * $durasiHari,
]);

$total += $harga * $qty * $durasiHari;
$totalProduk += $qty;
            }

            // ================= PRODUK =================
            else {

                $produk = Produk::findOrFail($id);

                if ($qty > $produk->stok_pusat) {
                    throw new \Exception('Stok pusat tidak cukup');
                }

                $harga = $produk->hargaAktif->harga ?? 0;


                $produk->decrement('stok_pusat', $qty);

                $subtotal = $harga * $qty * $durasiHari;

                ItemPenyewaan::create([
                    'penyewaan_idpenyewaan' => $penyewaan->idpenyewaan,
                    'produk_idproduk'       => $produk->idproduk,
                    'paket_id'              => null,
                    'type'                  => 'produk',

                    'nama_produk'           => $produk->nama_produk,
                    'jenis_skala'           => $produk->jenis_skala,

                    'harga'                 => $harga,
                    'qty'                   => $qty,
                    'subtotal'              => $subtotal,
                ]);

                $total += $subtotal;
                $totalProduk += $qty;
            }
        }

        $penyewaan->update([
            'total' => $total,
            'total_produk' => $totalProduk
        ]);

        DB::commit();

        return redirect()->route('data_penyewaan_pusat')
            ->with('success', 'Reservasi pusat berhasil');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
public function pusatIndex(Request $request)
{
    $user = Auth::user();

    $adminPusat = $user->adminPusat;
    $isOwner = $user->status === 'owner';

    if (!$adminPusat && !$isOwner) {
        abort(403, 'Tidak punya akses');
    }

    $query = Penyewaan::with(['penyewa.user'])
    ->whereNull('cabang_idcabang')
    ->whereIn('status_penyewaan', [
        'menunggu_pembayaran',
        'sedang_disewa',
        'dibatalkan'
    ])
    ->orderByDesc('tanggal_sewa')
    ->orderByDesc('created_at'); 

    if (!$isOwner) {
    $query->where(function ($q) use ($adminPusat) {
        $q->where('admin_pusat_idadmin_pusat', $adminPusat->idadmin_pusat);
    });
}

    if ($request->search) {
        $query->whereHas('penyewa.user', function ($q) use ($request) {
            $q->where('nama', 'like', '%' . $request->search . '%')
              ->orWhere('no_telepon', 'like', '%' . $request->search . '%');
        });
    }

    $penyewaanList = $query->paginate($request->get('per_page', 10))
                          ->withQueryString();

    return view('data_penyewaan_pusat', compact('penyewaanList'));
}
public function pusatDetail($id)
{
    $user = Auth::user();

    $adminPusat = $user->adminPusat;
    $isOwner = $user->status === 'owner';

    if (!$adminPusat && !$isOwner) {
        abort(403, 'Tidak punya akses');
    }

    $query = Penyewaan::with([
        'penyewa.user',
        'itemPenyewaan.produk',
        'itemPenyewaan.paket.detail.produk'
    ])->where('idpenyewaan', $id);

    if (!$isOwner) {
        $query->where('admin_pusat_idadmin_pusat', $adminPusat->idadmin_pusat);
    }

    $penyewaan = $query->firstOrFail();

    return view('detail_penyewaan_pusat', compact('penyewaan'));
}
public function konfirmasiPusat($id)
{
    $user = Auth::user();

    $adminPusat = $user->adminPusat;
    $isOwner = $user->status === 'owner';

    if (!$adminPusat && !$isOwner) {
        abort(403, 'Tidak punya akses');
    }

    $query = Penyewaan::where('idpenyewaan', $id);

    if (!$isOwner) {
        $query->where('admin_pusat_idadmin_pusat', $adminPusat->idadmin_pusat);
    }

    $penyewaan = $query->firstOrFail();

    if ($penyewaan->status_penyewaan === 'menunggu_pembayaran') {
        $penyewaan->update([
            'status_penyewaan' => 'sedang_disewa'
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi');
    }

    return back()->with('error', 'Tidak bisa dikonfirmasi');
}
public function selesaiAdminPusat($id)
{
    $user = Auth::user();
    $adminPusat = $user->adminPusat;
    $isOwner = $user->status === 'owner';

    if (!$adminPusat && !$isOwner) {
        abort(403, 'Tidak punya akses');
    }

    $query = Penyewaan::with('itemPenyewaan')
        ->whereNull('cabang_idcabang')
        ->where('idpenyewaan', $id);

    if (!$isOwner) {
        $query->where('admin_pusat_idadmin_pusat', $adminPusat->idadmin_pusat);
    }

    $penyewaan = $query->firstOrFail();

    if ($penyewaan->status_penyewaan !== 'sedang_disewa') {
        return back()->with('error', 'Tidak bisa diselesaikan');
    }

    DB::beginTransaction();

    try {

        foreach ($penyewaan->itemPenyewaan as $item) {

            // ================= PRODUK =================
            if ($item->type === 'produk' && $item->produk_idproduk) {

                Produk::where('idproduk', $item->produk_idproduk)
                    ->increment('stok_pusat', $item->qty);
            }

            // ================= PAKET =================
           elseif ($item->type === 'paket') {

                if (!$item->detail_paket) continue;

                $details = json_decode($item->detail_paket, true);

                foreach ($details as $d) {

                    Produk::where('idproduk', $d['produk_id'])
                        ->increment('stok_pusat', $d['qty'] * $item->qty);
                }
            }
        }

        $penyewaan->update([
            'status_penyewaan' => 'selesai',
            'tanggal_kembali'  => now(),
        ]);

        DB::commit();

        return redirect()
            ->route('data_riwayat_pusat')
            ->with('success', 'Penyewaan selesai');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menyelesaikan');
    }
}
public function pusatRiwayat(Request $request)
{
    $user = Auth::user();

    $adminPusat = $user->adminPusat;
    $isOwner = $user->status === 'owner';

    if (!$adminPusat && !$isOwner) {
        abort(403, 'Tidak punya akses');
    }

    $query = Penyewaan::with(['penyewa.user'])
        ->whereNull('cabang_idcabang')
        ->where('status_penyewaan', 'selesai');

    if (!$isOwner) {
        $query->where('admin_pusat_idadmin_pusat', $adminPusat->idadmin_pusat);
    }

    if ($request->search) {
        $query->whereHas('penyewa.user', function ($q) use ($request) {
            $q->where('nama', 'like', "%{$request->search}%")
              ->orWhere('no_telepon', 'like', "%{$request->search}%");
        });
    }

    $riwayatList = $query->orderBy('tanggal_kembali', 'desc')
                         ->paginate(10)
                         ->withQueryString();

    return view('data_riwayat_pusat', compact('riwayatList'));
}
public function cancelPusat($id)
{
    $user = Auth::user();
    $adminPusat = $user->adminPusat;
    $isOwner = $user->status === 'owner';

    if (!$adminPusat && !$isOwner) {
        return redirect()->back()->with('error', 'Tidak punya akses');
    }

    $query = Penyewaan::with('itemPenyewaan')
        ->where('idpenyewaan', $id);

    if (!$isOwner) {
        $query->where('admin_pusat_idadmin_pusat', $adminPusat->idadmin_pusat);
    }

    $penyewaan = $query->firstOrFail();

    // ❌ tidak bisa cancel
    if ($penyewaan->status_penyewaan !== 'menunggu_pembayaran') {
        return redirect()->back()->with('error', 'Tidak bisa dibatalkan');
    }

    DB::beginTransaction();

    try {

        foreach ($penyewaan->itemPenyewaan as $item) {

            // ================= PRODUK =================
            if ($item->type === 'produk' && $item->produk_idproduk) {

                Produk::where('idproduk', $item->produk_idproduk)
                    ->increment('stok_pusat', $item->qty);
            }

            // ================= PAKET =================
            elseif ($item->type === 'paket') {

                if (!$item->detail_paket) continue;

                $details = json_decode($item->detail_paket, true);

                foreach ($details as $d) {

                    Produk::where('idproduk', $d['produk_id'])
                        ->increment('stok_pusat', $d['qty'] * $item->qty);
                }
            }
        }

        $penyewaan->update([
            'status_penyewaan' => 'dibatalkan'
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Penyewaan dibatalkan');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal membatalkan');
    }
}
public function detailPenyewaPusat($id)
{
    if (!auth()->check()) {
        abort(403);
    }

    $penyewaan = Penyewaan::with([
        'penyewa.user',
        'itemPenyewaan.produk',
        'itemPenyewaan.paket.detail.produk',
        'cabang'
    ])
    ->where('idpenyewaan', $id)
    ->whereHas('penyewa.user', function ($q) {
        $q->where('idusers', auth()->user()->idusers);
    })
    ->firstOrFail();

    return view('detail_sewa_pusat', compact('penyewaan'));
}

public function riwayatPusat(Request $request)
{
    $user = Auth::user();
    $penyewa = $user->penyewa ?? null;

    if (!$penyewa) {
        abort(403, 'Akun ini bukan penyewa');
    }
$perPage = $request->get('per_page', 10);
    // ⏳ BELUM BAYAR
    $belumBayar = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
    ->whereNotNull('admin_pusat_idadmin_pusat')
    ->whereIn('status_penyewaan', ['menunggu_pembayaran'])
    ->with(['cabang', 'cabang.adminCabang.user', 'penyewa.user'])
    ->orderBy('created_at', 'desc')
    ->paginate($perPage);

$penyewaanAktif = Penyewaan::where('penyewa_idpenyewa', $penyewa->idpenyewa)
    ->whereNotNull('admin_pusat_idadmin_pusat')
    ->whereIn('status_penyewaan', ['sedang_disewa'])
    ->with(['cabang', 'cabang.adminCabang.user', 'penyewa.user'])
    ->orderBy('created_at', 'desc')
    ->paginate($perPage);

    return view('item_penyewaan_pusat', compact('belumBayar', 'penyewaanAktif'));
}
public function selesaiPusat(Request $request) {
    $user = Auth::user();
    $penyewa = $user->penyewa ?? null;

    if (!$penyewa) {
        abort(403, 'Akun ini bukan penyewa');
    }
$perPage = $request->get('per_page', 10);
    $penyewaanSelesai = Penyewaan::with(['penyewa.user'])
        ->whereNotNull('admin_pusat_idadmin_pusat')
        ->where('penyewa_idpenyewa', $penyewa->idpenyewa) // ✅ FIX DI SINI
        ->where('status_penyewaan', 'selesai')
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    return view('riwayat_penyewaan_pusat', compact('penyewaanSelesai'));
}

public function uploadPusat($id)
{
    $penyewaan = Penyewaan::findOrFail($id);

    // Hanya bisa akses jika status menunggu pembayaran
    if ($penyewaan->status_penyewaan !== 'menunggu_pembayaran') {
        return back()->with('error', 'Penyewaan ini tidak bisa diupload bukti bayar');
    }
    $rekening = null; // atau ambil sesuai kebutuhan pusat
    
    if ($penyewaan->batas_pembayaran) {
        $sisaDetik = now()->diffInSeconds($penyewaan->batas_pembayaran, false);
        if ($sisaDetik < 0) {
            $sisaDetik = 0;
        }
    } else {
        $sisaDetik = 0;
    }
    return view('upload_pembayaran_pusat', compact(
        'penyewaan',
        'rekening',
        'sisaDetik'
    ));
}

public function uploadBuktiBayarPusat(Request $request, $idpenyewaan)
{
    $request->validate([
        'bukti_bayar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $penyewaan = Penyewaan::findOrFail($idpenyewaan);

    if ($request->hasFile('bukti_bayar')) {
    $file = $request->file('bukti_bayar');
    $filename = 'bukti_'.$idpenyewaan.'_'.time().'.'.$file->getClientOriginalExtension();
    $path = $file->storeAs(
        'bukti_bayar',
        $filename,
        'public'
    );
    $penyewaan->bukti_bayar = $path;
    $penyewaan->save();
}

    return redirect()->route('item_penyewaan_pusat')
                     ->with('success', 'Bukti bayar berhasil diupload, tunggu konfirmasi admin.');
}

public function struk($id)
{
    $data = Penyewaan::with(['cabang', 'penyewa.user', 'itemPenyewaan'])->findOrFail($id);

    return view('struk', compact('data'));
}
}
