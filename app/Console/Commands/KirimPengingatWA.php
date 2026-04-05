<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Penyewaan;

class KirimPengingatWA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kirim:pengingat-wa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat WhatsApp untuk penyewaan yang akan berakhir';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("=== Command Kirim WA dijalankan ===");

        // Ambil semua penyewaan yang sedang disewa dan belum diingatkan
        $data = Penyewaan::where('status_penyewaan', 'sedang_disewa')
            ->where('sudah_diingatkan', 0)
            ->get();

        $this->info("Jumlah penyewaan yang perlu diingatkan: " . $data->count());

        if ($data->isEmpty()) {
            $this->info("Tidak ada penyewaan yang perlu diingatkan.");
            return;
        }

        foreach ($data as $item) {

    if (!$item->penyewa || !$item->penyewa->user || !$item->penyewa->user->no_telepon) {
        $this->info("Penyewa atau nomor telepon kosong, ID: {$item->idpenyewaan}, dilewati.");
        continue;
    }

    $now = Carbon::now();
    $waktu_selesai = Carbon::parse($item->tanggal_selesai)->setHour(18)->setMinute(0)->setSecond(0); // deadline jam 18:00
    $waktu_pengingat = Carbon::parse($item->tanggal_selesai)->subHours(3); // 3 jam sebelum selesai

    if ($now->lt($waktu_pengingat)) {
        $this->info("Belum waktunya mengingatkan, ID Penyewaan: {$item->idpenyewaan}");
        continue;
    }

    if ($now->gt($waktu_selesai)) {
        $this->info("Sudah lewat jam 18:00, ID Penyewaan: {$item->idpenyewaan}");
        continue;
    }

    $nomor = preg_replace('/[^0-9]/', '', $item->penyewa->user->no_telepon);
    if (substr($nomor, 0, 1) == '0') $nomor = '62' . substr($nomor, 1);

    $pesan = "Halo {$item->penyewa->user->nama},\n\n"
        ."Pengingat ya, penyewaan Anda akan berakhir hari ini.\n"
        ."Silakan melakukan pengembalian mulai pukul 16:00\n"
        ."dan maksimal sebelum jam 18:00.\n\n"
        ."Terima kasih 🙏";

    try {
        $response = Http::withHeaders([
            'Authorization' => env('FONTE_TOKEN')
        ])->asForm()->post(env('FONTE_URL'), [
            'target' => $nomor,
            'message' => $pesan,
        ]);

        $responseData = $response->json();

        if (!empty($responseData['status']) && $responseData['status'] === true) {
            $item->update(['sudah_diingatkan' => 1]);
            $this->info("✅ WA berhasil dikirim ke: $nomor (ID Penyewaan: {$item->idpenyewaan})");
        } else {
            $this->error("❌ Gagal kirim WA ke: $nomor (ID Penyewaan: {$item->idpenyewaan})");
            $this->info("Response: " . json_encode($responseData));
        }
    } catch (\Exception $e) {
        $this->error("❌ Exception kirim WA: " . $e->getMessage());
        \Log::error('Exception kirim WA', [
            'id_penyewaan' => $item->idpenyewaan,
            'nomor' => $nomor,
            'exception' => $e->getMessage()
        ]);
    }
}
        $this->info("=== Proses kirim pengingat selesai ===");
    }
}