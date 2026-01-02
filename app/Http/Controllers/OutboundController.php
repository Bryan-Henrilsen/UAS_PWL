<?php

namespace App\Http\Controllers;

use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutboundController extends Controller
{
    public function index()
    {
        $outbounds = Outbound::with(['requester', 'details'])->latest()->get();
        return view('outbounds.index', compact('outbounds'));
    }

    public function create()
    {
        // Mengirim data produk dan stock saat ini berserta harga jualnya
        $variants = ProductVariant::with('product')->get()->map(function($v) {
            $v->fullname = $v->product->name . ' - ' . $v->color . ' - ' . $v->size . ' (Stok: '.$v->stock_qty.')';
            return $v;
        });
        return view('outbounds.create', compact('variants'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'outbound_date' => 'required|date',
            'delivery_data' => 'nullable|string',
            'photo_proof' => 'nullable|image|max:2048',
            'tax_global' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'tax_rate' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Validasi Stok sebelum disimpan
            foreach ($request->items as $item) {
                $variant = ProductVariant::find($item['variant_id']);
                if($variant->stock_qty < $item['qty']) {
                    throw new \Exception("Stock tidak cukup untuk produk: " . $variant->product->name . " ($variant->color-$variant->size). Sisa stok: " . $variant->stock_qty);
                }
            }

            // 2. Simpan Header Outbound
            $outbound = new Outbound();
            $outbound->outbound_date = $request->outbound_date;
            $outbound->delivery_data = $request->delivery_data;
            $outbound->user_id = Auth::id();
            $outbound->status = 'Requested';
            $outbound->total_amount = 0;
            $outbound->tax_amount = 0;
            $outbound->grand_total = 0;

            if ($request->hasFile('photo_proof')) {
                $outbound->photo_proof = $request->file('photo_proof')->store('outbounds', 'public');
            }
            $outbound->save();

            // 3. Simpan Detail Outbound
            $totalAmount = 0; // Total barang sebelum pajak

            foreach ($request->items as $item) {
                $price = $item['unit_price'];
                $qty = $item['qty'];
                $discPercent = $item['discount_percent'] ?? 0;
                
                // Konversi Persen ke Rupiah
                $discountNomimal = $price * ($discPercent / 100);

                $subtotal = ($price - $discountNomimal) * $qty;
                
                $totalAmount += $subtotal; 

                $outbound->details()->create([
                    'variant_id' => $item['variant_id'],
                    'qty' => $qty,
                    'unit_price' => $price,
                    'discount_percent' => $discPercent,
                    'discount_amount' => $discountNomimal,
                    'subtotal' => $subtotal
                ]);
            }

            // Hitung pajak & grand total
            $taxRate = $request->tax_rate ?? 0;
            $taxNominal = $totalAmount * ($taxRate / 100);
            $grandTotal = $totalAmount + $taxNominal;

            $outbound->update([
                'total_amount' => $totalAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxNominal,
                'grand_total' => $grandTotal,
            ]);

            DB::commit();
            return redirect()->route('outbounds.index')->with('success', 'Request Outbound berhasil dibuat. Menunggu approval.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id) 
    {
        $outbound = Outbound::with(['details.variant.product', 'requester'])->findOrFail($id);
        return view('outbounds.show', compact('outbound'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['note' => 'required|string']);

        $outbound = Outbound::findOrFail($id);
        if($outbound->status != 'Requested') abort(404);

        $outbound->update([
            'status' => 'Revision',
            'note' => $request->note
        ]);

        return back()->with('success', 'Status outbound berhasil diubah menjadi revisi.');
    }

    public function edit($id) {
        $outbound = Outbound::with('details')->findOrFail($id);

        if($outbound->status != 'Revision' && $outbound->status != 'Requested') {
            return redirect()->route('outbounds.index')->with('error', 'Hanya transaski dengan status Revisi/Requested yang bisa diedit');
        }

        // Load data varian seperti method create
        $variants = ProductVariant::with('product')->get()->map(function($v) {
            $v->fullname = $v->product->name . ' - ' . $v->color . ' - ' . $v->size . ' (Stok: '.$v->stock_qty.')';
            return $v;
        });

        return view('outbounds.edit', compact('outbound', 'variants'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'outbound_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'tax_rate' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $outbound = Outbound::findOrFail($id);

            // Cek ulang ketersediaan stok
            foreach ($request->items as $item) {
                $variant = ProductVariant::find($item['variant_id']);
                // Karena ketika status revisi, stok tidak terpotong, maka kita harus cek stok murni database
                if($variant->stock_qty < $item['qty']) {
                    throw new \Exception("Stok {$variant->product->name} tidak cukup untuk jumlah baru!");
                }
            }

            // 1. Update Header
            $outbound->outbound_date = $request->outbound_date;
            $outbound->delivery_data = $request->delivery_data;
            $outbound->user_id = Auth::id();
            $outbound->status = 'Requested';

            // Jika ada penggantian foto
            if ($request->hasFile('photo_proof')) {
                $outbound->photo_proof = $request->file('photo_proof')->store('outbounds', 'public');
            }
            $outbound->save();

            // 2. Hapus detail lama (Wipe Clean)
            $outbound->details()->delete();

            // 3. Masukkan detail baru (sama seperti logic store)
            $totalAmount = 0;

            foreach ($request->items as $item) {
                $price = $item['unit_price'];
                $qty = $item['qty'];
                $discPercent = $item['discount_percent'] ?? 0;

                $discountNominal = $price * ($discPercent / 100);
                $subtotal = ($price - $discountNominal) * $qty;
                
                $totalAmount += $subtotal;

                $outbound->details()->create([
                    'variant_id' => $item['variant_id'],
                    'qty' => $qty,
                    'unit_price' => $price,
                    'discount_percent' => $discPercent,
                    'discount_amount' => $discountNominal,
                    'subtotal' => $subtotal
                ]);
            }

            // 4. Hitung ulang grand total
            $taxRate = $request->tax_rate ?? 0;
            $taxNominal = $totalAmount * ($taxRate / 100);
            $grandTotal = $totalAmount + $taxNominal;

            $outbound->update([
                'total_amount' => $totalAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxNominal,
                'grand_total' => $grandTotal,
            ]);

            DB::commit();
            return redirect()->route('outbounds.index')->with('success', 'Data Outbound berhasil diperbaiki dan diajukan kembali.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve($id) 
    {
        try {
            DB::beginTransaction();
            $outbound = Outbound::with('details')->findOrFail($id);

            if($outbound->status != 'Requested') {
                return back()->with('error', 'Outbound telah disetujui sebelumnya');
            }

            // Validasi Stok lagi (Takutnya saat jeda waktu request -> approve, stok sudah diambil orang lain)
            foreach ($outbound->details as $detail) {
                $variant = ProductVariant::find($detail->variant_id);
                if($variant->stock_qty < $detail->qty) {
                    throw new \Exception("Gagal Approve. Stok " . $variant->product->name . " tidak cukup saat ini. Tersisa: " . $detail->qty);
                }
            }

            // Update Status
            $outbound->status = 'Sent';
            $outbound->approved_by = Auth::id();
            $outbound->approved_at = now();
            $outbound->save();

            // Update Pengurangan Stok
            foreach ($outbound->details as $detail) {
                $variant = ProductVariant::find($detail->variant_id);
                $variant->stock_qty -= $detail->qty;
                $variant->save();
            }

            DB::commit();
            return back()->with('success', 'Outbound disetujui. Stok telah dikurangi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id) 
    {
        $outbound = Outbound::findOrFail($id);
        if ($outbound->status != 'Requested') return back()->with('error', 'Outbound tidak bisa cancel');

        $outbound->status = 'Cancel';
        $outbound->save();
        return back()->with('success', 'Outbound berhasil dibatalkan');
    }

    public function updateNote(Request $request, $id) 
    {
        $request->validate(['note' => 'required|string']);

        $outbound = Outbound::findOrFail($id);

        if($outbound->status != 'Revision') {
            return back()->with('error', 'Hanya bisa edit catatan saat status masih Revisi');
        }

        $outbound->update(['note' => $request->note]);

        return back()->with('success', 'Catatan revisi berhasil diperbarui');
    }
}
