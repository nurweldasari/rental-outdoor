<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penyewaan extends Model
{
    use HasFactory;

    protected $table = 'penyewaan';
    protected $primaryKey = 'idpenyewaan';

    protected $fillable = [
        'tanggal_sewa',
        'tanggal_selesai',
        'tanggal_kembali',
        'sudah_diingatkan',
        'total',
        'total_produk',
        'bukti_bayar',
        'status_penyewaan',
        'metode_bayar',          // ⬅️ tambahkan ini
        'batas_pembayaran',      // ⬅️ tambahkan ini
        'penyewa_idpenyewa',
        'cabang_idcabang',
        'admin_pusat_idadmin_pusat',
    ];

    protected $dates = [
        'tanggal_sewa',
        'tanggal_selesai',
        'tanggal_kembali',
        'batas_pembayaran',      // ⬅️ tambahkan
        'created_at',
        'updated_at',
    ];

    /* ================= RELATION ================= */
    public function penyewa()
{
    return $this->belongsTo(
        Penyewa::class,
        'penyewa_idpenyewa', // foreign key di tabel penyewaan
        'idpenyewa'          // primary key di tabel penyewa
    );
}

public function adminPusat()
{
    return $this->belongsTo(AdminPusat::class, 'admin_pusat_idadmin_pusat');
}
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang');
    }
    

    public function itemPenyewaan()
{
    return $this->hasMany(ItemPenyewaan::class, 'penyewaan_idpenyewaan');
}

}
