@extends('layouts.master')
@section('title', 'Pusat Laporan')

@section('content')
<div class="row">
    <div class="col-12">
        <h4 class="mb-3"><i class="fas fa-file-alt me-2"></i>Pusat Laporan & Ekspor</h4>
        
        <div class="card p-3 shadow-sm border-0">
            <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button">
                        <i class="fas fa-boxes me-1"></i> Stok & Aset
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="inbound-tab" data-bs-toggle="tab" data-bs-target="#inbound" type="button">
                        <i class="fas fa-arrow-down me-1"></i> Barang Masuk (Inbound)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="outbound-tab" data-bs-toggle="tab" data-bs-target="#outbound" type="button">
                        <i class="fas fa-truck me-1"></i> Barang Keluar (Outbound)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="reportTabsContent">
                
                <div class="tab-pane fade show active" id="stock">
                    <div class="alert alert-info">
                        Laporan ini menampilkan <strong>Snapshot Stok Saat Ini</strong> beserta estimasi nilai aset berdasarkan harga master produk.
                    </div>
                    <form action="{{ route('reports.stock') }}" method="GET" target="_blank">
                        <button type="submit" name="view" value="html" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Lihat Laporan (Web)
                        </button>
                        <button type="submit" name="export_pdf" value="true" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        </form>
                </div>

                <div class="tab-pane fade" id="inbound">
                    <div class="alert alert-primary">
                        Rekap transaksi barang masuk yang berstatus <strong>Approved</strong>.
                    </div>
                    <form action="{{ route('reports.inbound') }}" method="GET" class="row align-items-end" target="_blank">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <button type="submit" name="export_pdf" value="true" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="outbound">
                    <div class="alert alert-warning">
                        Rekap transaksi barang keluar yang berstatus <strong>Sent</strong>.
                    </div>
                    <form action="{{ route('reports.outbound') }}" method="GET" class="row align-items-end" target="_blank">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <button type="submit" name="export_pdf" value="true" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection