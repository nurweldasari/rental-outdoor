<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_bank'       => 'required|max:45',
            'no_rekening'     => 'required|max:45',
            'atas_nama'       => 'required|max:45',
            'cabang_idcabang' => 'required',
        ]);

        Rekening::create([
            'nama_bank'       => $request->nama_bank,
            'no_rekening'     => $request->no_rekening,
            'atas_nama'       => $request->atas_nama,
            'cabang_idcabang' => $request->cabang_idcabang,
        ]);

        return back()->with('success', 'Rekening berhasil ditambahkan');
    }
}
