@extends('layouts.master')
@section('title', 'Edit Produk')

@section('content')
<form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <div class="row">
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Informasi Produk</h5>
                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                        {{ $product->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>

                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>
                
                <div class="mb-3">
                    <label>SKU Base (Kode Induk)</label>
                    <input type="text" name="sku_base" class="form-control bg-light" value="{{ $product->sku_base }}" readonly>
                    <small class="text-muted">SKU Base tidak dapat diubah.</small>
                </div>
                
                <div class="mb-3">
                    <label>Ganti Foto Utama (Opsional)</label>
                    <input type="file" name="photo_main" class="form-control" accept="image/*">
                    
                    @if($product->photo_main)
                        <div class="mt-2 text-center border p-2 rounded bg-light">
                            <small class="d-block mb-1 fw-bold">Foto Saat Ini:</small>
                            <img src="{{ asset('storage/'.$product->photo_main) }}" class="img-fluid rounded" style="max-height: 150px;">
                        </div>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="fas fa-info-circle"></i> <strong>Catatan:</strong> Stok hanya bisa diubah melalui menu Inbound (Masuk) atau Outbound (Keluar). Di sini Anda hanya bisa mengubah info varian.
                </div>

                <h5 class="mb-3">Edit Daftar Varian</h5>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Size</th>
                                <th width="20%">Color</th>
                                <th width="25%">Harga (Rp)</th>
                                <th width="10%">Stok</th>
                                <th width="30%">Foto Varian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variants as $variant)
                            <tr>
                                <td>
                                    <input type="text" name="variants[{{ $variant->id }}][size]" 
                                            class="form-control form-control-sm" value="{{ $variant->size }}" required>
                                </td>
                                <td>
                                    <input type="text" name="variants[{{ $variant->id }}][color]" 
                                            class="form-control form-control-sm" value="{{ $variant->color }}" required>
                                </td>
                                <td>
                                    <input type="number" name="variants[{{ $variant->id }}][price]" 
                                            class="form-control form-control-sm" value="{{ $variant->price }}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm bg-light text-center" 
                                            value="{{ $variant->stock_qty }}" readonly>
                                </td>
                                <td>
                                    <input type="file" name="variants[{{ $variant->id }}][photo]" 
                                            class="form-control form-control-sm" accept="image/*">
                                    
                                    @if($variant->photo_variant)
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/'.$variant->photo_variant) }}" target="_blank" class="small text-decoration-none">
                                                <i class="fas fa-image"></i> Lihat Foto
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection