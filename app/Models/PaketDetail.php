<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketDetail extends Model
{
    protected $table = 'paket_detail';

    protected $fillable = [
        'paket_id',
        'produk_idproduk',
        'stok_cabang_id',
        'qty'
    ];

    // 🔗 ke paket
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id', 'id');
    }

    // 🔗 ke produk pusat (optional)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_idproduk', 'idproduk');
    }

    // 🔗 ke stok cabang (INI YANG PALING SERING DIPAKAI)
    public function stokCabang()
    {
        return $this->belongsTo(StokCabang::class, 'stok_cabang_id', 'idstok');
    }
}