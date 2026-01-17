<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminPusat extends Model
{
    use HasFactory;

    protected $table = 'admin_pusat';
    protected $primaryKey = 'idadmin_pusat';

    protected $fillable = [
        'users_idusers'
    ];

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class, 'users_idusers');
    }
}
