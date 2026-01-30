<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokCabang extends Model
{
    protected $table = 'stok_cabang';

    protected $primaryKey = 'idstok';   // 🔥 INI YANG HILANG

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'produk_idproduk',
        'cabang_idcabang',
        'jumlah',
        'is_active'
    ];

    public function produk()
    {
        return $this->belongsTo(
            Produk::class,
            'produk_idproduk',
            'idproduk'
        );
    }
}