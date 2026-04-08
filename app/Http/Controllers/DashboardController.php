<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\User;
use App\Models\Kategori;
use App\Models\StokCabang;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   
public function index(Request $request)
{
    $user = Auth::user();

    /* ================= PENYEWA ================= */
    if ($user->status === 'penyewa') {

        $perPage = $request->get('per_page', 6); // jumlah card per halaman

        $cabang = Cabang::where('status_cabang', 'aktif')
            ->paginate($perPage)
            ->withQueryString();

        $adminpusat = User::where('status', 'admin_pusat')
            ->select('idusers', 'nama', 'no_telepon', 'alamat')
            ->first();

        return view('dashboard_penyewa', compact('cabang', 'adminpusat'));
    }


        /* ================= ADMIN CABANG ================= */
        if ($user->status === 'admin_cabang') {

            return view('/dashboard_cabang');
        }

        /* ================= ADMIN PUSAT ================= */
        if ($user->status === 'admin_pusat') {

            return view('/dashboard_pusat');
        }

        /* ================= OWNER ================= */
        if ($user->status === 'owner') {

            return view('/dashboard_owner');
        }

        abort(403);
    }
 
}