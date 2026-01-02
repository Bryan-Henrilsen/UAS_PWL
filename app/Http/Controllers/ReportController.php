<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inbound;
use App\Models\Outbound;
use App\Models\StockOpname;
use App\Models\ProductVariant;
use Barryvdh\DomPDF\Facade\Pdf; // Facade PDF
// use Maatwebsite\Excel\Facades\Excel; // Facade Excel
use App\Exports\InboundExport; 
use App\Exports\OutboundExport; 
use App\Exports\StockExport; 

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // --- 1. LAPORAN STOK & ASET (SNAPSHOT SAAT INI) ---
    public function stock(Request $request)
    {
        // Ambil semua varian beserta produk induknya
        $stocks = ProductVariant::with('product')->get();
        
        // Hitung estimasi aset (Stok * Harga)
        $totalAsset = $stocks->sum(function($item) {
            return $item->stock_qty * $item->price;
        });

        if($request->has('export_pdf')) {
            $pdf = Pdf::loadView('reports.pdf.stock', compact('stocks', 'totalAsset'));
            return $pdf->download('Laporan_Stok_Aset_'.date('Y-m-d').'.pdf');
        }

        return view('reports.view_stock', compact('stocks', 'totalAsset'));
    }

    // --- 2. LAPORAN INBOUND (PERIODE) ---
    public function inbound(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $inbounds = Inbound::with(['requester', 'details.variant.product'])
                    ->whereBetween('inbound_date', [$startDate, $endDate])
                    ->where('status', 'Approved') // Hanya yang sudah masuk stok
                    ->latest()
                    ->get();

        $totalNominal = $inbounds->sum('total_amount');

        if($request->has('export_pdf')) {
            $pdf = Pdf::loadView('reports.pdf.inbound', compact('inbounds', 'startDate', 'endDate', 'totalNominal'));
            return $pdf->setPaper('a4', 'landscape')->download('Laporan_Inbound.pdf');
        }

        return view('reports.view_inbound', compact('inbounds', 'startDate', 'endDate', 'totalNominal'));
    }

    // --- 3. LAPORAN OUTBOUND (PERIODE) ---
    public function outbound(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $outbounds = Outbound::with(['requester', 'details.variant.product'])
                    ->whereBetween('outbound_date', [$startDate, $endDate])
                    ->where('status', 'Sent') // Hanya yang sudah dikirim
                    ->latest()
                    ->get();

        $totalGrand = $outbounds->sum('grand_total');

        if($request->has('export_pdf')) {
            $pdf = Pdf::loadView('reports.pdf.outbound', compact('outbounds', 'startDate', 'endDate', 'totalGrand'));
            return $pdf->setPaper('a4', 'landscape')->download('Laporan_Outbound.pdf');
        }

        return view('reports.view_outbound', compact('outbounds', 'startDate', 'endDate', 'totalGrand'));
    }
}