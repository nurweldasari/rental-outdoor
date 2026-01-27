<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /* ================= PENYEWA ================= */
        if ($user->status === 'penyewa') {

            $cabang = Cabang::all();

            return view('/dashboard_penyewa', compact('cabang'));
        }

        /* ================= ADMIN CABANG ================= */
        if ($user->status === 'admin_cabang') {

            return view('/dashboard_cabang');
        }

        /* ================= ADMIN PUSAT ================= */
        if ($user->status === 'admin_pusat') {

            return view('/dashboard_cabang');
        }

        /* ================= OWNER ================= */
        if ($user->status === 'owner') {

            return view('/dashboard_cabang');
        }

        abort(403);
    }


    
}
