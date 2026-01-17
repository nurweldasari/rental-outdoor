<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'idusers';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'no_telepon',
        'alamat',
        'status'
    ];

    protected $hidden = [
        'password'
    ];

    /**
     * Login pakai username, bukan email
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /* ================= RELATIONS ================= */

    public function penyewa()
    {
        return $this->hasOne(Penyewa::class, 'users_idusers');
    }

    public function adminCabang()
    {
        return $this->hasOne(AdminCabang::class, 'users_idusers');
    }

    public function adminPusat()
    {
        return $this->hasOne(AdminPusat::class, 'users_idusers');
    }

    public function owner()
    {
        return $this->hasOne(Owner::class, 'users_idusers');
    }
}
