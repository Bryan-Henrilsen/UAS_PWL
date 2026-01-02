<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    // 1. Menampilkan riwayat Stock Opname
    public function index()
    {
        $stockOpnames = StockOpname::with('requester')->latest()->get();
        return view('stock_opnames.index', compact('stockOpnames'));
    }

    // 2. Form input stock opname (Pake teknik Blind Count)
    public function create() 
    {
        // Mengirim data produk, tapi ketika diview tidak ditampilkan stoknya untuk menjalankan teknik blind count
        $variants = ProductVariant::with('product')->get()->map(function($v) {
            $v->fullname = $v->product->name . ' - ' . $v->color . ' - ' . $v->size;
            return $v;
        });

        return view('stock_opnames.create', compact('variants'));
    }

    // 3. Membuat proses simpan hasil hitungan (snapshot system stock)
    public function store(Request $request)
    {
        $request->validate([
            'so_date' => 'required|date',
            'photo_proof' => 'nullable|image|max:2048',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty_actual' => 'required|integer|min:0', // Hasil hitung fisik
        ]);

        try {
            DB::beginTransaction();

            // Simpan Bagian Header SO
            $so = new StockOpname();
            $so->so_date = $request->so_date;
            $so->user_id = Auth::id(); // Staff Inbound/Outbound
            $so->status = 'Requested';

            if($request->hasFile('photo_proof')) {
                $so->photo_proof = $request->file('photo_proof')->store('stock_opnames', 'public');
            }
            $so->save();

            // Menyimpan detail & snapshot stock sistem
            foreach ($request->items as $item) {
                // Mengambil data stok sistem real time
                $variant = ProductVariant::find($item['variant_id']);
                $currentSystemStock = $variant->stock_qty;

                $actualStock = $item['qty_actual'];

                // Rumus: data fisik - data komputer
                $diff = $actualStock - $currentSystemStock;
                
                $so->details()->create([
                    'variant_id' => $item['variant_id'],
                    'qty_system' => $currentSystemStock, // staff tidak boleh tau
                    'qty_actual' => $actualStock, // diinput oleh staff
                    'qty_diff' => $diff,
                    'reason' => $item['reason'] ?? null
                ]);
            }

            DB::commit();
            return redirect()->route('stock_opnames.index')->with('success', 'Hasil Stock Opname berhasil disimpan. Menunggu review Supervisor');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    // 4. Detail & Approval 
    public function show($id)
    {
        $stockOpname = StockOpname::with(['details.variant.product', 'requester'])->findOrFail($id);
        return view('stock_opnames.show', compact('stockOpname'));
    }

    // 5. Proses Approval (update stok master data)
    public function approve($id) 
    {
        try {
            DB::beginTransaction();

            $so = StockOpname::with('details')->findorFail($id);

            if($so->status != 'Requested') {
                return back()->with('error', 'Hanya status requested yang bisa di approve.');
            }

            // Update Status Header
            $so->status = 'Approved';
            $so->approved_by = Auth::id();
            $so->approved_at = now();
            $so->save();

            // Penyesuain stok data master
            foreach ($so->details as $detail) {
                $variant = ProductVariant::find($detail['variant_id']);
                
                // Stok data master akan dipaksa mengikuti stok fisik (qty_actual)
                $variant->stock_qty = $detail['qty_actual'];
                $variant->save();
            }

            DB::commit();
            return back()->with('success', 'Stock opname berhasil disetujui. Stok Data Master telah disesuaikan dengan data stok fisik.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    // 6. Cancel Approval
    public function cancel($id) 
    {
        $so = StockOpname::findOrFail($id);
        if ($so->status != 'Requested') return back()->with('error', 'Hanya Stock Opname dengan status Requested yang bisa dibatalkan.');

        $so->status = 'Cancel';
        $so->save();
        return back()->with('success', 'Stock Opname dibatalkan. Tidak ada perubahan pada stok data master');
    } 

    // 7. Edit
    public function edit($id)
    {
        $stockOpname = StockOpname::with('details')->findOrFail($id);

        if($stockOpname->status != 'Requested') {
                return back()->with('error', 'Stock Opname yang sudah selesai tidak bisa diedit.');
            }
        
        $variants = ProductVariant::with('product')->get()->map(function($v) {
            $v->fullname = $v->product->name . ' - ' . $v->color . ' - ' . $v->size;
            return $v;
        });

        return view('stock_opnames.edit', compact('stockOpname', 'variants'));
    }
    
    // 8. Update
    public function update(Request $request, $id)
    {
        $so = StockOpname::findOrFail($id);

        if($so->status != 'Requested') {
            return back()->with('error', 'Tidak bisa edit data yang sudah diproses.');
        }

        try {
            DB::beginTransaction();

            // 1. Update Header
            $so->so_date = $request->so_date;
            $so->user_id = Auth::id(); // Staff Inbound/Outbound
            if ($request->hasFile('photo_proof')) {
                $so->photo_proof = $request->file('photo_proof')->store('stock_opnames', 'public');
            }
            $so->save();

            // 2. Hapus Detail Lama (Reset)
            $so->details()->delete();

            // 3. Masukkan Detail Baru
            foreach ($request->items as $item) {
                $variant = ProductVariant::find($item['variant_id']);
                $currentSystemStock = $variant->stock_qty; // Snapshot ulang stok saat ini
                $actualStock = $item['qty_actual'];
                $diff = $actualStock - $currentSystemStock;

                $so->details()->create([
                    'variant_id' => $item['variant_id'],
                    'qty_system' => $currentSystemStock,
                    'qty_actual' => $actualStock,
                    'qty_diff' => $diff,
                    'reason' => $item['reason'] ?? null
                ]);
            }

            DB::commit();
            return redirect()->route('stock_opnames.index')->with('success', 'Perbaikan Stock Opname berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }
}
