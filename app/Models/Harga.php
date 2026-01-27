<?php

namespace App\Models;
use App\Models\Produk;

use Illuminate\Database\Eloquent\Model;


class Harga extends Model
{
    protected $table = 'harga';
    protected $primaryKey = 'idharga';

    public $incrementing = true;
    protected $keyType = 'int';

    // Karena tabel TIDAK pakai timestamps
    public $timestamps = false;

    protected $fillable = [
        'harga_sewa',
        'tanggal_berlaku',
    ];

    // Relasi ke produk (satu harga bisa dipakai banyak produk)
    public function produk()
    {
        return $this->hasMany(Produk::class, 'harga_idharga', 'idharga');
    }
}
