@extends('layouts.master')
@section('title', 'Edit Stock Opname')

@section('content')
<form action="{{ route('stock_opnames.update', $stockOpname->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') 
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3 border-primary">
                <h5 class="text-primary"><i class="fas fa-info-circle"></i> Informasi Sesi SO</h5>
                <p class="text-muted small">
                    Masukkan hasil hitungan fisik (Blind Count). Sistem akan membandingkannya dengan data komputer secara otomatis setelah disimpan.
                </p>
                
                <div class="mb-3">
                    <label>Tanggal Pengerjaan</label>
                    <input type="date" name="so_date" class="form-control" value="{{ $stockOpname->so_date->format('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label>Ganti Foto Dokumentasi (Opsional)</label>
                    <input type="file" name="photo_proof" class="form-control" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Pastikan semua hitungan fisik sudah benar. Lanjutkan?')">
                    <i class="fas fa-save"></i> Simpan Hasil Hitungan
                </button>
                <a href="{{ route('stock_opnames.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Lembar Kerja Hitung Fisik</h5>
                    <button type="button" class="btn btn-sm btn-success" onclick="addItemRow()">
                        <i class="fas fa-plus"></i> Tambah Baris Barang
                    </button>
                </div>
                
                <div class="alert alert-warning py-2 small">
                    <i class="fas fa-exclamation-triangle"></i> <strong>PENTING (BACA SOP):</strong>
                    <ul class="mb-0 ps-3">
                        <li>Masukkan HANYA jumlah barang yang <strong>Bagus/Layak Jual</strong>. Barang rusak jangan dihitung (tulis di catatan).</li>
                        <li>Hanya barang yang Anda masukkan di tabel ini yang stoknya akan di-update. Barang yang tidak dipilih stoknya <strong>TETAP/TIDAK BERUBAH</strong>.</li>
                        <li>Jika fisik barang habis (0), <strong>WAJIB</strong> pilih barang tersebut dan isi Qty 0. Jangan dilewatkan.</li>
                    </ul>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Produk (Varian)</th>
                            <th width="20%">Qty Fisik (Pcs)</th>
                            <th width="25%">Catatan Kondisi (Opsional)</th>
                            <th width="5%">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTable">
                        @foreach($stockOpname->details as $index => $detail)
                        <tr>
                            <td>
                                <select name="items[{{ $index }}][variant_id]" class="form-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($variants as $v)
                                        <option value="{{ $v->id }}"
                                            {{ $detail->variant_id == $v->id ? 'selected' : '' }}>
                                            {{ $v->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number"
                                        name="items[{{ $index }}][qty_actual]"
                                        class="form-control fw-bold"
                                        min="0"
                                        value="{{ $detail->qty_actual }}"
                                        required>
                            </td>
                            <td>
                                <input type="text"
                                        name="items[{{ $index }}][reason]"
                                        class="form-control"
                                        value="{{ $detail->reason }}">
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="this.closest('tr').remove()">X</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
    let itemIndex = {{ $stockOpname->details->count() }};

    // List Produk untuk Dropdown
    const variantOptions = `
        <option value="">-- Pilih Produk yang Dihitung --</option>
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
                    <input type="number" name="items[${itemIndex}][qty_actual]" class="form-control fw-bold" placeholder="0" min="0" required>
                </td>
                <td>
                    <input type="text" name="items[${itemIndex}][reason]" class="form-control" placeholder="Cth: Sobek / Baik">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button>
                </td>
            </tr>
        `;
        table.insertAdjacentHTML('beforeend', row);
        itemIndex++;
    }

    const hasDetails = {{ $stockOpname->details->isNotEmpty() ? 'true' : 'false' }};

    if (!hasDetails) {
        document.addEventListener("DOMContentLoaded", () => addItemRow());
    }

</script>
@endsection