<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminCabang;
use App\Models\Cabang;
use App\Models\AdminPusat;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // CABANG
    $cabang = Cabang::create([
        'nama_cabang' => 'Cabang Banyuwangi',
        'status_cabang' => 'aktif',
        'lokasi' => 'Banyuwangi'
    ]);

    // ADMIN PUSAT
    $adminPusatUser = User::create([
        'nama' => 'Admin Pusat',
        'username' => 'adminpusat',
        'password' => Hash::make('admin123'),
        'no_telepon'  => '081331623134',
        'alamat'      => 'Tegalsari',
        'status' => 'admin_pusat'
    ]);
    AdminPusat::create(['users_idusers' => $adminPusatUser->idusers]);

    // OWNER
    $ownerUser = User::create([
        'nama' => 'Owner',
        'username' => 'owner',
        'password' => Hash::make('owner123'),
        'no_telepon'  => '081331623134',
        'alamat'      => 'Tegalsari',
        'status' => 'owner'
    ]);
    Owner::create(['users_idusers' => $ownerUser->idusers]);

    // ADMIN CABANG
    $adminCabangUser = User::create([
        'nama' => 'Admin Cabang',
        'username' => 'admincabang',
        'password' => Hash::make('admin123'),
        'no_telepon'  => '081331623131',
        'alamat'      => 'Rogojampi',
        'status' => 'admin_cabang'
    ]);
    AdminCabang::create([
        'users_idusers' => $adminCabangUser->idusers,
        'cabang_idcabang' => $cabang->idcabang,
        'gambar_mou' => 'default_admin.png'
    ]);
}
}
