<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\InboundDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class InboundController extends Controller
{
    // 1. Daftar Riwayat Inbound
    public function index()
    {
        $inbounds = Inbound::with(['requester', 'details'])->latest()->get();
        return view('Inbounds.index', compact('inbounds'));
    }

    // 2. Form Input Inbound
    public function create()
    {
        // Mengambil data varian untuk dipilih di dropdown
        // Format nama: "nama - warna - ukuran'
        $variants = ProductVariant::with('product')->get()->map(function($variant) {
            $variant->fullname = $variant->product->name . ' - ' . $variant->color . ' - ' . $variant->size;
            return $variant;
        });

        return view('inbounds.create', compact('variants'));
    }

    // 3. Proses Simpan (Status Requested)
    public function store(Request $request)
    {
        $request->validate([
            'inbound_date' => 'required|date',
            'photo_proof' => 'required|image|max:2048',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Simpan Header Inbound
            $inbound = new Inbound();
            $inbound->inbound_date = $request->inbound_date;
            $inbound->user_id = Auth::id(); // yang login saat ini
            $inbound->status = 'Requested';
            $inbound->total_amount = 0;

            if($request->hasFile('photo_proof')) {
                $inbound->photo_proof = $request->file('photo_proof')->store('inbounds', 'public');
            }
            $inbound->save();

            // Simpan Detail Items Inbound & hitung total
            $grandTotal = 0;

            foreach($request->items as $item) {
                $subtotal = $item['qty'] * $item['unit_price'];
                $grandTotal += $subtotal; 

                $inbound->details()->create([
                    'variant_id' => $item['variant_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal
                ]);
            }

            // Update Total Amount di header inbound
            $inbound->update(['total_amount' => $grandTotal]);

            DB::commit();
            return redirect()->route('inbounds.index')->with('success', 'Request Inbound berhasil dibuat. Total: Rp. ' . number_format($grandTotal) . '. Sedang menunggu persetujuan.');

        } catch(\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    // 4. Detail & Halaman Approval
    public function show($id) {
        $inbound = Inbound::with(['details.variant.product', 'requester'])->findOrFail($id);
        return view('inbounds.show', compact('inbound'));
    }

    // 5. Proses Persetujuan (Penambahan Stok)
    public function approve($id) {
        // Validasi dimana hanya bisa role supervisor/admin yang bisa (diskip dlu)

        try {
            DB::beginTransaction();

            $inbound = Inbound::with('details')->findOrFail($id);

            if($inbound->status != 'Requested') {
                return back()->with('error', 'Inbound ini sudah diproses sebelumnya.');
            }

            // Update Header Inbound
            $inbound->status = 'Approved';
            $inbound->approved_by = Auth::id();
            $inbound->approved_at = now();
            $inbound->save();

            // Update Stock Product
            foreach($inbound->details as $detail) {
                $variant = ProductVariant::find($detail->variant_id);
                $variant->stock_qty += $detail->qty;
                $variant->save();
            }

            DB::commit();
            return back()->with('success', 'Inbound disetujui. Stok telah bertambah.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    // 6. Proses Cancel Inbound ketika status requested
    public function cancel($id) 
    {
        $inbound = Inbound::findOrFail($id);
        if($inbound->status != 'Requested') {
            return back()->with('error', 'Hanya Inbound dengan status Requested yang bisa dicancel!');
        }

        $inbound->status = 'Cancel';
        $inbound->save();

        return back()->with('success', 'Inbound berhasil dibatalkan.');
    }

    // 7. Pengajuan Revisi oleh supervisor
    public function reject(Request $request, $id)
    {
        $request->validate(['note' => 'required|string']);
        
        $inbound = Inbound::findOrFail($id);
        
        // Cuma bisa revisi kalau status masih Requested
        if($inbound->status != 'Requested') {
            return back()->with('error', 'Hanya status Requested yang bisa direvisi.');
        }

        $inbound->update([
            'status' => 'Revision',
            'note' => $request->note
        ]);

        return back()->with('success', 'Status diubah menjadi Revisi. Catatan telah dikirim ke Staff.');
    }

    // 8. Update catatan/note revisi oleh supervisor
    public function updateNote(Request $request, $id)
    {
        $request->validate(['note' => 'required|string']);
        $inbound = Inbound::findOrFail($id);
        $inbound->update(['note' => $request->note]);
        return back()->with('success', 'Catatan revisi diperbarui.');
    }

    // 9. Edit
    public function edit($id)
    {
        $inbound = Inbound::with('details')->findOrFail($id);
        
        if($inbound->status != 'Requested' && $inbound->status != 'Revision') {
            return back()->with('error', 'Hanya Inbound yang statusnya requested/revision yang bisa diedit.');
        }

        $variants = ProductVariant::with('product')->get()->map(function($v) {
            $v->fullname = $v->product->name . ' - ' . $v->color . ' - ' . $v->size;
            return $v;
        });

        return view('inbounds.edit', compact('inbound', 'variants'));
    }

    // 10. Update ketika Revisi maupun Requested
    public function update(Request $request, $id) {
        $request->validate([
            'inbound_date' => 'required|date',
            'photo_proof' => 'nullable|image|max:2048',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $inbound = Inbound::findOrFail($id);

            if(!in_array($inbound->status, ['Requested', 'Revision'])) {
                return back()->with('error', 'Data tidak bisa diedit lagi.');
            }

            // Simpan Header Inbound
            $inbound->inbound_date = $request->inbound_date;
            $inbound->user_id = Auth::id(); // yang login saat ini
            $inbound->status = 'Requested';

            if($request->hasFile('photo_proof')) {
                $inbound->photo_proof = $request->file('photo_proof')->store('inbounds', 'public');
            }
            $inbound->save();

            // Hapus detail lama (Wipe Clean)
            $inbound->details()->delete();

            // Simpan Detail Items Inbound & hitung total
            $grandTotal = 0;

            foreach($request->items as $item) {
                $subtotal = $item['qty'] * $item['unit_price'];
                $grandTotal += $subtotal; 

                $inbound->details()->create([
                    'variant_id' => $item['variant_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal
                ]);
            }

            $inbound->update(['total_amount' => $grandTotal]);

            DB::commit();
            return redirect()->route('inbounds.index')->with('success', 'Data Outbound berhasil diperbaiki dan diajukan kembali. Total: Rp. ' . number_format($grandTotal) . '. Sedang menunggu persetujuan.');
        } catch(\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
