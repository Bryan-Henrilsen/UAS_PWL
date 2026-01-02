@extends('layouts.master')
@section('title', 'Data Produk')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Daftar Produk</h4>
        @can('manage_products')
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Produk
        </a>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th width="10%">Foto</th>
                    <th>Nama Produk</th>
                    <th>SKU Base</th>
                    <th>Status</th>
                    <th>Varian (Size - Color - Stok)</th>
                    @can('manage_products')
                    <th width="15%">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ $product->is_active ? '' : 'table-secondary text-muted' }}">
                    <td>
                        @if($product->photo_main)
                            <img src="{{ asset('storage/'.$product->photo_main) }}" width="60" class="rounded border">
                        @else
                            <span class="text-muted small">No Image</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if(!$product->is_active)
                            <br><small class="text-danger fst-italic">(Tidak Aktif)</small>
                        @endif
                    </td>
                    <td>{{ $product->sku_base }}</td>
                    <td>
                        @if($product->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                        @endif
                    </td>
                    <td>
                        <ul class="mb-0 ps-3 small">
                            @foreach($product->variants as $variant)
                            <li>
                                {{ $variant->color }} - {{ $variant->size }}: 
                                <strong class="{{ $variant->stock_qty > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $variant->stock_qty }}
                                </strong> pcs
                            </li>
                            @endforeach
                        </ul>
                    </td>
                    
                    @can('manage_products')
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        @if($product->is_active)
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menonaktifkan produk ini? Pastikan stok 0.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" title="Non-aktifkan">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection