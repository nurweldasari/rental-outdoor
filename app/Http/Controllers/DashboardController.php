<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;

class DashboardController extends Controller
{
    public function dashboard_penyewa() {
    $cabang = Cabang::all();
    return view('dashboard_penyewa', compact('cabang'));
}
}
