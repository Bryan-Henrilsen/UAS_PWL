<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Header Outbound: Total Pendapatan Transaksi/Penjualan
        Schema::table('outbounds', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('status');
        });

        Schema::table('outbound_details', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->default(0)->after('qty');
            $table->decimal('subtotal', 15, 2)->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outbounds', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });

        Schema::table('outbound_details', function (Blueprint $table) {
            $table->dropColumn('unit_price', 'subtotal');
        });
    }
};
