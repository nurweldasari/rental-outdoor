<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistribusiProduk extends Model
{
    protected $table = 'distribusi_produk';
    protected $primaryKey = 'iddistribusi';
    protected $guarded = [];

 public function permintaanProduk()
{
    return $this->belongsTo(PermintaanProduk::class, 'permintaan_produk_id', 'id');
}

    // Update stok cabang otomatis saat status diterima
    public function confirm()
    {
        if ($this->status_distribusi != 'diterima' && $this->permintaan) {

            $this->status_distribusi = 'diterima';
            $this->save();

            $cabangId = $this->permintaan->cabang_idcabang ?? null;
            $produkId = $this->permintaan->produk_idproduk ?? null;

            if ($cabangId && $produkId) {
                $stok = StokCabang::firstOrCreate(
                    [
                        'produk_idproduk' => $produkId,
                        'cabang_idcabang' => $cabangId
                    ],
                    ['jumlah' => 0]
                );

                $stok->jumlah += $this->jumlah_dikirim;
                $stok->save();
            }
        }
    }
}
