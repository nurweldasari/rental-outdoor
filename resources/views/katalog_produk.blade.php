@extends('layouts.app')

@php
    $active = 'katalog';
@endphp

@section('title', 'Katalog Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_produk.css') }}">
@endpush

@section('content')

<div class="container-produk katalog-produk">

    {{-- ================= HEADER ================= --}}
    <div class="header-produk">
        <form method="GET" id="searchForm">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text"
               id="searchInput"
               name="search"
               placeholder="Pencarian..."
               value="{{ request('search') }}">
    </div>
</form>

        <form method="GET" id="filterForm">
            <select name="kategori" onchange="this.form.submit()">
                <option value="">Filter Kategori</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori->idkategori }}" {{ request('kategori') == $kategori->idkategori ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ================= KATALOG + KERANJANG ================= --}}
    <div class="katalog-wrapper" id="katalogWrapper">

        {{-- ================= KATALOG ================= --}}
        <div class="katalog-area">
            <div class="grid-produk">
                @forelse($produkList as $pc)
                    @php
                        $produk = $pc->produk;
                        $stok = $pc->jumlah ?? 0;
                        $gambarPath = 'assets/uploads/produk/' . $produk->gambar_produk;
                        $gambar = $produk->gambar_produk && file_exists(public_path($gambarPath))
                            ? asset($gambarPath)
                            : asset('images/placeholder.png');
                    @endphp

                    <div class="card-produk">
                        <span class="badge-kategori">{{ $produk->kategori->nama_kategori ?? '-' }}</span>
                        <img src="{{ $gambar }}" class="img-produk">
                        <h4 class="nama-produk">{{ $produk->nama_produk }}</h4>
                        <p class="harga">Rp {{ number_format($produk->harga) }} / hari</p>

                        <div class="stok-wrapper">
                            <span class="stok">Stok: {{ $stok }}</span>
                            <button type="button" class="btn-keranjang" onclick="addToCart({{ $pc->idstok }})">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <p style="text-align:center;margin-top:40px">Tidak ada produk</p>
                @endforelse
            </div>

            @if(method_exists($produkList,'links'))
                <div class="pagination">
                    {{ $produkList->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- ================= KERANJANG ================= --}}
        <div class="keranjang-area">
            <div class="keranjang-header">Keranjang</div>
            <div class="keranjang-body" id="cartBody">
                <p id="cartEmpty">Keranjang kosong</p>
                <div class="ringkasan">
                    <p>Total Item Penyewaan <span id="totalItem">0</span></p>
                    <p>Total Pembayaran <strong id="totalHarga">Rp 0</strong></p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ================= FORM SUBMIT ================= --}}
<form id="formPenyewaan" method="POST" action="{{ route('penyewaan.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="tanggal_sewa" id="formTanggalSewa">
    <input type="hidden" name="tanggal_selesai" id="formTanggalSelesai">
    <input type="hidden" name="metode_bayar" id="formMetodeBayar">
    <div id="formItemsContainer"></div>
</form>

{{-- ================= MODAL BAYAR ================= --}}
<div class="modal-bayar" id="modalBayar">
    <div class="modal-bayar-box">
        <h4>Konfirmasi Pembayaran</h4>

        <label>Metode Pembayaran</label>
        <select id="metodeBayar">
            <option value="">-- Pilih --</option>
            <option value="cash">Cash</option>
            <option value="transfer">Transfer</option>
        </select>

        <p class="catatan-bayar" id="catatanBayar"></p>

        <div class="info-transfer" id="infoTransfer" style="display:none;">
            <div class="bank-box"><strong>Bank Negara Indonesia (BNI)</strong></div>
            <p class="rekening">No. rekening: <strong>489755489287</strong></p>
        </div>

        <button class="btn-konfirmasi">Konfirmasi</button>
    </div>
</div>

@endsection

<script>
/* ================= CART CACHE ================= */
let cartCache = {};

/* ================= OPEN KERANJANG ================= */
function openKeranjang(){
    document.getElementById('katalogWrapper')?.classList.add('active');
}

/* ================= HITUNG DURASI ================= */
function hitungDurasi(tglSewa, tglSelesai){
    if(!tglSewa || !tglSelesai) return 0;

    const start = new Date(tglSewa);
    const end   = new Date(tglSelesai);

    if(end < start) return 0;

    const diff = end.getTime() - start.getTime();
    return Math.ceil(diff / (1000 * 60 * 60 * 24)) + 1;
}

/* ================= CART AJAX ================= */
function addToCart(idstok){
    openKeranjang();

    fetch('/cart/add',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({idstok})
    })
    .then(r=>r.json())
    .then(cart=>{
        cartCache = cart;
        renderCart(cartCache);
    });
}

function updateCart(idstok,qty){
    if(qty < 1) return deleteCart(idstok);

    fetch('/cart/update',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({idstok,qty})
    })
    .then(r=>r.json())
    .then(cart=>{
        cartCache = cart;
        renderCart(cartCache);
    });
}

