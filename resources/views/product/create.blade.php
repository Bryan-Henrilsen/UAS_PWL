@extends('layouts.master')
@section('title', 'Tambah Produk')

@section('content')
<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <h5 class="mb-3">Informasi Produk</h5>
                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Kemeja Flannel" required>
                </div>
                <div class="mb-3">
                    <label>SKU Base (Kode Induk)</label>
                    <input type="text" name="sku_base" class="form-control" placeholder="Contoh: KMJ-FLN01" required>
                    <small class="text-muted">Kode ini akan menjadi awalan SKU varian.</small>
                </div>
                <div class="mb-3">
                    <label>Foto Utama</label>
                    <input type="file" name="photo_main" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100 py-2">
                <i class="fas fa-save"></i> Simpan Semua
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Daftar Varian</h5>
                    <button type="button" class="btn btn-sm btn-info text-white" onclick="addVariantRow()">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">Size</th>
                            <th width="20%">Color</th>
                            <th width="25%">Harga (Rp)</th>
                            <th width="30%">Foto Varian</th> <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="variantTableBody">
                        <tr>
                            <td>
                                <input type="text" name="variants[0][size]" class="form-control form-control-sm" placeholder="Size" required>
                            </td>
                            <td>
                                <input type="text" name="variants[0][color]" class="form-control form-control-sm" placeholder="Color" required>
                            </td>
                            <td>
                                <input type="number" name="variants[0][price]" class="form-control form-control-sm" placeholder="0" required>
                            </td>
                            <td>
                                <input type="file" name="variants[0][photo]" class="form-control form-control-sm" accept="image/*">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" disabled>Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

<script>
    let variantIndex = 1;

    function addVariantRow() {
        const tableBody = document.getElementById('variantTableBody');
        const row = `
            <tr>
                <td>
                    <input type="text" name="variants[${variantIndex}][size]" class="form-control form-control-sm" placeholder="Size" required>
                </td>
                <td>
                    <input type="text" name="variants[${variantIndex}][color]" class="form-control form-control-sm" placeholder="Color" required>
                </td>
                <td>
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control form-control-sm" placeholder="Harga" required>
                </td>
                <td>
                    <input type="file" name="variants[${variantIndex}][photo]" class="form-control form-control-sm" accept="image/*">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Hapus</button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', row);
        variantIndex++;
    }
</script>
@endsection