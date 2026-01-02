@extends('layouts.master')
@section('title', 'Laporan Stok & Aset')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Laporan Stok & Aset</h4>
            <p class="text-muted mb-0">Dicetak pada: {{ date('d F Y H:i') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.stock', ['export_pdf' => 'true']) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Produk</th>
                    <th>Varian (Warna/Size)</th>
                    <th class="text-end">Harga Beli/Aset (Rp)</th>
                    <th class="text-center">Stok Fisik</th>
                    <th class="text-end">Total Nilai Aset (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $key => $s)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $s->product->name }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $s->color }} - {{ $s->size }}
                        </span>
                        <br><small class="text-muted">{{ $s->sku_variant }}</small>
                    </td>
                    <td class="text-end">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold {{ $s->stock_qty > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $s->stock_qty }}
                    </td>
                    <td class="text-end fw-bold">Rp {{ number_format($s->stock_qty * $s->price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data stok.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="5" class="text-end fw-bold">TOTAL ESTIMASI ASET GUDANG</th>
                    <th class="text-end fw-bold text-primary fs-5">Rp {{ number_format($totalAsset, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection