<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AdminCabang;

class KontrakFranchiseController extends Controller
{
public function index()
{
    $adminCabang = AdminCabang::where(
        'users_idusers',
        Auth::user()->idusers
    )->first();

    return view('kontrak_franchise', compact('adminCabang'));
}
}
