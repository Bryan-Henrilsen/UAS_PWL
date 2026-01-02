@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card p-4 mb-4 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-primary">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-muted mb-0">
                        Anda login sebagai: 
                        <span class="badge bg-info text-dark">
                            {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'User') }}
                        </span>
                    </p>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-warehouse fa-3x text-gray-300 opacity-25"></i>
                </div>
            </div>
            
            @can('view_financials')
                @if(isset($totalAsset)) 
                <div class="alert alert-success mt-3 d-flex align-items-center">
                    <i class="fas fa-wallet fa-2x me-3"></i>
                    <div>
                        <small>Total Nilai Aset (Estimasi)</small>
                        <h4 class="fw-bold mb-0">Rp {{ number_format($totalAsset, 0, ',', '.') }}</h4>
                    </div>
                </div>
                @endif
            @endcan
        </div>

        <h5 class="mb-3 text-muted"><i class="fas fa-chart-line me-2"></i>Ringkasan Bulan Ini</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-primary text-white p-3 mb-3 h-100">
                    <div class="d-flex justify-content-between align-items-center h-100">
                        <div>
                            <h6 class="opacity-75">Total Varian Produk</h6>
                            <h2 class="fw-bold mb-0">{{ $totalVariants }}</h2>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white p-3 mb-3 h-100">
                    <div class="d-flex justify-content-between align-items-center h-100">
                        <div>
                            <h6 class="opacity-75">Inbound (Selesai)</h6>
                            <h2 class="fw-bold mb-0">{{ $inboundApprovedCount }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info text-white p-3 mb-3 h-100">
                    <div class="d-flex justify-content-between align-items-center h-100">
                        <div>
                            <h6 class="opacity-75">Outbound (Terkirim)</h6>
                            <h2 class="fw-bold mb-0">{{ $outboundSentCount }}</h2>
                        </div>
                        <i class="fas fa-truck fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        @if($inboundRequestedCount > 0 || $outboundRequestedCount > 0)
        <h5 class="mb-3 mt-4 text-warning"><i class="fas fa-exclamation-circle me-2"></i>Perlu Tindakan (Pending)</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-warning mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold">Request Barang Masuk</h6>
                            <h3 class="fw-bold text-warning mb-0">{{ $inboundRequestedCount }}</h3>
                            <small class="text-muted">Menunggu Approval</small>
                        </div>
                        <div class="bg-warning text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-arrow-down fa-lg"></i>
                        </div>
                    </div>
                    @canany(['approve_inbound', 'create_inbound'])
                    <div class="card-footer bg-warning bg-opacity-10 border-0 text-center">
                        <a href="{{ route('inbounds.index') }}" class="text-warning fw-bold text-decoration-none small stretched-link">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @endcanany
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-warning mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold">Request Barang Keluar</h6>
                            <h3 class="fw-bold text-warning mb-0">{{ $outboundRequestedCount }}</h3>
                            <small class="text-muted">Menunggu Approval</small>
                        </div>
                        <div class="bg-warning text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-truck-loading fa-lg"></i>
                        </div>
                    </div>
                    @canany(['approve_outbound', 'create_outbound'])
                    <div class="card-footer bg-warning bg-opacity-10 border-0 text-center">
                        <a href="{{ route('outbounds.index') }}" class="text-warning fw-bold text-decoration-none small stretched-link">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @endcanany
                </div>
            </div>
        </div>
        @else
            <div class="alert alert-light border mt-4 text-center text-muted">
                <i class="fas fa-check-double text-success me-2"></i> Tidak ada transaksi yang menunggu persetujuan (Pending) saat ini.
            </div>
        @endif
    </div>
</div>
@endsection