<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminCabang extends Model
{
    use HasFactory;

    protected $table = 'admin_cabang';
    protected $primaryKey = 'idadmin_cabang';

    protected $fillable = [
        'users_idusers',
        'cabang_idcabang',
        'gambar_mou'
    ];

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class, 'users_idusers');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang');
    }
}
