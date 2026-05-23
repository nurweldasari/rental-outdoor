<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\BagiHasil;
use App\Models\SkalaBagiHasil;
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

    // HANYA OWNER
    if(auth()->user()->status != 'owner'){
        abort(403,'Akses ditolak');
    }
    $view = $request->view ?? 'list';

    $cabangs = Cabang::paginate(12)->withQueryString();

    $riwayat = BagiHasil::latest()->paginate(10)->withQueryString();

    $riwayat->getCollection()->transform(function ($item) {

        $tahun = date('Y', strtotime($item->bulan));
        $bulan = date('m', strtotime($item->bulan));

        $awal = Carbon::create($tahun,$bulan)->startOfMonth();
        $akhir = Carbon::create($tahun,$bulan)->endOfMonth();

        $item->total_pendapatan = Penyewaan::where('cabang_idcabang',$item->cabang_idcabang)
            ->whereBetween('tanggal_sewa',[$awal,$akhir])
            ->where('status_penyewaan','selesai')
            ->sum('total');

        return $item;
    });

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
        'cabang_idcabang' => 'required',
        'bulan' => 'required',
        'presentase_owner' => 'required|numeric',
        'presentase_cabang' => 'required|numeric',
        'nominal_owner' => 'required|numeric',
        'nominal_cabang' => 'required|numeric',
    ]);

    $sudahClosing = BagiHasil::where('cabang_idcabang', $request->cabang_idcabang)
        ->where('bulan', $request->bulan)
        ->exists();

    if ($sudahClosing) {
        return back()->with('error', 'Bulan ini sudah closing');
    }

    $skala = SkalaBagiHasil::aktif(
        $request->cabang_idcabang,
        $request->bulan.'-01'
    )->first();

    BagiHasil::create([
        'cabang_idcabang'   => $request->cabang_idcabang,
        'skala_id'          => $skala?->id,
        'bulan'             => $request->bulan,

        'presentase_owner'  => $request->presentase_owner ?? 0,
        'presentase_cabang' => $request->presentase_cabang ?? 0,

        'nominal_owner'     => (int) $request->nominal_owner,
        'nominal_cabang'    => (int) $request->nominal_cabang,

        'status'            => 'terkunci',
    ]);

    return redirect()->route('bagi_hasil', ['view' => 'riwayat',
    'cabang' => $request->cabang_idcabang])
    ->with('success','Bagi hasil berhasil di-close');
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
        ->whereIn('status',['terkunci','menunggu']) // atau "menunggu_bukti"
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
    // HANYA OWNER
    if(auth()->user()->status != 'owner'){
        abort(403,'Akses ditolak');
    }
    
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

$skala = SkalaBagiHasil::where('cabang_idcabang',$id)
    ->orderByDesc('id')
    ->first();

$persenOwner = $skala?->owner ?? 50;
$persenCabang = $skala?->cabang ?? 50;

$hasilOwner = round($totalPendapatan * $persenOwner / 100);
$hasilCabang = round($totalPendapatan * $persenCabang / 100);

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