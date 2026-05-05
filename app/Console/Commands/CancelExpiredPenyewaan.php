<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penyewaan;
use App\Models\StokCabang;
use App\Models\Produk;
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

                    /* ==================================================
                     | PRODUK CABANG
                     ================================================== */
                    if ($item->type === 'produk' && $item->produk_idproduk && $p->cabang_idcabang) {

                        StokCabang::where('produk_idproduk', $item->produk_idproduk)
                            ->where('cabang_idcabang', $p->cabang_idcabang)
                            ->increment('jumlah', $item->qty);
                    }

                    /* ==================================================
                     | PRODUK PUSAT
                     ================================================== */
                    elseif ($item->type === 'produk' && $item->produk_idproduk && !$p->cabang_idcabang) {

                        Produk::where('idproduk', $item->produk_idproduk)
                            ->increment('stok_pusat', $item->qty);
                    }

                    /* ==================================================
                     | PAKET CABANG
                     ================================================== */
                    elseif ($item->type === 'paket' && $item->paket_id && $p->cabang_idcabang) {

                        $paket = Paket::with('detail')->find($item->paket_id);

                        if ($paket) {
                            foreach ($paket->detail as $detail) {

                                StokCabang::where('idstok', $detail->stok_cabang_id)
                                    ->where('cabang_idcabang', $p->cabang_idcabang)
                                    ->increment('jumlah', $detail->qty * $item->qty);
                            }
                        }
                    }

                    /* ==================================================
                     | PAKET PUSAT
                     ================================================== */
                    elseif ($item->type === 'paket' && $item->paket_id && !$p->cabang_idcabang) {

                        $paket = Paket::with('detail')->find($item->paket_id);

                        if ($paket) {
                            foreach ($paket->detail as $detail) {

                                Produk::where('idproduk', $detail->produk_idproduk)
                                    ->increment('stok_pusat', $detail->qty * $item->qty);
                            }
                        }
                    }
                }

                // ==================================================
                // UPDATE STATUS PENYEWAAN
                // ==================================================
                $p->update([
                    'status_penyewaan' => 'dibatalkan'
                ]);
            });
        }

        $this->info('Expired penyewaan berhasil dibatalkan');
    }
}