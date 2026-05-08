<?php

namespace App\Models;
use App\Models\Harga;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = [
        'nama_paket',
        'cabang_id',
        'is_template',
        'gambar_paket'
    ];

        public function harga()
    {
        return $this->hasMany(Harga::class, 'paket_id', 'id')
        ->where('type', 'paket');
    }
   public function hargaTerbaru()
{
    return $this->hasOne(Harga::class, 'paket_id', 'id')
        ->where('type', 'paket')
        ->latestOfMany('tanggal_berlaku');
}

    // relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'idcabang');
    }

    // relasi ke detail paket
    public function detail()
    {
        return $this->hasMany(PaketDetail::class, 'paket_id', 'id');
    }

}