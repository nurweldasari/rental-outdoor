<?php

namespace App\Http\Controllers;

use App\Models\BagiHasil;
use Illuminate\Http\Request;
use App\Models\Cabang;

class BagiHasilController extends Controller
{
    // =========================
    // OWNER - TAMPIL LIST
    // =========================
    public function index()
    {
        $cabangs = Cabang::all();
        return view('bagi_hasil_owner', compact('cabangs'));
    }


    // =========================
    // OWNER - SIMPAN PERHITUNGAN
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'presentase_owner' => 'required',
            'presentase_cabang' => 'required',
        ]);

        BagiHasil::create([
            'presentase_owner' => $request->presentase_owner,
            'presentase_cabang' => $request->presentase_cabang,
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan');
    }

    // =========================
    // ADMIN CABANG - UPLOAD BUKTI
    // =========================
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_fee' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $bagiHasil = BagiHasil::findOrFail($id);

        $file = $request->file('bukti_fee');
        $namaFile = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/bukti_fee'), $namaFile);

        $bagiHasil->update([
            'bukti_fee' => $namaFile
        ]);

        return redirect()->back()->with('success', 'Bukti fee berhasil diupload');
    }

    // =========================
    // DETAIL
    // =========================
public function show($id)
{
    $cabangs = Cabang::all();
    $cabangTerpilih = Cabang::findOrFail($id);

    $totalPendapatan = $cabangTerpilih->transaksi()->sum('total'); 
    // sesuaikan relasi kamu

    $persenOwner  = 50;
    $persenCabang = 50;

    $hasilOwner  = $totalPendapatan * $persenOwner / 100;
    $hasilCabang = $totalPendapatan * $persenCabang / 100;

    return view('bagi_hasil', compact(
        'cabangs',
        'cabangTerpilih',
        'totalPendapatan',
        'persenOwner',
        'persenCabang',
        'hasilOwner',
        'hasilCabang'
    ));
}
}