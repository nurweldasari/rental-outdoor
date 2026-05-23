<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminCabang;

class KontrakFranchiseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $adminCabang = AdminCabang::where(
            'users_idusers',
            $user->idusers
        )->first();

        if (!$user || !$adminCabang) {
            abort(403, 'Akun ini bukan admin cabang');
        }

        return view('kontrak_franchise', compact('adminCabang'));
    }
}