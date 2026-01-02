@extends('layouts.master')
@section('title', 'Laporan Barang Keluar')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Laporan Barang Keluar (Outbound)</h4>
            <p class="text-muted mb-0">
                Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($endDate)) }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('reports.outbound', ['start_date' => $startDate, 'end_date' => $endDate, 'export_pdf' => 'true']) }}" class="btn btn-danger">
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
                    <th>Tujuan / Customer</th>
                    <th>Staff / Sales</th>
                    <th>Detail Barang</th>
                    <th class="text-end">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outbounds as $out)
                <tr>
                    <td>{{ $out->outbound_date->format('d/m/Y') }}</td>
                    <td>
                        {{ $out->delivery_data }}<br>
                        <small class="text-muted">ID: #{{ $out->id }}</small>
                    </td>
                    <td>{{ $out->requester->name }}</td>
                    <td>
                        <ul class="mb-0 ps-3 small">
                        @foreach($out->details as $d)
                            <li>
                                {{ $d->variant->product->name }} 
                                ({{ $d->variant->size }}) 
                                : <strong>{{ $d->qty }} pcs</strong>
                            </li>
                        @endforeach
                        </ul>
                    </td>
                    <td class="text-end fw-bold">Rp {{ number_format($out->grand_total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Tidak ada transaksi barang keluar pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end fw-bold">GRAND TOTAL PENJUALAN</th>
                    <th class="text-end fw-bold text-primary fs-5">Rp {{ number_format($totalGrand, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection