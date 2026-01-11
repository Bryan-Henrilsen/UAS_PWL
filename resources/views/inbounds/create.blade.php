@extends('layouts.master')
@section('title', 'Buat Inbound Baru')

@section('content')
<form action="{{ route('inbounds.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3 border-primary">
                <h5 class="text-primary mb-3">Informasi Inbound</h5>
                
                <div class="mb-3">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="inbound_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label>Foto Bukti (Surat Jalan/Barang)</label>
                    <input type="file" name="photo_proof" class="form-control" accept="image/*" required>
                    <small class="text-muted">Wajib upload bukti fisik.</small>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="fw-bold">Total Nilai Barang (Rp)</label>
                    <input type="text" id="grand_total_display" class="form-control bg-success text-white fw-bold fs-4" value="0" readonly>
                    <input type="hidden" name="total_amount" id="grand_total_value" value="0">
                </div>

                <button type="submit" class="btn btn-success w-100 mb-2">
                    <i class="fas fa-save"></i> Simpan Request
                </button>
                <a href="{{ route('inbounds.index') }}" class="btn btn-secondary w-100">Batal</a>
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
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Produk (Varian)</th>
                                <th width="20%">Harga Beli (Rp)</th> 
                                <th width="15%">Qty</th>
                                <th width="20%">Subtotal (Rp)</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable">
                            </tbody>
                    </table>
                </div>
                
                <div class="alert alert-light border mt-2 small text-muted">
                    <i class="fas fa-info-circle"></i> Pastikan harga beli yang diinput sesuai dengan invoice/nota supplier.
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let itemIndex = 0;

    // Simpan Opsi Produk di Variable JS (dari Blade)
    const variantOptions = `
        <option value="">-- Pilih Produk --</option>
        @foreach($variants as $v)
            <option value="{{ $v->id }}">{{ $v->fullname }}</option>
        @endforeach
    `;

    // 1. Fungsi Tambah Baris
    function addItemRow() {
        const table = document.getElementById('itemsTable');
        const rowId = `row-${itemIndex}`;
        
        const row = `
            <tr id="${rowId}">
                <td>
                    <select name="items[${itemIndex}][variant_id]" class="form-select" required>
                        ${variantOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control input-price" 
                            placeholder="0" min="0" oninput="calculateRow('${rowId}')" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][qty]" class="form-control input-qty" 
                            placeholder="1" min="1" oninput="calculateRow('${rowId}')" required>
                </td>
                <td>
                    <input type="text" class="form-control bg-light input-subtotal-display" readonly>
                    <input type="hidden" class="input-subtotal-value"> 
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button>
                </td>
            </tr>
        `;
        table.insertAdjacentHTML('beforeend', row);
        itemIndex++;
    }

    // 2. Hitung Subtotal per Baris
    function calculateRow(rowId) {
        const row = document.getElementById(rowId);
        
        // Ambil nilai input (jika kosong anggap 0)
        let price = parseFloat(row.querySelector('.input-price').value) || 0;
        let qty = parseFloat(row.querySelector('.input-qty').value) || 0;

        let subtotal = price * qty;

        // Tampilkan Subtotal Rupiah
        row.querySelector('.input-subtotal-display').value = formatRupiah(subtotal);
        row.querySelector('.input-subtotal-value').value = subtotal;

        // Update Grand Total
        calculateGrandTotal();
    }

    // 3. Hitung Grand Total (Sum semua Subtotal)
    function calculateGrandTotal() {
        let totalAmount = 0;
        
        // Loop semua input hidden subtotal
        document.querySelectorAll('.input-subtotal-value').forEach(input => {
            totalAmount += parseFloat(input.value) || 0;
        });

        // Tampilkan di Kolom Kiri
        document.getElementById('grand_total_display').value = formatRupiah(totalAmount);
        document.getElementById('grand_total_value').value = totalAmount;
    }

    // 4. Hapus Baris
    function removeRow(btn) {
        const tableBody = document.getElementById('itemsTable');
        if (tableBody.querySelectorAll('tr').length > 1) {
            btn.closest('tr').remove();
            calculateGrandTotal(); // Hitung ulang setelah hapus
        } else {
            alert("Minimal harus ada satu barang!");
        }
    }

    // Helper Format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { maximumSignificantDigits: 20 }).format(angka);
    }

    // Init: Tambahkan 1 baris kosong saat load
    document.addEventListener("DOMContentLoaded", function() {
        addItemRow();
    });
</script>
@endsection