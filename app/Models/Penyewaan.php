<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyewaan extends Model
{
    protected $table = 'penyewaan';
    protected $primaryKey = 'idpenyewaan';

    protected $fillable = [
        'tanggal_sewa',
        'tanggal_selesai',
        'tanggal_kembali',
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


    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_idcabang');
    }

    public function itemPenyewaan()
{
    return $this->hasMany(ItemPenyewaan::class, 'penyewaan_idpenyewaan');
}

}
