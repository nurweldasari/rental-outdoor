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
        $expired = Penyewaan::where('status_penyewaan', 'menunggu_pembayaran')
            ->where('created_at', '<', now()->subHours(2))
            ->get();

        foreach ($expired as $p) {

            DB::transaction(function () use ($p) {

                // 👉 KEMBALIKAN STOK
                foreach ($p->detailPenyewaan as $detail) {
                    StokCabang::where('idstok', $detail->stok_id)
                        ->increment('jumlah', $detail->qty);
                }

                // 👉 UPDATE STATUS
                $p->update([
                    'status_penyewaan' => 'dibatalkan'
                ]);
            });
        }

        $this->info('Expired penyewaan berhasil dibatalkan');
    }
}
