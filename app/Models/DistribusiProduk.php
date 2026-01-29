<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistribusiProduk extends Model
{
    protected $table = 'distribusi_produk';
    protected $primaryKey = 'iddistribusi';
    protected $guarded = [];

    /**
     * DETAIL PERMINTAAN
     */
    public function permintaanProduk()
    {
        return $this->belongsTo(
            PermintaanProduk::class,
            'permintaan_produk_id',
            'id'
        );
    }

    /**
     * HEADER PERMINTAAN
     */
    public function permintaan()
    {
        return $this->hasOneThrough(
            Permintaan::class,
            PermintaanProduk::class,
            'id',                // FK di permintaan_produk
            'idpermintaan',      // PK di permintaan
            'permintaan_produk_id',
            'permintaan_id'
        );
    }

    /**
     * KONFIRMASI TERIMA BARANG
     */
    public function confirm()
    {
        if ($this->status_distribusi === 'diterima') {
            return;
        }

        $permintaanProduk = $this->permintaanProduk;
        $permintaan       = $permintaanProduk?->permintaan;

        if (!$permintaanProduk || !$permintaan) {
            return;
        }

        $cabangId = $permintaan->cabang_idcabang;
        $produkId = $permintaanProduk->produk_idproduk;

        // update status distribusi
        $this->status_distribusi = 'diterima';
        $this->save();

        // update stok cabang
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
