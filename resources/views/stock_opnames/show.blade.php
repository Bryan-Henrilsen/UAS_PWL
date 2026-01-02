@extends('layouts.master')
@section('title', 'Detail Stock Opname')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card p-3 mb-3">
            <h5>Info Stock Opname</h5>
            <hr>
            <p><strong>Tanggal:</strong> {{ $stockOpname->so_date->format('d M Y') }}</p>
            <p><strong>Pencacah (Staff):</strong> {{ $stockOpname->requester->name }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ $stockOpname->status == 'Approved' ? 'success' : ($stockOpname->status == 'Requested' ? 'warning' : 'danger') }}">
                    {{ $stockOpname->status }}
                </span>
            </p>
            
            @if($stockOpname->status == 'Approved')
            <p><strong>Disetujui Oleh:</strong> {{ $stockOpname->approver->name ?? '-' }}</p>
            @endif

            @if($stockOpname->approved_at)
                <div class="alert alert-success py-2 small">
                    <i class="fas fa-check-circle"></i> Disetujui & Stok Disesuaikan pada:<br>
                    <strong>{{ $stockOpname->approved_at->format('d M Y H:i') }}</strong>
                </div>
            @endif
            
            <div class="mt-3">
                <label>Foto Dokumentasi:</label><br>
                @if($stockOpname->photo_proof)
                    <img src="{{ asset('storage/'.$stockOpname->photo_proof) }}" class="img-fluid rounded mt-2 border">
                @else
                    <span class="text-muted fst-italic">- Tidak ada foto -</span>
                @endif
            </div>

            <hr>

            @if($stockOpname->status == 'Requested')
                
                @can('approve_so')
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> <strong>Perhatian Supervisor:</strong><br>
                        Klik Approve akan mengubah Stok Master sesuai Qty Fisik.
                    </div>

                    <form action="{{ route('stock_opnames.approve', $stockOpname->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin Approve? Stok Master akan di-update sesuai Fisik!')">
                        @csrf
                        <button class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check-double"></i> Approve (Sesuaikan Stok)
                        </button>
                    </form>
                @endcan

                <form action="{{ route('stock_opnames.cancel', $stockOpname->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin batalkan? Data hitungan akan diabaikan.')">
                    @csrf
                    <button class="btn btn-danger w-100">
                        <i class="fas fa-times"></i> Batalkan / Reject
                    </button>
                </form>
            @endif

            <a href="{{ route('stock_opnames.index') }}" class="btn btn-secondary w-100 mt-2">Kembali</a>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-3">
            <h5 class="mb-3">Hasil Hitungan</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Produk</th>
                            
                            @can('approve_so')
                                <th class="text-center bg-secondary">Qty Sistem</th>
                            @endcan

                            <th class="text-center bg-primary">Qty Fisik</th>

                            @can('approve_so')
                                <th class="text-center">Selisih</th>
                            @endcan

                            <th>Keterangan Staff</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockOpname->details as $detail)
                        <tr>
                            <td>
                                <strong>{{ $detail->variant->product->name }}</strong><br>
                                <small class="text-muted">
                                    {{ $detail->variant->color }} / {{ $detail->variant->size }}
                                </small>
                            </td>
                            
                            @can('approve_so')
                                <td class="text-center bg-light text-muted">
                                    {{ $detail->qty_system }}
                                </td>
                            @endcan

                            <td class="text-center fw-bold fs-5 text-primary">
                                {{ $detail->qty_actual }}
                            </td>

                            @can('approve_so')
                                <td class="text-center fw-bold">
                                    @if($detail->qty_diff == 0)
                                        <span class="badge bg-success">COCOK</span>
                                    @elseif($detail->qty_diff < 0)
                                        <span class="badge bg-danger fs-6">{{ $detail->qty_diff }}</span>
                                        <div class="small text-danger">Hilang</div>
                                    @else
                                        <span class="badge bg-warning text-dark fs-6">+{{ $detail->qty_diff }}</span>
                                        <div class="small text-warning-dark">Lebih</div>
                                    @endif
                                </td>
                            @endcan

                            <td>
                                @if($detail->reason)
                                    <i class="fas fa-comment-dots text-muted"></i> {{ $detail->reason }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @cannot('approve_so')
                <div class="alert alert-light border mt-3 text-center text-muted small">
                    <i class="fas fa-lock"></i> Kolom <strong>Qty Sistem</strong> dan <strong>Selisih</strong> disembunyikan (Blind Count).<br>
                    Silakan hubungi Supervisor untuk hasil analisis selisih.
                </div>
            @endcannot

        </div>
    </div>
</div>
@endsection