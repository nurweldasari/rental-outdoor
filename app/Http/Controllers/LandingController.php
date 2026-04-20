<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Cabang;
use Illuminate\Http\Request;

class LandingController extends BaseController
{
    public function landing(Request $request)
{
    $perPage = 6;

    $cabang = Cabang::where('status_cabang', 'aktif')
        ->paginate($perPage)
        ->withQueryString();

    return view('landing_penyewa', compact('cabang'));
}
}
