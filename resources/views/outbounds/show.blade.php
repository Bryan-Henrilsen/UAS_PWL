@extends('layouts.master')
@section('title', 'Detail Outbound')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card p-3 mb-3">
            <h5>Info Outbound</h5>
            <hr>
            <p><strong>Tanggal:</strong> {{ $outbound->outbound_date->format('d M Y') }}</p>
            <p><strong>Dibuat Oleh:</strong> {{ $outbound->requester->name }}</p>
            <p><strong>Tujuan:</strong> {{ $outbound->delivery_data ?? '-' }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ $outbound->status == 'Sent' ? 'success' : ($outbound->status == 'Requested' ? 'warning' : ($outbound->status == 'Revision' ? 'info' : 'danger')) }}">
                    {{ $outbound->status }}
                </span>
            </p>
            
            @if($outbound->status == 'Sent')
                <p><strong>Disetujui Oleh:</strong> {{ $outbound->approver->name ?? '-' }}</p>
            @endif
            
            <h3 class="mt-3 text-primary fw-bold">Rp {{ number_format($outbound->grand_total, 0, ',', '.') }}</h3>
            <small class="text-muted">Termasuk Pajak {{ $outbound->tax_rate }}%</small>
            
            <div class="mt-3">
                <label>Foto Bukti:</label><br>
                @if($outbound->photo_proof)
                    <img src="{{ asset('storage/'.$outbound->photo_proof) }}" class="img-fluid rounded mt-2 border">
                @else
                    <span class="text-muted fst-italic">- Tidak ada foto -</span>
                @endif
            </div>

            <hr>

            @if($outbound->status == 'Requested')
                
                @can('approve_outbound')
                    <div class="alert alert-info small">
                        <i class="fas fa-user-shield"></i> <strong>Aksi Supervisor:</strong>
                    </div>

                    <form action="{{ route('outbounds.approve', $outbound->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin setujui? Stok akan berkurang.')">
                        @csrf
                        <button class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check"></i> Approve (Kirim)
                        </button>
                    </form>

                    <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalRevisi">
                        <i class="fas fa-edit"></i> Minta Revisi
                    </button>
                @endcan

                <form action="{{ route('outbounds.cancel', $outbound->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin batalkan transaksi ini?')">
                    @csrf
                    <button class="btn btn-danger w-100">
                        <i class="fas fa-times"></i> Cancel / Tolak
                    </button>
                </form>

            @elseif($outbound->status == 'Revision')
                
                <div class="alert alert-info">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong><i class="fas fa-info-circle"></i> PERLU REVISI</strong><br>
                            Catatan Supervisor: <br>
                            <em class="text-dark">"{{ $outbound->note }}"</em>
                        </div>
                        
                        @can('approve_outbound')
                        <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalEditNote" title="Edit Catatan">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        @endcan
                    </div>
                </div>
                
                @can('create_outbound')
                <a href="{{ route('outbounds.edit', $outbound->id) }}" class="btn btn-primary w-100">
                    <i class="fas fa-pencil-alt"></i> Perbaiki Data Barang
                </a>
                @endcan

            @endif

            <a href="{{ route('outbounds.index') }}" class="btn btn-secondary w-100 mt-2">Kembali</a>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-3">
            <h5>Detail Barang</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Harga (@pcs)</th>
                            <th>Disc</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outbound->details as $detail)
                        <tr>
                            <td>
                                <strong>{{ $detail->variant->product->name }}</strong><br>
                                <small>{{ $detail->variant->color }} / {{ $detail->variant->size }}</small>
                            </td>
                            <td>Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td>
                                @if($detail->discount_percent > 0)
                                    <span class="text-danger">{{ $detail->discount_percent }}%</span><br>
                                    <small class="text-muted">-{{ number_format($detail->discount_amount, 0, ',', '.') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="fw-bold">{{ $detail->qty }}</td>
                            <td class="fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Total Barang</td>
                            <td class="fw-bold">Rp {{ number_format($outbound->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Pajak ({{ $outbound->tax_rate }}%)</td>
                            <td class="fw-bold text-danger">+ Rp {{ number_format($outbound->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td colspan="4" class="text-end fw-bold">GRAND TOTAL</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($outbound->grand_total, 0, ',', '.') }}</td>
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
            <form action="{{ route('outbounds.reject', $outbound->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Revisi / Catatan Perbaikan</label>
                        <textarea name="note" class="form-control" rows="4" placeholder="Contoh: Kemeja A, diskonnya terlalu tinggi" required></textarea>
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
            <form action="{{ route('outbounds.update_note', $outbound->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Perbarui Catatan:</label>
                        <textarea name="note" class="form-control" rows="4" required>{{ $outbound->note }}</textarea>
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