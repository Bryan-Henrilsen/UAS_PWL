@extends('layouts.master')
@section('title', 'Detail Inbound')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card p-3 mb-3">
            <h5>Info Inbound</h5>
            <hr>
            <p><strong>Tanggal:</strong> {{ $inbound->inbound_date->format('d M Y') }}</p>
            <p><strong>Dibuat Oleh:</strong> {{ $inbound->requester->name }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ $inbound->status == 'Approved' ? 'success' : ($inbound->status == 'Requested' ? 'warning' : ($inbound->status == 'Revision' ? 'info' : 'danger')) }}">
                    {{ $inbound->status }}
                </span>
            </p>
            
            @if($inbound->status == 'Approved')
                <p><strong>Disetujui Oleh:</strong> {{ $inbound->approver->name ?? '-' }}</p>
                <div class="alert alert-success py-2 small">
                    <i class="fas fa-check-circle"></i> Stok telah ditambahkan ke sistem.
                </div>
            @endif

            <h4 class="mt-3 text-primary fw-bold">Total: Rp {{ number_format($inbound->total_amount, 0, ',', '.') }}</h4>
            
            <div class="mt-3">
                <label>Foto Bukti:</label><br>
                @if($inbound->photo_proof)
                    <img src="{{ asset('storage/'.$inbound->photo_proof) }}" class="img-fluid rounded mt-2 border">
                @else
                    <span class="text-muted fst-italic">- Tidak ada foto -</span>
                @endif
            </div>

            <hr>

            @if($inbound->status == 'Requested')
                
                @can('approve_inbound')
                    <div class="alert alert-info small">
                        <i class="fas fa-user-shield"></i> <strong>Aksi Supervisor:</strong>
                    </div>

                    <form action="{{ route('inbounds.approve', $inbound->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin setujui? Stok akan bertambah.')">
                        @csrf
                        <button class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check"></i> Approve (Tambah Stok)
                        </button>
                    </form>

                    <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalRevisi">
                        <i class="fas fa-edit"></i> Minta Revisi
                    </button>
                @endcan

                @can('create_inbound')
                    @cannot('approve_inbound')
                        <a href="{{ route('inbounds.edit', $inbound->id) }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-pencil-alt"></i> Edit Data
                        </a>
                    @endcannot
                @endcan

                <form action="{{ route('inbounds.cancel', $inbound->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin batalkan?')">
                    @csrf
                    <button class="btn btn-danger w-100">
                        <i class="fas fa-times"></i> Cancel / Reject
                    </button>
                </form>

            @elseif($inbound->status == 'Revision')

                <div class="alert alert-info">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong><i class="fas fa-info-circle"></i> PERLU REVISI</strong><br>
                            Catatan Supervisor: <br>
                            <em class="text-dark">"{{ $inbound->note }}"</em>
                        </div>
                        
                        @can('approve_inbound')
                        <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalEditNote" title="Edit Catatan">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        @endcan
                    </div>
                </div>
                
                @can('create_inbound')
                <a href="{{ route('inbounds.edit', $inbound->id) }}" class="btn btn-primary w-100">
                    <i class="fas fa-pencil-alt"></i> Perbaiki Data Inbound
                </a>
                @endcan

            @endif

            <a href="{{ route('inbounds.index') }}" class="btn btn-secondary w-100 mt-2">Kembali</a>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-3">
            <h5>Detail Barang</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Warna/Size</th>
                            <th>Harga Beli (@pcs)</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inbound->details as $detail)
                        <tr>
                            <td>
                                <strong>{{ $detail->variant->product->name }}</strong>
                            </td>
                            <td>
                                {{ $detail->variant->color }} / {{ $detail->variant->size }}
                                <br><small class="text-muted">{{ $detail->variant->sku_variant }}</small>
                            </td>
                            <td>Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td class="fw-bold text-success">+ {{ $detail->qty }}</td>
                            <td class="fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">GRAND TOTAL</td>
                            <td class="fw-bold bg-light text-primary">Rp {{ number_format($inbound->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRevisi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">Form Permintaan Revisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inbounds.reject', $inbound->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Revisi / Catatan Perbaikan</label>
                        <textarea name="note" class="form-control" rows="4" placeholder="Contoh: Harga beli kemahalan, tolong cek lagi" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditNote" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Edit Catatan Revisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inbounds.update_note', $inbound->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Perbarui Catatan:</label>
                        <textarea name="note" class="form-control" rows="4" required>{{ $inbound->note }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection