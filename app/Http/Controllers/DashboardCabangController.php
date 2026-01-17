<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardCabangController extends Controller
{
    public function dashboard_cabang()
    {
        // Ambil data yang dibutuhkan untuk dashboard cabang
        // Misal data admin cabang atau cabang sendiri
        return view('dashboard_cabang'); // pastikan file Blade ada di resources/views/dashboard/cabang.blade.php
    }
}
