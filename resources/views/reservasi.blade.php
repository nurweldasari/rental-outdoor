@extends('layouts.app')

@php
    $active = 'penyewa';
@endphp

@section('title', 'Katalog Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/data_produk.css') }}">
@endpush

@section('content')


<div class="container-produk katalog-produk">

    {{-- ================= HEADER ================= --}}
    <div class="header-produk">
        <div class="card-penyewa">
    <div class="card-body">
    <div class="avatar">
        <i class="fa-solid fa-user"></i>
    </div>

    <div class="info">
        <h4>Buat Reservasi:</h4>
        <h4>{{ $penyewa->user->nama }}</h4>
        <p><i class="fa-solid fa-phone"></i> {{ $penyewa->user->no_telepon }}</p>
    </div>
</div>
</div>
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
        {{-- ================= KATALOG ================= --}}
        <div class="katalog-area">
            <div class="grid-produk">
                {{-- ================= PAKET ================= --}}
@foreach($paketList as $paket)

     @php
        $gambar = $paket->gambar_paket
        ? asset('storage/'.$paket->gambar_paket)
        : asset('images/placeholder.png');
    @endphp

    <div class="card-produk paket">

        <span class="badge-kategori">Paket</span>

        <img src="{{ $gambar }}" class="img-produk">

        <h4 class="nama-produk">{{ $paket->nama_paket }}</h4>

        <p class="harga">
            Rp {{ number_format($paket->harga_paket, 0, ',', '.') }} / hari
        </p>
<div class="aksi-wrapper">
        <button class="btn-detail"
            onclick="openModal(this)"
            data-nama="{{ $paket->nama_paket }}"
            data-harga="{{ $paket->harga_paket }}"
             data-gambar="{{ $paket->gambar_paket
            ? asset('storage/'.$paket->gambar_paket)
            : asset('images/placeholder.png') }}"
            data-detail="
                @foreach($paket->detail as $item)
                    {{ optional($item->stokCabang->produk)->nama_produk }} ({{ $item->qty }})|
                @endforeach
            ">
            Detail
        </button>

        <button class="btn-keranjang"
            onclick="addPaketToCart({{ $paket->id }})">
            <i class="fa-solid fa-cart-plus"></i>
        </button>
</div>
    </div>

@endforeach
                @forelse($produkList as $pc)
                    @php
                        $produk = $pc->produk;
                        $stok = $pc->jumlah ?? 0;

                        $gambar = $produk->gambar_produk
                            ? asset('storage/'.$produk->gambar_produk)
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
<div class="pagination-simple">

    {{-- Prev --}}
    @if ($produkList->onFirstPage())
        <span class="nav disabled">«</span>
    @else
        <a href="{{ $produkList->previousPageUrl() }}" class="nav">«</a>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($produkList->getUrlRange(1, $produkList->lastPage()) as $page => $url)
        @if ($page == $produkList->currentPage())
            <span class="page active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if ($produkList->hasMorePages())
        <a href="{{ $produkList->nextPageUrl() }}" class="nav">»</a>
    @else
        <span class="nav disabled">»</span>
    @endif

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
{{-- ================= FORM SUBMIT ================= --}}
<form id="formPenyewaan"
      method="POST"
      action="{{ route('reservasi.store', $penyewa->users_idusers) }}" 
      style="display:none;">
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
            <div class="bank-box"><strong>{{ $rekening->nama_bank }}</strong></div>
            <p class="rekening">No. Rekening : {{ $rekening->no_rekening }} ({{ $rekening->atas_nama }})</p>
        </div>

        <button class="btn-konfirmasi">Simpan Reservasi</button>
    </div>
</div>

