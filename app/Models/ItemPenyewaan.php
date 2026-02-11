<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPenyewaan extends Model
{
    protected $table = 'item_penyewaan';
    protected $primaryKey = 'iditem_penyewaan';

    protected $fillable = [
        'produk_idproduk',
        'harga',
        'penyewaan_idpenyewaan',
        'qty',
        'subtotal'
    ];

    // 🔗 RELASI
    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_idpenyewaan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_idproduk');
    }
}
