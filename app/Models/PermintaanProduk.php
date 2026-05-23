<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermintaanProduk extends Model
{
    use HasFactory;
    protected $table = 'permintaan_produk';
    protected $primaryKey = 'id'; // sesuai migration terbaru
    protected $guarded = []; // supaya mass assign bisa

    // Relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang', 'idcabang');
    }
    
public function permintaan()
{
    return $this->belongsTo(Permintaan::class, 'permintaan_id', 'idpermintaan');
}



    // Relasi ke produk
    public function produk()
{
    return $this->belongsTo(Produk::class, 'produk_idproduk', 'idproduk')
        ->withTrashed();
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
