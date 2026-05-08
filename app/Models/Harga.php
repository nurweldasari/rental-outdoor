<?php

namespace App\Models;
use App\Models\Produk;
use App\Models\Paket;

use Illuminate\Database\Eloquent\Model;


class Harga extends Model
{
    protected $table = 'harga';
    protected $primaryKey = 'idharga';

    public $timestamps = true;

    protected $fillable = [
        'type',
        'produk_id',
        'paket_id',
        'harga',
        'tanggal_berlaku',
    ];
    public function scopeProduk($query)
{
    return $query->where('type', 'produk');
}

public function scopePaket($query)
{
    return $query->where('type', 'paket');
}

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'idproduk');
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id', 'id');
    }
}
