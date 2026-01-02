@extends('layouts.master')
@section('title', 'Buat Outbound')

@section('content')
<form action="{{ route('outbounds.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <h5>Data Pengiriman</h5>
                <div class="mb-3">
                    <label>Tanggal Keluar</label>
                    <input type="date" name="outbound_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label>Tujuan / Ekspedisi</label>
                    <textarea name="delivery_data" class="form-control" rows="2" placeholder="Cth: PTK - PUSAT CANTIK"></textarea>
                </div>
                <div class="mb-3">
                    <label>Foto Packing</label>
                    <input type="file" name="photo_proof" class="form-control" accept="image/*">
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <label>Total Sebelum Pajak (Rp)</label>
                    <input type="text" id="total_amount_display" class="form-control bg-light" readonly>
                    <input type="hidden" name="total_amount" id="total_amount_value">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label>Pajak (%)</label>
                            <input type="number" name="tax_rate" id="tax_rate" class="form-control" placeholder="0" min="0" value="0" oninput="calculateGrandTotal()">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label>Nominal Pajak (Rp)</label>
                            <input type="text" id="tax_amount_display" class="form-control bg-light" readonly>
                            <input type="hidden" name="tax_global" id="tax_global_value"> 
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Grand Total (Rp)</label>
                    <input type="text" id="grand_total_display" class="form-control bg-warning fw-bold text-dark" readonly>
                    <input type="hidden" name="grand_total" id="grand_total_value">
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan Outbound</button>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Barang Keluar</h5>
                    <button type="button" class="btn btn-sm btn-info text-white" onclick="addItemRow()">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>
                
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th width="15%">Harga (Rp)</th>
                            <th width="10%">Disc (%)</th>
                            <th width="10%">Qty</th>
                            <th width="20%">Subtotal (Rp)</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTable">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
    let itemIndex = 0;

    // Template Option untuk Dropdown Produk
    const variantOptions = `
        <option value="" data-price="0">-- Pilih Produk --</option>
        @foreach($variants as $v)
            <option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock_qty }}">
                {{ $v->fullname }}
            </option>
        @endforeach
    `;

    // 1. Fungsi Tambah Baris
    function addItemRow() {
        const table = document.getElementById('itemsTable');
        const rowId = `row-${itemIndex}`;
        
        const row = `
            <tr id="${rowId}">
                <td>
                    <select name="items[${itemIndex}][variant_id]" class="form-select" required onchange="setPrice(this, '${rowId}')">
                        ${variantOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control input-price" placeholder="0" min="0" oninput="calculateRow('${rowId}')" required readonly>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][discount_percent]" class="form-control input-disc" placeholder="0" min="0" max="100" value="0" oninput="calculateRow('${rowId}')">
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][qty]" class="form-control input-qty" placeholder="1" min="1" value="1" oninput="calculateRow('${rowId}')" required>
                </td>
                <td>
                    <input type="text" class="form-control bg-light input-subtotal-display" readonly>
                    <input type="hidden" name="items[${itemIndex}][subtotal]" class="input-subtotal-value">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button>
                </td>
            </tr>
        `;
        table.insertAdjacentHTML('beforeend', row);
        itemIndex++;
    }

    // 2. Fungsi Set Harga Otomatis saat Pilih Produk
    function setPrice(selectElement, rowId) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const stock = selectedOption.getAttribute('data-stock') || 0;

        // Set harga ke input
        const row = document.getElementById(rowId);
        row.querySelector('.input-price').value = parseInt(price);
        
        // Peringatan stok
        if(parseInt(stock) <= 0) alert("Stok Habis! Transaksi mungkin ditolak.");

        // Hitung ulang baris ini
        calculateRow(rowId);
    }

    // 3. Fungsi Hitung Subtotal Per Baris
    function calculateRow(rowId) {
        const row = document.getElementById(rowId);
        
        let price = parseFloat(row.querySelector('.input-price').value) || 0;
        let discPercent = parseFloat(row.querySelector('.input-disc').value) || 0;
        let qty = parseFloat(row.querySelector('.input-qty').value) || 0;

        // Rumus: (Harga - (Harga * Diskon% / 100)) * Qty
        let discountAmount = price * (discPercent / 100);
        let netPrice = price - discountAmount;
        let subtotal = netPrice * qty;

        // Tampilkan hasil (Format Rupiah di display, Angka murni di hidden)
        row.querySelector('.input-subtotal-display').value = formatRupiah(subtotal);
        row.querySelector('.input-subtotal-value').value = subtotal;

        // Panggil fungsi hitung Grand Total setiap kali ada perubahan di baris
        calculateGrandTotal();
    }

    // 4. Fungsi Hitung Grand Total (Semua Baris + Pajak Global)
    function calculateGrandTotal() {
        let totalAmount = 0;
        
        // Loop semua subtotal yang ada
        const subtotals = document.querySelectorAll('.input-subtotal-value');
        subtotals.forEach(input => {
            totalAmount += parseFloat(input.value) || 0;
        });

        // Ambil Pajak Global
        let taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        let taxAmount = totalAmount * (taxRate / 100);
        let grandTotal = totalAmount + taxAmount;

        // Update Tampilan Header
        document.getElementById('total_amount_display').value = formatRupiah(totalAmount);
        document.getElementById('total_amount_value').value = totalAmount;

        document.getElementById('tax_amount_display').value = formatRupiah(taxAmount);
        document.getElementById('tax_global_value').value = taxAmount;

        document.getElementById('grand_total_display').value = formatRupiah(grandTotal);
        document.getElementById('grand_total_value').value = grandTotal;
    }

    // Helper: Hapus Baris
    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateGrandTotal(); // Hitung ulang total
    }

    // Helper: Format Rupiah 
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { maximumSignificantDigits: 20 }).format(angka);
    }

    // Init: Tambah 1 baris saat load
    document.addEventListener("DOMContentLoaded", function() {
        addItemRow(); 
    });
</script>
@endsection