<div id="modalDetail" class="modal-paket">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <img id="modalGambar" style="width:100%; border-radius:10px; margin-bottom:10px;">

        <h3 id="modalNama"></h3>
        <p id="modalHarga" style="font-weight:bold;"></p>

        <div id="modalIsi"></div>
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

    const mulai = new Date(tglSewa);
    const selesai = new Date(tglSelesai);

    // Reset jam supaya tidak kena efek timezone
    mulai.setHours(0,0,0,0);
    selesai.setHours(0,0,0,0);

    const selisih = (selesai - mulai) / (1000 * 60 * 60 * 24);

    return Math.max(1, selisih);
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
    .then(res=>{
        if(res.error){
            alert(res.error);
        }

        cartCache = res.cart;
        renderCart(cartCache);
    });
}

function updateCart(idstok, qty){
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
    .then(res=>{
        if(res.error){
            alert(res.error);
        }

        cartCache = res.cart;
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
    .then(res=>{
        cartCache = res.cart;
        renderCart(cartCache);
    });
}
function addPaketToCart(paketId){
    openKeranjang();

    fetch('/cart/add-paket',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ paket_id: paketId })
    })
    .then(r => r.json())
    .then(res => {
  console.log('UPDATE RES:', res);
    // 🔥 HANDLE ERROR + AUTO FIX
    if(res.error){
        alert(res.error);

        // 🔥 kalau backend kasih max → paksa ke max
        if(res.max !== undefined){
            updatePaket(paketId, res.max);
        }

        return;
    }

    if(!res.cart){
        console.log('RESPONSE ANEH:', res);
        return;
    }

    cartCache = res.cart;
    renderCart(cartCache);
});
}
function updatePaket(paketId, qty){
    if(qty < 1) return deletePaket(paketId);

    fetch('/cart/update-paket',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ paket_id: paketId, qty })
    })
    .then(r => r.json())
    .then(res => {

        // 🔥 STOP kalau error
        if(res.error){
            alert(res.error);
            return; // ❗ INI PENTING BANGET
        }

        // 🔥 safety tambahan
        if(!res.cart){
            console.log('RESPONSE ANEH:', res);
            return;
        }

        cartCache = res.cart;
        renderCart(cartCache);
    });
}
function deletePaket(paketId){
    fetch('/cart/delete-paket',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ paket_id: paketId })
    })
    .then(r=>r.json())
    .then(res=>{
        cartCache = res.cart;
        renderCart(cartCache);
    });
}
function renderCart(cart){

    const body  = document.getElementById('cartBody');
    const items = document.getElementById('formItemsContainer');

    if(!cart || Object.keys(cart).length === 0){
        body.innerHTML = '<p id="cartEmpty">Keranjang kosong</p>';
        items.innerHTML = '';
        document.getElementById('totalItem').textContent = 0;
        document.getElementById('totalHarga').textContent = 'Rp 0';
        return;
    }

    const tglSewa    = document.getElementById('cartTanggalSewa')?.value || '';
    const tglSelesai = document.getElementById('cartTanggalSelesai')?.value || '';
    const durasi = hitungDurasi(tglSewa, tglSelesai);

    let html = '';
    let inputs = '';

    let totalItem = 0;
    let totalHarga = 0;

    Object.values(cart).forEach(item => {

        const qty = Number(item.qty) || 1;
        const harga = Number(item.harga) || 0;
        const max = Number(item.max) || null;

        // ================= PAKET =================
        if(item.type === 'paket'){

            totalItem += qty;

            let subtotal = 0;
            let subtotalText = '<em>Pilih tanggal</em>';

            if(durasi > 0){
                subtotal = harga * qty * durasi;
                totalHarga += subtotal;

                subtotalText = `${qty} paket × ${durasi} hari = <b>Rp ${subtotal.toLocaleString()}</b>`;
            }

            const disablePlus = max !== null && qty >= max;

            html += `
                <div class="item-keranjang">
                    <div class="item-info">

                        <span class="badge-paket">Paket</span>

                        <strong>${item.nama}</strong>

                        <p>Rp ${harga.toLocaleString()} / paket</p>

                        <small>${subtotalText}</small>

                        <div class="paket-detail">
                            ${Object.values(item.items || {}).map(i =>
                                `<div class="detail-line">• ${i.nama} (${i.qty})</div>`
                            ).join('')}
                        </div>

                    </div>

                    <div class="item-aksi">
                        <button onclick="updatePaket(${item.paket_id}, ${qty - 1})"><i class="fa-solid fa-minus"></i></button>
                        <span class="qty">${qty}</span>
                        <button onclick="updatePaket(${item.paket_id}, ${qty + 1})" ${disablePlus ? 'disabled' : ''}><i class="fa-solid fa-plus"></i></button>
                        <button class="btn-hapus" onclick="deletePaket(${item.paket_id})"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            `;

            inputs += `
                <input type="hidden" name="produk_cabang[]" value="${item.paket_id}">
                <input type="hidden" name="qty[]" value="${qty}">
                <input type="hidden" name="type[]" value="paket">
            `;
        }

        // ================= PRODUK =================
        else {

            totalItem += qty;

            let subtotal = 0;
            let subtotalText = '<em>Pilih tanggal</em>';

            if(durasi > 0){
                subtotal = qty * harga * durasi;
                totalHarga += subtotal;

                subtotalText = `${qty} × ${durasi} hari = <b>Rp ${subtotal.toLocaleString()}</b>`;
            }

            html += `
                <div class="item-keranjang">
                    <div class="item-info">

                        <strong>${item.nama}</strong>

                        <p>Rp ${harga.toLocaleString()} / hari</p>

                        <small>${subtotalText}</small>

                    </div>

                    <div class="item-aksi">
                        <button onclick="updateCart(${item.idstok},${qty-1})"><i class="fa-solid fa-minus"></i></button>
                        <span class="qty">${qty}</span>
                        <button onclick="updateCart(${item.idstok},${qty+1})"><i class="fa-solid fa-plus"></i></button>
                        <button class="btn-hapus" onclick="deleteCart(${item.idstok})"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            `;

            inputs += `
                <input type="hidden" name="produk_cabang[]" value="${item.idstok}">
                <input type="hidden" name="qty[]" value="${qty}">
                <input type="hidden" name="type[]" value="produk">
            `;
        }
    });

    body.innerHTML = `
        ${html}

        <hr>

        <div class="ringkasan">
            <p>Total Item <span>${totalItem}</span></p>
            <p>Durasi <span>${durasi > 0 ? durasi + ' hari' : '-'}</span></p>
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

        <button class="btn-pesan" ${durasi === 0 ? 'disabled' : ''}>
            Pesan Sekarang
        </button>
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
document.addEventListener('DOMContentLoaded', function(){

    const metodeSelect = document.getElementById('metodeBayar');
    const catatan = document.getElementById('catatanBayar');
    const info = document.getElementById('infoTransfer');

    metodeSelect.addEventListener('change', function(){

        if(this.value === 'cash'){
    catatan.textContent = 'Pembayaran dilakukan langsung di toko';
            info.style.display = 'none';
        }
        else if(this.value === 'transfer'){
            catatan.textContent = 'Transfer ke rekening ';
            info.style.display = 'block';
        }
        else{
            catatan.textContent = '';
            info.style.display = 'none';
        }

    });

});
function openModal(el) {
    let nama = el.getAttribute('data-nama');
    let harga = el.getAttribute('data-harga');
    let gambar = el.getAttribute('data-gambar');
    let detail = el.getAttribute('data-detail');

    let list = detail.split('|');

    let html = '';
    list.forEach(item => {
        if (item.trim() !== '') {
            html += `<div>• ${item}</div>`;
        }
    });

    document.getElementById('modalNama').innerText = nama;
    document.getElementById('modalHarga').innerText = "Rp " + Number(harga).toLocaleString('id-ID');
    document.getElementById('modalGambar').src = gambar;
    document.getElementById('modalIsi').innerHTML = html;

    document.getElementById('modalDetail').style.display = 'block';
}

function closeModal() {
    document.getElementById('modalDetail').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('modalDetail');
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
</script>
