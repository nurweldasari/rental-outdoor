<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $table = 'rekening';
    protected $primaryKey = 'idrekening';

    protected $fillable = [
        'nama_bank',
        'no_rekening',
        'atas_nama',
        'cabang_idcabang',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang', 'idcabang');
    }
}
