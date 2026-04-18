<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penyewaan;
use App\Models\StokCabang;
use App\Models\Paket;
use Illuminate\Support\Facades\DB;

class CancelExpiredPenyewaan extends Command
{
    protected $signature = 'penyewaan:cancel-expired';
    protected $description = 'Batalkan penyewaan yang belum dibayar lebih dari 2 jam';

    public function handle()
    {
        $expired = Penyewaan::with('itemPenyewaan')
            ->where('status_penyewaan', 'menunggu_pembayaran')
            ->where('batas_pembayaran', '<', now())
            ->get();

        foreach ($expired as $p) {

            DB::transaction(function () use ($p) {

                foreach ($p->itemPenyewaan as $item) {

                    /* ================= PRODUK ================= */
                    if ($item->type === 'produk' && $item->produk_idproduk) {

                        StokCabang::where('produk_idproduk', $item->produk_idproduk)
                            ->where('cabang_idcabang', $p->cabang_idcabang)
                            ->increment('jumlah', $item->qty);
                    }

                    /* ================= PAKET ================= */
                    elseif ($item->type === 'paket' && $item->paket_id) {

                        $paket = Paket::with('detail')->find($item->paket_id);

                        if ($paket) {
                            foreach ($paket->detail as $detail) {

                                $stok = StokCabang::find($detail->stok_cabang_id);

                                if ($stok) {
                                    $stok->increment(
                                        'jumlah',
                                        $detail->qty * $item->qty
                                    );
                                }
                            }
                        }
                    }
                }

                // ❌ Update status (DI LUAR LOOP ITEM)
                $p->update([
                    'status_penyewaan' => 'dibatalkan'
                ]);
            });
        }

        $this->info('Expired penyewaan berhasil dibatalkan');
    }
}