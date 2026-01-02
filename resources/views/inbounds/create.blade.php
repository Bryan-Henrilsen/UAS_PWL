@extends('layouts.master')
@section('title', 'Buat Inbound')

@section('content')
<form action="{{ route('inbounds.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <h5>Data Surat Jalan</h5>
                <div class="mb-3">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="inbound_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label>Foto Bukti (Surat Jalan/Barang)</label>
                    <input type="file" name="photo_proof" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Simpan Request</button>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Daftar Barang Masuk</h5>
                    <button type="button" class="btn btn-sm btn-info text-white" onclick="addItemRow()">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>
                
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Pilih Produk (Varian)</th>
                            <th width="150">Harga Beli (@pcs)</th> 
                            <th width="100">Qty</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTable">
                        <tr>
                            <td>
                                <select name="items[0][variant_id]" class="form-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($variants as $v)
                                        <option value="{{ $v->id }}">{{ $v->fullname }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][unit_price]" class="form-control" placeholder="Rp" min="0" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][qty]" class="form-control" placeholder="0" min="1" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" disabled>Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
    let itemIndex = 1;
    // Kita simpan opsi produk di variable JS biar bisa dipanggil ulang
    const variantOptions = `
        <option value="">-- Pilih Produk --</option>
        @foreach($variants as $v)
            <option value="{{ $v->id }}">{{ $v->fullname }}</option>
        @endforeach
    `;

    function addItemRow() {
        const table = document.getElementById('itemsTable');
        const row = `
            <tr>
                <td>
                    <select name="items[${itemIndex}][variant_id]" class="form-select" required>
                        ${variantOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control" placeholder="Rp" min="0" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][qty]" class="form-control" placeholder="0" min="1" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Hapus</button>
                </td>
            </tr>
        `;
        table.insertAdjacentHTML('beforeend', row);
        itemIndex++;
    }
</script>
@endsection