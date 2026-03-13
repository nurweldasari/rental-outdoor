<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penyewaan;
use App\Models\StokCabang;
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

            // 🔄 Kembalikan stok
            foreach ($p->itemPenyewaan as $item) {

                StokCabang::where('produk_idproduk', $item->produk_idproduk)
                    ->where('cabang_idcabang', $p->cabang_idcabang)
                    ->increment('jumlah', $item->qty);
            }

            // ❌ Update status
            $p->update([
                'status_penyewaan' => 'dibatalkan'
            ]);
        });
    }

    $this->info('Expired penyewaan berhasil dibatalkan');
}
}
