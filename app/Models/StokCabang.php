<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokCabang extends Model
{
    protected $table = 'stok_cabang';
    protected $primaryKey = 'idstok';
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(
            Produk::class,
            'produk_idproduk',
            'idproduk'
        );
    }

    public function cabang()
    {
        return $this->belongsTo(
            Cabang::class,
            'cabang_idcabang',
            'idcabang'
        );
    }
}
