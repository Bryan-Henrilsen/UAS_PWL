@extends('layouts.master')
@section('title', 'Edit Inbound')

@section('content')
<form action="{{ route('inbounds.update', $inbound->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') 
    
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3 border-primary">
                <h5 class="text-primary">Perbaikan Data Masuk</h5>
                
                @if($inbound->status == 'Revision')
                <div class="alert alert-info py-2 small">
                    <strong>Catatan Revisi:</strong><br> 
                    "{{ $inbound->note }}"
                </div>
                @endif

                <div class="mb-3">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="inbound_date" class="form-control" 
                            value="{{ old('inbound_date', $inbound->inbound_date->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label>Ganti Foto Bukti (Opsional)</label>
                    <input type="file" name="photo_proof" class="form-control" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
                    
                    @if($inbound->photo_proof)
                        <div class="mt-2">
                            <a href="{{ asset('storage/'.$inbound->photo_proof) }}" target="_blank" class="small text-decoration-none">
                                <i class="fas fa-image"></i> Lihat Foto Saat Ini
                            </a>
                        </div>
                    @endif
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <label class="fw-bold">Total Nilai Barang (Rp)</label>
                    <input type="text" id="grand_total_display" class="form-control bg-success fw-bold text-white" readonly>
                    <input type="hidden" name="total_amount" id="grand_total_value">
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan Perbaikan</button>
                <a href="{{ route('inbounds.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Daftar Barang</h5>
                    <button type="button" class="btn btn-sm btn-info text-white" onclick="addItemRow()">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th width="20%">Harga Beli (Rp)</th>
                                <th width="15%">Qty</th>
                                <th width="25%">Subtotal (Rp)</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let itemIndex = 0;

    // 1. Template Opsi Produk (Dropdown)
    const variantOptions = `
        <option value="">-- Pilih Produk --</option>
        @foreach($variants as $v)
            <option value="{{ $v->id }}">{{ $v->fullname }}</option>
        @endforeach
    `;

    // 2. Ambil Data Lama dari Controller (JSON)
    const existingItems = @json($inbound->details);

    // 3. Fungsi Tambah Baris (Bisa data baru / data lama)
    function addItemRow(data = null) {
        const table = document.getElementById('itemsTable');
        const rowId = `row-${itemIndex}`;
        
        // Default Value (Jika data lama ada, pakai itu. Jika baru, kosong/nol)
        const variantId = data ? data.variant_id : '';
        const price = data ? parseInt(data.unit_price) : '';
        const qty = data ? parseInt(data.qty) : 1;

        const row = `
            <tr id="${rowId}">
                <td>
                    <select name="items[${itemIndex}][variant_id]" class="form-select item-select" required>
                        ${variantOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control input-price" 
                            placeholder="0" min="0" value="${price}" oninput="calculateRow('${rowId}')" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][qty]" class="form-control input-qty" 
                            placeholder="1" min="1" value="${qty}" oninput="calculateRow('${rowId}')" required>
                </td>
                <td>
                    <input type="text" class="form-control bg-light input-subtotal-display" readonly>
                    </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button>
                </td>
            </tr>
        `;
        table.insertAdjacentHTML('beforeend', row);

        // Set Selected Option untuk data lama
        if(variantId) {
            const select = document.querySelector(`#${rowId} .item-select`);
            select.value = variantId;
        }

        // Hitung Subtotal awal baris ini
        calculateRow(rowId);

        itemIndex++;
    }

    // 4. Hitung Subtotal per Baris
    function calculateRow(rowId) {
        const row = document.getElementById(rowId);
        let price = parseFloat(row.querySelector('.input-price').value) || 0;
        let qty = parseFloat(row.querySelector('.input-qty').value) || 0;

        let subtotal = price * qty;

        // Tampilkan Subtotal Rupiah
        row.querySelector('.input-subtotal-display').value = formatRupiah(subtotal);

        // Update Grand Total Keseluruhan
        calculateGrandTotal();
    }

    // 5. Hitung Grand Total (Semua Baris)
    function calculateGrandTotal() {
        let totalAmount = 0;
        
        // Loop semua input harga & qty untuk hitung ulang (lebih akurat daripada ambil text subtotal)
        document.querySelectorAll('#itemsTable tr').forEach(row => {
            let price = parseFloat(row.querySelector('.input-price').value) || 0;
            let qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            totalAmount += (price * qty);
        });

        // Tampilkan di Kolom Kiri
        document.getElementById('grand_total_display').value = formatRupiah(totalAmount);
        document.getElementById('grand_total_value').value = totalAmount;
    }

    // 6. Hapus Baris
    function removeRow(btn) {
        const tableBody = document.getElementById('itemsTable');
        // Validasi: Sisakan minimal 1 baris
        if (tableBody.querySelectorAll('tr').length > 1) {
            btn.closest('tr').remove();
            calculateGrandTotal();
        } else {
            alert("Minimal harus ada satu barang!");
        }
    }

    // Helper Format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { maximumSignificantDigits: 20 }).format(angka);
    }

    // 7. INIT: Jalankan saat halaman loading
    document.addEventListener("DOMContentLoaded", function() {
        if(existingItems && existingItems.length > 0) {
            existingItems.forEach(item => {
                addItemRow(item);
            });
        } else {
            addItemRow(); // Baris kosong default jika data error
        }
    });
</script>
@endsection