<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'kategori';

    // Primary key custom
    protected $primaryKey = 'idkategori';

    // Karena tidak pakai created_at & updated_at
    public $timestamps = false;

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'nama_kategori',
    ];
}
