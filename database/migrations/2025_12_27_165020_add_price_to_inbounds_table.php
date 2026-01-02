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
        // Menambah kolom total di inbound header
        Schema::table('inbounds', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('status');
        });

        // Menambah kolom harga di inbound detail
        Schema::table('inbound_details', function (Blueprint $table){
            $table->decimal('unit_price', 15, 2)->default(0)->after('qty');
            $table->decimal('subtotal', 15, 2)->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });

        Schema::table('inbound_details', function (Blueprint $table) {
            $table->dropColumn('unit_price', 'subtotal');
        });
    }
};
