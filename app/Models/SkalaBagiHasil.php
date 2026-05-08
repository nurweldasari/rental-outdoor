<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cabang;

class SkalaBagiHasil extends Model
{
    use HasFactory;

    protected $table = 'skala_bagi_hasil';

    protected $fillable = [
        'cabang_idcabang',
        'owner',
        'cabang',
        'berlaku_mulai'
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang', 'idcabang');
    }
    public function scopeAktif($q, $cabangId, $tanggal)
    {
    return $q->where('cabang_idcabang',$cabangId)
             ->whereDate('berlaku_mulai','<=',$tanggal)
             ->orderByDesc('berlaku_mulai');
    }
}