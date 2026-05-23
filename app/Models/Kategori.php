<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Nama tabel
    protected $table = 'kategori';

    // Primary key custom
    protected $primaryKey = 'idkategori';


    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'nama_kategori',
    ];
    // Relasi ke Produk
    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_idkategori', 'idkategori');
    }
}