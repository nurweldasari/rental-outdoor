@push('styles')
<link rel="stylesheet" href="{{ asset('css/tambah_rekening.css') }}">
@endpush

{{-- MODAL TAMBAH --}}
<div class="modal" id="modalRekening">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Tambah Rekening Cabang</h3>
            <button class="btn-lihat-rekening" onclick="showRekening()">
                Lihat Rekening
            </button>
        </div>

        {{-- FORM (TETAP PUNYAMU) --}}
        <form action="{{ route('rekening.store') }}" method="POST">
            @csrf
            <select name="cabang_idcabang" required>
                <option value="">Pilih Cabang</option>
                @foreach ($listCabang as $c)
                    <option value="{{ $c->idcabang }}">
                        {{ $c->nama_cabang }}
                    </option>
                @endforeach
            </select>

            <input name="nama_bank" placeholder="Nama Bank" required>
            <input name="no_rekening" placeholder="No. Rekening" required>
            <input name="atas_nama" placeholder="Atas Nama" required>

            <div class="form-actions">
                <button type="submit" class="btn-simpan">Simpan</button>
                <button type="button" class="btn-batal" onclick="closeRekeningModal()">Batal</button>
            </div>
        </form>
    </div>
</div>


{{-- CONTAINER TABEL REKENING --}}
<div class="container-rekening" id="containerRekening" style="display:none;">

    <div class="card-rekening">

        {{-- JUDUL --}}
        <div class="header-rekening">
            <h3>Rekening Cabang</h3>
        </div>

        {{-- FILTER --}}
        <div class="table-controls">
            <div class="entries-box">
                <form method="GET" class="entries-form">
                    <select name="entries" onchange="this.form.submit()">
                        <option value="20" {{ $entries == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $entries == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $entries == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span>Data Per Halaman</span>
                </form>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInputRekening" placeholder="Pencarian...">
            </div>
        </div>

        {{-- TABLE --}}
        <table class="table-rekening" id="rekeningTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Cabang</th>
                    <th>Lokasi Cabang</th>
                    <th>Nama Bank</th>
                    <th>No. Rekening</th>
                    <th>Atas Nama</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($rekening as $i => $r)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $r->cabang->nama_cabang }}</td>
                    <td>{{ $r->cabang->lokasi }}</td>
                    <td>{{ $r->nama_bank }}</td>
                    <td>{{ $r->no_rekening }}</td>
                    <td>{{ $r->atas_nama }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>

<script>
function showRekening() {
    document.getElementById('modalRekening').style.display = 'none';
    document.getElementById('containerCabang').style.display = 'none'; 
    document.getElementById('containerRekening').style.display = 'block';
}

function showTambah() {
    document.getElementById('containerRekening').style.display = 'none';
    document.getElementById('containerCabang').style.display = 'block'; 
    document.getElementById('modalRekening').style.display = 'flex';
}

function closeRekeningModal() {
    document.getElementById('modalRekening').style.display = 'none';
}
document.getElementById('searchInputRekening')
.addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('#rekeningTable tbody tr');

    rows.forEach(row => {
        row.style.display = row.innerText
            .toLowerCase()
            .includes(keyword)
            ? ''
            : 'none';
    });
});
</script>
