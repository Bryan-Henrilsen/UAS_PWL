@extends('layouts.master')
@section('title', 'Barang Masuk')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Riwayat Inbound (Masuk)</h4>
        <a href="{{ route('inbounds.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Request Inbound
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
            @forelse($inbounds as $inbound)
            <tr>
                <td>{{ $inbound->inbound_date->format('d M Y') }}</td>
                <td>{{ $inbound->requester->name }}</td>
                <td>{{ $inbound->details->count() }} Jenis Barang</td>
                <td>{{ number_format($inbound->total_amount, 0, ',', '.') }}</td>
                <td>
                    @if($inbound->status == 'Requested')
                        <span class="badge bg-warning">Menunggu Approval</span>
                    @elseif($inbound->status == 'Approved')
                        <span class="badge bg-success">Selesai (Stok Masuk)</span>
                    @elseif($inbound->status == 'Revision')
                        <span class="badge bg-warning text-dark">Perlu Direvisi</span>
                    @else
                        <span class="badge bg-danger">Dibatalkan</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('inbounds.show', $inbound->id) }}" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($inbound->status == 'Requested' || $inbound->status == 'Revision')
                            
                            @can('create_inbound')
                                @cannot('approve_inbound')
                                <a href="{{ route('inbounds.edit', $inbound->id) }}" class="btn btn-sm btn-warning text-white">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endcannot
                            @endcan

                            @can('approve_inbound')
                                @endcan

                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada transaksi inbound.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection