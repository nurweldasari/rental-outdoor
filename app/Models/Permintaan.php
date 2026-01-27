<?php

namespace App\Models;

// app/Models/Permintaan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'idpermintaan';
    protected $guarded = [];

    // Relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang', 'idcabang');
    }

    // Semua detail produk
    public function produkDetail()
    {
        return $this->hasMany(PermintaanProduk::class, 'permintaan_id', 'idpermintaan')
                    ->with('produk'); // eager load produk
    }

    public function produkPermintaan()
{
    return $this->hasMany(PermintaanProduk::class, 'permintaan_id');
}

// Relasi admin cabang lewat cabang
    public function adminCabang()
    {
        return $this->hasOneThrough(
            AdminCabang::class,   // model target
            Cabang::class,        // model perantara
            'idcabang',           // FK di tabel Cabang yang menghubungkan ke AdminCabang
            'cabang_idcabang',    // FK di tabel AdminCabang
            'cabang_idcabang',    // FK di Permintaan
            'idcabang'            // PK di Cabang
        );
    }
}
