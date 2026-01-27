<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanProduk extends Model
{
    protected $table = 'permintaan_produk';
    protected $primaryKey = 'idpermintaan';
    protected $guarded = []; // penting supaya create() bisa mass assign

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang', 'idcabang');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_idproduk', 'idproduk');
    }

    /**
     * ✅ RELASI YANG HILANG
     * admin cabang diambil lewat cabang
     */
    public function adminCabang()
    {
        return $this->hasOne(
            AdminCabang::class,
            'cabang_idcabang',
            'cabang_idcabang'
        );
    }

    public function distribusi()
    {
        return $this->hasOne(
            DistribusiProduk::class,
            'permintaan_id',
            'idpermintaan'
        );
    }

}
