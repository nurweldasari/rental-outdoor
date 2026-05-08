<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkalaBagiHasil;
use Carbon\Carbon;

class SkalaBagiHasilController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'cabang_idcabang' => 'required',
        'owner' => 'required|numeric|min:0|max:100',
    ]);

    $owner = $request->owner;
    $cabang = 100 - $owner;

    SkalaBagiHasil::create([
        'cabang_idcabang' => $request->cabang_idcabang,
        'owner' => $owner,
        'cabang' => $cabang,
        'berlaku_mulai' => now()
    ]);

    return back()->with('success','Skala berhasil disimpan');
}
}
