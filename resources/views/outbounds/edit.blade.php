@extends('layouts.master')
@section('title', 'Edit Outbound')

@section('content')
<form action="{{ route('outbounds.update', $outbound->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3 border-primary">
                <h5 class="text-primary">Perbaikan Data</h5>
                <div class="alert alert-info py-2 small">
                    <strong>Catatan Revisi:</strong><br> {{ $outbound->note }}
                </div>

                <div class="mb-3">
                    <label>Tanggal Keluar</label>
                    <input type="date" name="outbound_date" class="form-control" value="{{ $outbound->outbound_date->format('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label>Tujuan / Ekspedisi</label>
                    <textarea name="delivery_data" class="form-control" rows="2">{{ $outbound->delivery_data }}</textarea>
                </div>
                <div class="mb-3">
                    <label>Ganti Foto Packing (Opsional)</label>
                    <input type="file" name="photo_proof" class="form-control" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>

                    @if($outbound->photo_proof)
                        <div class="mt-2">
                            <a href="{{ asset('storage/'.$outbound->photo_proof) }}" target="_blank" class="small text-decoration-none">
                                <i class="fas fa-image"></i> Lihat Foto Saat Ini
                            </a>
                        </div>
                    @endif
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
                            <input type="number" name="tax_rate" id="tax_rate" class="form-control" min="0" value="{{ $outbound->tax_rate }}" oninput="calculateGrandTotal()">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label>Nominal Pajak (Rp)</label>
                            <input type="text" id="tax_amount_display" class="form-control bg-light" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Grand Total (Rp)</label>
                    <input type="text" id="grand_total_display" class="form-control bg-warning fw-bold text-dark" readonly>
                    <input type="hidden" name="grand_total" id="grand_total_value">
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan Perbaikan</button>
                <a href="{{ route('outbounds.show', $outbound->id) }}" class="btn btn-secondary w-100 mt-2">Batal</a>
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

    // Template Option
    const variantOptions = `
        <option value="" data-price="0">-- Pilih Produk --</option>
        @foreach($variants as $v)
            <option value="{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock_qty }}">
                {{ $v->fullname }}
            </option>
        @endforeach
    `;

    // Ambil data lama dari Controller (Pass as JSON)
    const existingItems = @json($outbound->details);

    function addItemRow(data = null) {
        const table = document.getElementById('itemsTable');
        const rowId = `row-${itemIndex}`;
        
        // Jika ada data (dari database), pakai nilainya. Jika tidak, default kosong.
        const variantId = data ? data.variant_id : '';
        const price = data ? parseInt(data.unit_price) : '';
        const disc = data ? parseFloat(data.discount_percent) : 0;
        const qty = data ? parseInt(data.qty) : 1;

        const row = `
            <tr id="${rowId}">
                <td>
                    <select name="items[${itemIndex}][variant_id]" class="form-select item-select" required onchange="setPrice(this, '${rowId}')">
                        ${variantOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control input-price" placeholder="0" min="0" value="${price}" oninput="calculateRow('${rowId}')" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][discount_percent]" class="form-control input-disc" placeholder="0" min="0" max="100" value="${disc}" oninput="calculateRow('${rowId}')">
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][qty]" class="form-control input-qty" placeholder="1" min="1" value="${qty}" oninput="calculateRow('${rowId}')" required>
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

        // Jika ini data lama, kita harus SET value dropdownnya secara manual
        if(variantId) {
            const select = document.querySelector(`#${rowId} .item-select`);
            select.value = variantId;
        }

        // Hitung subtotal baris ini
        calculateRow(rowId);

        itemIndex++;
    }

    // Fungsi Set Harga (Copy dari Create)
    function setPrice(selectElement, rowId) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const row = document.getElementById(rowId);
        row.querySelector('.input-price').value = parseInt(price);
        calculateRow(rowId);
    }

    // Fungsi Hitung Row (Copy dari Create)
    function calculateRow(rowId) {
        const row = document.getElementById(rowId);
        let price = parseFloat(row.querySelector('.input-price').value) || 0;
        let discPercent = parseFloat(row.querySelector('.input-disc').value) || 0;
        let qty = parseFloat(row.querySelector('.input-qty').value) || 0;

        let discountAmount = price * (discPercent / 100);
        let netPrice = price - discountAmount;
        let subtotal = netPrice * qty;

        row.querySelector('.input-subtotal-display').value = formatRupiah(subtotal);
        row.querySelector('.input-subtotal-value').value = subtotal;

        calculateGrandTotal();
    }

    // Fungsi Hitung Grand Total (Copy dari Create)
    function calculateGrandTotal() {
        let totalAmount = 0;
        document.querySelectorAll('.input-subtotal-value').forEach(input => {
            totalAmount += parseFloat(input.value) || 0;
        });

        let taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        let taxAmount = totalAmount * (taxRate / 100);
        let grandTotal = totalAmount + taxAmount;

        document.getElementById('total_amount_display').value = formatRupiah(totalAmount);
        document.getElementById('total_amount_value').value = totalAmount;
        document.getElementById('tax_amount_display').value = formatRupiah(taxAmount);
        document.getElementById('grand_total_display').value = formatRupiah(grandTotal);
        document.getElementById('grand_total_value').value = grandTotal;
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateGrandTotal();
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { maximumSignificantDigits: 20 }).format(angka);
    }

    // INIT: Load data lama
    document.addEventListener("DOMContentLoaded", function() {
        if(existingItems && existingItems.length > 0) {
            existingItems.forEach(item => {
                addItemRow(item);
            });
        } else {
            addItemRow(); // Default blank row jika kosong
        }
    });
</script>
@endsection