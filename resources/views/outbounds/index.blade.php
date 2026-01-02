@extends('layouts.master')
@section('title', 'Barang Keluar')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Riwayat Outbound (Keluar)</h4>
        <a href="{{ route('outbounds.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Request Outbound
        </a>
    </div>

    <table class="table table-hover table-bordered">
        <thead class="table-light">
            <tr>
                <th>Tanggal</th>
                <th>Request Oleh</th>
                <th>Jumlah Item</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outbounds as $outbound)
            <tr>
                <td>{{ $outbound->outbound_date->format('d M Y') }}</td>
                <td>{{ $outbound->requester->name }}</td>
                <td>{{ $outbound->details->count() }} Jenis Barang</td>
                <td>{{ number_format($outbound->grand_total, 0, ',', '.') }}</td>
                <td>
                    @if($outbound->status == 'Requested')
                        <span class="badge bg-warning">Menunggu Approval</span>
                    @elseif($outbound->status == 'Sent')
                        <span class="badge bg-success">Selesai (Stok Keluar)</span>
                    @elseif($outbound->status == 'Revision')
                        <span class="badge bg-warning text-dark">Perlu Direvisi</span>
                    @else
                        <span class="badge bg-danger">Dibatalkan</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('outbounds.show', $outbound->id) }}" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($outbound->status == 'Requested' || $outbound->status == 'Revision')
                            
                            @can('create_outbound')
                                @cannot('approve_outbound')
                                <a href="{{ route('outbounds.edit', $outbound->id) }}" class="btn btn-sm btn-warning text-white">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endcannot
                            @endcan

                            @can('approve_outbound')
                                @endcan

                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada transaksi outbound.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection