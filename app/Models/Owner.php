<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends Model
{
    use HasFactory;

    protected $table = 'owner';
    protected $primaryKey = 'idowner';

    protected $fillable = [
        'users_idusers'
    ];

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class, 'users_idusers');
    }
}
