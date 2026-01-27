<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistribusiProduk extends Model
{
    protected $table = 'distribusi_produk';
    protected $primaryKey = 'iddistribusi';
    protected $guarded = [];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanProduk::class, 'permintaan_id', 'idpermintaan');
    }

    // Update stok cabang otomatis saat status diterima
    public function confirm()
{
    if ($this->status_distribusi != 'diterima') {
        // Update status distribusi
        $this->status_distribusi = 'diterima';
        $this->save();

        // Update stok cabang
        $stok = StokCabang::firstOrCreate(
            [
                'produk_idproduk' => $this->permintaan->produk_idproduk,
                'cabang_idcabang' => $this->permintaan->cabang_idcabang
            ],
            ['jumlah' => 0]
        );

        $stok->jumlah += $this->jumlah_dikirim;
        $stok->save();
    }
}

}

