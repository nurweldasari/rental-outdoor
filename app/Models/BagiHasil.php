<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagiHasil extends Model
{
    use HasFactory;

    protected $table = 'bagi_hasil';

    protected $primaryKey = 'idbagi_hasil';

    protected $fillable = [
    'cabang_idcabang',
    'bulan',
    'presentase_owner',
    'presentase_cabang',
    'nominal_owner',
    'nominal_cabang',
    'bukti_fee',
    'status'
];
public function cabang()
{
    return $this->belongsTo(Cabang::class,'cabang_idcabang','idcabang');
}
}