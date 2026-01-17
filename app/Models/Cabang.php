<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';
    protected $primaryKey = 'idcabang';

    protected $fillable = [
        'nama_cabang',
        'status_cabang',
        'lokasi'
    ];

    /* ================= RELATIONS ================= */

    public function adminCabang()
    {
        return $this->hasMany(AdminCabang::class, 'cabang_idcabang');
    }
}
