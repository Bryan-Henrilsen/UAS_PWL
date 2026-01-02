@extends('layouts.master')
@section('title', 'Riwayat Stock Opname')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Riwayat Stock Opname</h4>
        <a href="{{ route('stock_opnames.create') }}" class="btn btn-primary">
            <i class="fas fa-clipboard-list"></i> Mulai Stock Opname Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Tanggal SO</th>
                    <th>Dikerjakan Oleh</th>
                    <th>Status</th>
                    <th>Disetujui Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOpnames as $so)
                <tr>
                    <td>{{ $so->so_date->format('d M Y') }}</td>
                    <td>{{ $so->requester->name }}</td>
                    <td>
                        @if($so->status == 'Requested')
                            <span class="badge bg-warning text-dark">Menunggu Review</span>
                        @elseif($so->status == 'Approved')
                            <span class="badge bg-success">Selesai (Stok Updated)</span>
                        @else
                            <span class="badge bg-danger">Dibatalkan</span>
                        @endif
                    </td>
                    <td>
                        {{ $so->approved_at ? 'Supervisor' : '-' }}
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('stock_opnames.show', $so->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($so->status == 'Requested')
                                @can('create_so') 
                                    @cannot('approve_so')
                                        <a href="{{ route('stock_opnames.edit', $so->id) }}" class="btn btn-sm btn-warning text-white" title="Edit Data">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endcannot
                                @endcan
                            @endif

                            @if($so->status == 'Requested')
                                @can('approve_so')
                                    @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada riwayat Stock Opname.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection