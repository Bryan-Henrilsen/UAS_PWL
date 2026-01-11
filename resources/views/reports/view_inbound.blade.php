@extends('layouts.master')
@section('title', 'Laporan Barang Masuk')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Laporan Barang Masuk (Inbound)</h4>
            <p class="text-muted mb-0">
                Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($endDate)) }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('reports.inbound', ['start_date' => $startDate, 'end_date' => $endDate, 'export_pdf' => 'true']) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>No. Transaksi</th>
                    <th>Staff Gudang</th>
                    <th width="40%">Detail Barang (Harga Beli)</th>
                    <th class="text-end">Total Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inbounds as $in)
                <tr>
                    <td>{{ $in->inbound_date->format('d/m/Y') }}</td>
                    <td><span class="badge bg-secondary">#{{ $in->id }}</span></td>
                    <td>{{ $in->requester->name }}</td>
                    <td>
                        <ul class="mb-0 ps-3">
                        @foreach($in->details as $d)
                            <li class="mb-1">
                                <span class="fw-bold">{{ $d->variant->product->name }}</span> 
                                ({{ $d->variant->color }}/{{ $d->variant->size }}) <br>
                                <small class="text-muted">
                                    {{ $d->qty }} pcs x Rp {{ number_format($d->unit_price, 0, ',', '.') }} 
                                    = <strong>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</strong>
                                </small>
                            </li>
                        @endforeach
                        </ul>
                    </td>
                    <td class="text-end fw-bold">Rp {{ number_format($in->total_amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Tidak ada transaksi barang masuk pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end fw-bold">GRAND TOTAL PERIODE INI</th>
                    <th class="text-end fw-bold text-success fs-5">Rp {{ number_format($totalNominal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection