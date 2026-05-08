<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cabang;
use App\Models\SkalaBagiHasil;

class BagiHasil extends Model
{
    use HasFactory;

    protected $table = 'bagi_hasil';

    protected $primaryKey = 'idbagi_hasil';

    protected $fillable = [
        'cabang_idcabang',
        'skala_id',
        'bulan',
        'presentase_owner',
        'presentase_cabang',
        'nominal_owner',
        'nominal_cabang',
        'bukti_fee',
        'status'
    ];

    // 🔗 cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang', 'idcabang');
    }

    // 🔗 skala (INI PENTING)
    public function skala()
    {
        return $this->belongsTo(SkalaBagiHasil::class, 'skala_id', 'id');
    }
}