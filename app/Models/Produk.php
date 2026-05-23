<?php

namespace App\Models;
use App\Models\Harga;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
     use HasFactory;
     use SoftDeletes;
    protected $table = 'produk';
    protected $primaryKey = 'idproduk';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_produk',
        'stok_pusat',
        'jenis_skala',
        'gambar_produk',
        'kategori_idkategori',          // ✅
        'admin_pusat_idadmin_pusat',
    ];
    

    // ================= RELATION =================
    public function harga()
{
    return $this->hasMany(Harga::class, 'produk_id', 'idproduk');
    
}
public function hargaAktif()
{
    return $this->hasOne(Harga::class, 'produk_id', 'idproduk')
        ->where('type', 'produk')
        ->latestOfMany('tanggal_berlaku');
}
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_idkategori', 'idkategori');
    }

    public function stokCabang()
{
    return $this->hasMany(
        StokCabang::class,
        'produk_idproduk', // FK di stok_cabang
        'idproduk'         // PK di produk
    );
}

}