function deleteCart(idstok){
    fetch('/cart/delete',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({idstok})
    })
    .then(r=>r.json())
    .then(cart=>{
        cartCache = cart;
        renderCart(cartCache);
    });
}

/* ================= RENDER CART ================= */
function renderCart(cart){
    const body  = document.getElementById('cartBody');
    const items = document.getElementById('formItemsContainer');

    if(!cart || Object.keys(cart).length === 0){
        body.innerHTML = '<p id="cartEmpty">Keranjang kosong</p>';
        items.innerHTML = '';
        document.getElementBy  Id('totalItem').textContent = 0;
        document.getElementById('totalHarga').textContent = 'Rp 0';
        return;
    }

    const tglSewa    = document.getElementById('cartTanggalSewa')?.value || '';
    const tglSelesai = document.getElementById('cartTanggalSelesai')?.value || '';

    const durasi = hitungDurasi(tglSewa, tglSelesai);

    let html='', inputs='', totalItem=0, totalHarga=0;

    Object.values(cart).forEach(item=>{
        totalItem += item.qty;

        let subtotal = 0;
        let subtotalText = '<em>Pilih tanggal sewa</em>';

        if(durasi > 0){
            subtotal = item.qty * item.harga * durasi;
            totalHarga += subtotal;
            subtotalText = `${item.qty} × ${durasi} hari = <strong>Rp ${subtotal.toLocaleString()}</strong>`;
        }

        html += `
        <div class="item-keranjang">
            <div class="item-info">
                <strong>${item.nama}</strong>
                <p>Rp ${item.harga.toLocaleString()} / hari</p>
                <small>${subtotalText}</small>
            </div>
            <div class="item-aksi">
                <button onclick="updateCart(${item.idstok},${item.qty-1})">−</button>
                <span class="qty">${item.qty}</span>
                <button onclick="updateCart(${item.idstok},${item.qty+1})">+</button>
                <button onclick="deleteCart(${item.idstok})">🗑</button>
            </div>
        </div>`;

        inputs += `
        <input type="hidden" name="produk_cabang[]" value="${item.idstok}">
        <input type="hidden" name="qty[]" value="${item.qty}">`;
    });

    body.innerHTML = `
        ${html}
        <hr>
        <div class="ringkasan">
            <p>Total Item <span>${totalItem}</span></p>
            <p>Durasi <span>${durasi > 0 ? durasi+' hari' : '-'}</span></p>
            <p>Total <strong>Rp ${totalHarga.toLocaleString()}</strong></p>
        </div>

        <div class="tanggal">
            <div>
                <label>Tanggal Sewa</label>
                <input type="date" id="cartTanggalSewa" value="${tglSewa}">
            </div>
            <div>
                <label>Tanggal Berakhir</label>
                <input type="date" id="cartTanggalSelesai" value="${tglSelesai}">
            </div>
        </div>

        <button class="btn-pesan" ${durasi === 0 ? 'disabled' : ''}>Pesan Sekarang</button>
    `;

    items.innerHTML = inputs;
    document.getElementById('totalItem').textContent = totalItem;
    document.getElementById('totalHarga').textContent = 'Rp ' + totalHarga.toLocaleString();
}

/* ================= CHANGE TANGGAL ================= */
document.addEventListener('change',function(e){
    if(e.target.id === 'cartTanggalSewa' || e.target.id === 'cartTanggalSelesai'){
        renderCart(cartCache);
    }
});

/* ================= MODAL & SUBMIT ================= */
document.addEventListener('click',function(e){

    // buka modal
    if(e.target.closest('.btn-pesan')){
        document.getElementById('modalBayar').classList.add('active');
    }

    // tutup modal
    if(e.target === document.getElementById('modalBayar')){
        document.getElementById('modalBayar').classList.remove('active');
    }

    // konfirmasi
    if(e.target.closest('.btn-konfirmasi')){
        const metode = document.getElementById('metodeBayar').value;
        const tglSewa = document.getElementById('cartTanggalSewa').value;
        const tglSelesai = document.getElementById('cartTanggalSelesai').value;

        if(!metode || !tglSewa || !tglSelesai){
            alert('Lengkapi semua data');
            return;
        }

        document.getElementById('formTanggalSewa').value = tglSewa;
        document.getElementById('formTanggalSelesai').value = tglSelesai;
        document.getElementById('formMetodeBayar').value = metode;

        document.getElementById('formPenyewaan').submit();
    }
});

/* ================= INFO METODE ================= */
document.getElementById('metodeBayar')?.addEventListener('change',function(){
    const catatan = document.getElementById('catatanBayar');
    const info = document.getElementById('infoTransfer');

    if(this.value === 'cash'){
        catatan.textContent = 'Bayar ke toko (batas waktu 2 jam)';
        info.style.display = 'none';
    }else if(this.value === 'transfer'){
        catatan.textContent = 'Transfer ke rekening (batas waktu 2 jam)';
        info.style.display = 'block';
    }else{
        catatan.textContent = '';
        info.style.display = 'none';
    }
});
</script>
