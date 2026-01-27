<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanProduk extends Model
{
    protected $table = 'permintaan_produk';
    protected $primaryKey = 'id'; // sesuai migration terbaru
    protected $guarded = []; // supaya mass assign bisa

    // Relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang', 'idcabang');
    }

    // Relasi ke produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_idproduk', 'idproduk');
    }

    // Relasi admin cabang lewat cabang
    public function adminCabang()
    {
        return $this->hasOne(
            AdminCabang::class,
            'cabang_idcabang',
            'cabang_idcabang'
        );
    }

    // Relasi distribusi produk
    public function distribusi()
{
    return $this->hasMany(
        DistribusiProduk::class,
        'permintaan_produk_id', // FK yang benar
        'id' // PK tabel permintaan_produk
    );
}

}
