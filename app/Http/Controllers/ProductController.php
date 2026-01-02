<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. Menampilkan Daftar Produk
    public function index()
    {
        // Load produk beserta variannya (Eager Loading) biar lebih cepat
        $products = Product::with('variants')->latest()->get();
        return view('product.index', compact('products'));
    }

    // 2. Menampilan Form Tambah
    public function create()
    {
        return view('product.create');
    }

    // 3. Proses Menyimpan ke Database
    public function store(Request $request)
    {
        // 1. Melakukan Validasi
        $request->validate([
            'name' => 'required|string|max:100',
            'sku_base' => 'required|string|unique:products,sku_base|max:50',
            'photo_main' => 'nullable|image|max:2048', // 2MB

            // Validasi Array Varians
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction(); // Memulai Transaksi Database

            // 2. Simpan Produk Induk
            $product = new Product();
            $product->name = $request->name;
            $product->sku_base = strtoupper($request->sku_base);

            // Upload Foto Utama Jika Memang Ada
            if ($request->hasFile('photo_main')) {
                $path = $request->file('photo_main')->store('products', 'public');
                $product->photo_main = $path;
            }
            $product->save();

            // 3. Simpan Varian (Looping)
            // Diperlukan $index untuk mengambil file yang sesuai dari request
            foreach ($request->variants as $index => $variantData) {
                // Auto Generate SKU Variant: BASE-COLOR-SIZE
                $skuVariant = strtoupper($product->sku_base . '-' . $variantData['color'] . '-' . $variantData['size']);

                // Mengecek duplikasi SKU Varian (supaya tidak double)
                if(ProductVariant::where('sku_variant', $skuVariant)->exists()) {
                    throw new \Exception("SKU Varian $skuVariant sudah terdaftar di database!");
                }

                // Upload Foto Varian Jika Ada
                $variantPhotoPath = null;
                if($request->hasFile("variants.{$index}.photo")) {
                    $variantPhotoPath = $request->file("variants.{$index}.photo")->store('variants', 'public');
                }

                $product->variants()->create([
                    'size' => strtoupper($variantData['size']),
                    'color' => strtoupper($variantData['color']),
                    'price' => $variantData['price'],
                    'sku_variant' => $skuVariant,
                    'stock_qty' => 0, // Penambahan via Inbound
                    'photo_variant' => $variantPhotoPath // Simpan path foto 
                ]);
            }

            DB::commit(); // Simpan Permanen
            return redirect()->route('products.index')->with('success', 'Product dan Varian berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback(); // Batalkan semua jika terjadi error
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id) {
        $product = Product::with('variants')->findOrFail($id);
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, $id) 
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'sku_base' => 'required|string|unique:products,sku_base|max:50',
            'photo_main' => 'nullable|image|max:2048',

            // Validasi Array Varians
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $product->name = $request->name;
            $product->sku_base = $request->sku_base;
            
            // Jika ada penggantian foto
            if ($request->hasFile('photo_main')) {
                $product->photo_main = $request->file('photo_main')->store('products', 'public');
            }
            $product->save();

            // Update Varian (Looping)
            // Catatan: Di fitur ini kita hanya update harga/info varian yg ada.
            // Menambah/Menghapus varian saat edit lebih kompleks logicnya (bisa merusak history stok).
            // Jadi di sini kita asumsikan user mengupdate harga/size/color dari varian yg sudah ada.
            
            foreach ($request->variants as $variantId => $data) {
                $variant = ProductVariant::find($variantId);
                if($variant) {
                    $variant->update([
                        'size' => strtoupper($data['size']),
                        'color' => strtoupper($data['color']),
                        'price' => $data['price'],
                        // Update foto varian jika ada
                        'photo_variant' => $request->hasFile("variants.$variantId.photo") 
                                            ? $request->file("variants.$variantId.photo")->store('variants', 'public') 
                                            : $variant->photo_variant
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // 6. Proses Soft Delete (Non-Aktifkan)
    public function destroy($id)
    {
        $product = Product::with('variants')->findOrFail($id);

        // Cek Total Stok
        $totalStock = $product->variants->sum('stock_qty');

        if ($totalStock > 0) {
            return back()->with('error', 'Gagal menghapus! Produk masih memiliki sisa stok ' . $totalStock . ' pcs.');
        }

        // Soft Delete
        $product->update(['is_active' => 0]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dinonaktifkan.');
    }
}
