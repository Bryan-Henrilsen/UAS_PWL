<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Inbound;
use App\Models\Outbound;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index() 
    {
        // 1. Hitung Total Varian Produk
        $totalVariants = \App\Models\ProductVariant::count();

        // 2. Hitung Transaksi Bulan Ini
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $inboundApprovedCount = Inbound::whereMonth('inbound_date', $currentMonth)
                                ->whereYear('inbound_date', $currentYear)
                                ->where('status', 'Approved')
                                ->count();
        
        $inboundRequestedCount = Inbound::whereMonth('inbound_date', $currentMonth)
                                ->whereYear('inbound_date', $currentYear)
                                ->where('status', 'Requested')
                                ->count();

        $outboundSentCount = Outbound::whereMonth('outbound_date', $currentMonth)
                                ->whereYear('outbound_date', $currentYear)
                                ->where('status', 'Sent') 
                                ->count();

        $outboundRequestedCount = Outbound::whereMonth('outbound_date', $currentMonth)
                                ->whereYear('outbound_date', $currentYear)
                                ->where('status', 'Requested') 
                                ->count();

        // 3. Super Admin bisa melihat total aset
        $totalAsset = 0;
        if(auth()->user()->can('view_financials')) {
            // Ambil semua varian, kalikan stok dengan harga, lalu jumlahkan
            $totalAsset = ProductVariant::all()->sum(function($variant) {
                return $variant->stock_qty * $variant->price;
            });
        }

        return view('dashboard', compact('totalVariants', 'inboundApprovedCount', 'outboundSentCount', 'inboundRequestedCount', 'outboundRequestedCount', 'totalAsset'));
    }
} 
