<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = [
        'nama_paket',
        'harga_paket',
        'cabang_id',
        'is_template',
        'gambar_paket'
    ];

    // 🔗 relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'idcabang');
    }

    // 🔗 relasi ke detail paket
    public function detail()
    {
        return $this->hasMany(PaketDetail::class, 'paket_id', 'id');
    }

}