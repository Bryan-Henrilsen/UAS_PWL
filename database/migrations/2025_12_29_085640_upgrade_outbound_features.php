<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update Tabel Detail (Penambahan Diskon)
        Schema::table('outbound_details', function (Blueprint $table) {
            $table->decimal('discount_amout', 15, 2)->default(0)->after('unit_price');
        });

        // 2. Update Tabel Header (Pajak & Status Revisi)
        Schema::table('outbounds', function (Blueprint $table) {
            $table->decimal('tax_amount', 15, 2)->default(0)->after('total_amount');
            $table->decimal('grand_total', 15, 2)->default(0)->after('tax_amount');
            $table->text('note')->nullable()->after('photo_proof');
        });

        // 3. Mengubah Status ENUM (Menambah opsi 'Revision')
        DB::statement("ALTER TABLE outbounds MODIFY COLUMN status ENUM('Requested', 'Sent', 'Cancel', 'Revision') NOT NULL DEFAULT 'Requested'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback logic (Optional, hati-hati saat rollback ENUM)
        Schema::table('outbound_details', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
        });
        Schema::table('outbounds', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'grand_total', 'note']);
        });
        DB::statement("ALTER TABLE outbounds MODIFY COLUMN status ENUM('Requested', 'Sent', 'Cancel') NOT NULL DEFAULT 'Requested'");
    }
};
