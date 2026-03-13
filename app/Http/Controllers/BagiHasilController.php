<?php

namespace App\Http\Controllers;

use App\Models\BagiHasil;
use Illuminate\Http\Request;
use App\Models\AdminCabang;
use App\Models\Cabang;
use App\Models\Penyewaan;
use Carbon\Carbon;

class BagiHasilController extends Controller
{

// =========================
// OWNER - LIST
// =========================
public function index(Request $request)
{
    $view = $request->view ?? 'list';

    $cabangs = Cabang::all();

    $riwayat = BagiHasil::latest()->get();

    foreach ($riwayat as $item) {

        $tahun = date('Y', strtotime($item->bulan));
        $bulan = date('m', strtotime($item->bulan));

        $awal = Carbon::create($tahun,$bulan)->startOfMonth();
        $akhir = Carbon::create($tahun,$bulan)->endOfMonth();

        $item->total_pendapatan = Penyewaan::where('cabang_idcabang',$item->cabang_idcabang)
        ->whereBetween('tanggal_sewa',[$awal,$akhir])
        ->where('status_penyewaan','selesai')
        ->sum('total');
    }

    return view('bagi_hasil_owner',compact(
        'view',
        'cabangs',
        'riwayat'
    ));
}

// =========================
// OWNER - SIMPAN
// =========================
public function store(Request $request)
{

$request->validate([
'cabang_idcabang'=>'required',
'bulan'=>'required',
'nominal_owner'=>'required',
'nominal_cabang'=>'required'
]);

$sudahAda = BagiHasil::where('cabang_idcabang',$request->cabang_idcabang)
->where('bulan',$request->bulan)
->exists();

if($sudahAda){
return back()->with('error','Bagi hasil bulan ini sudah dibuat');
}

BagiHasil::create([

'cabang_idcabang'=>$request->cabang_idcabang,
'bulan'=>$request->bulan,
'presentase_owner'=>$request->presentase_owner,
'presentase_cabang'=>$request->presentase_cabang,
'nominal_owner'=>$request->nominal_owner,
'nominal_cabang'=>$request->nominal_cabang,
'status'=>'menunggu'

]);

return redirect()->route('bagi_hasil',['view'=>'riwayat'])
->with('success','Bagi hasil berhasil disimpan');

}


// =========================
// CABANG - DETAIL
// =========================
public function cabangIndex(Request $request)
{

$view = $request->view ?? 'detail';

$user = auth()->user();

$adminCabang = AdminCabang::where('users_idusers',$user->idusers)->first();

if(!$adminCabang){
abort(404,'Admin cabang tidak ditemukan');
}

$cabang = Cabang::find($adminCabang->cabang_idcabang);

// DATA YANG MASIH MENUNGGU (untuk halaman upload)
    $bagiHasilAktif = BagiHasil::where('cabang_idcabang',$cabang->idcabang)
        ->where('status','menunggu')
        ->latest()
        ->first();

    // RIWAYAT (yang sudah selesai)
    $riwayat = BagiHasil::where('cabang_idcabang',$cabang->idcabang)
        ->whereIn('status',['terkonfirmasi','ditolak'])
        ->latest()
        ->get();

return view('bagi_hasil_cabang',compact(

'view',
'cabang',
'bagiHasilAktif',
'riwayat'

));

}


// =========================
// CABANG - UPLOAD BUKTI
// =========================
public function uploadBukti(Request $request,$id)
{

$request->validate([

'bukti_fee'=>'required|image|mimes:jpg,png,jpeg|max:2048'

]);

$bagiHasil = BagiHasil::findOrFail($id);

$path = $request->file('bukti_fee')->store('bukti_fee','public');

$bagiHasil->update([

'bukti_fee'=>$path,
'status'=>'menunggu'

]);

return back()
->with('success','Bukti berhasil diupload')
->with('openModal',true);

}


// =========================
// OWNER - DETAIL
// =========================
public function show(Request $request,$id)
{

$view='detail';

$cabangs = Cabang::all();

$cabangTerpilih = Cabang::findOrFail($id);

$bulan = $request->bulan ?? now()->format('Y-m');

$tahun = date('Y',strtotime($bulan));
$bulanAngka = date('m',strtotime($bulan));

$awalBulan = Carbon::create($tahun,$bulanAngka)->startOfMonth();
$akhirBulan = Carbon::create($tahun,$bulanAngka)->endOfMonth();

$totalPendapatan = Penyewaan::where('cabang_idcabang',$id)
->whereBetween('tanggal_sewa',[$awalBulan,$akhirBulan])
->where('status_penyewaan','selesai')
->sum('total');

$persenOwner = $request->presentase_owner ?? 50;
$persenCabang = $request->presentase_cabang ?? 50;

$hasilOwner = $totalPendapatan * $persenOwner / 100;
$hasilCabang = $totalPendapatan * $persenCabang / 100;

// AMBIL DATA BUKTI FEE DARI DB
$buktiFee = BagiHasil::where('cabang_idcabang',$id)
    ->whereNotNull('bukti_fee')
    ->latest()
    ->get();

return view('bagi_hasil_owner',compact(

'view',
'cabangs',
'cabangTerpilih',
'totalPendapatan',
'hasilOwner',
'hasilCabang',
'persenOwner',
'persenCabang',
'bulan',
'buktiFee' 

));

}
public function konfirmasi($id)
{

$bagiHasil = BagiHasil::findOrFail($id);

$bagiHasil->update([
'status'=>'terkonfirmasi'
]);

return back()->with('success','Bagi hasil berhasil dikonfirmasi');

}
public function tolak($id)
{

$bagiHasil = BagiHasil::findOrFail($id);

$bagiHasil->update([
'status'=>'ditolak'
]);

return back()->with('success','Bukti fee ditolak');

}
}