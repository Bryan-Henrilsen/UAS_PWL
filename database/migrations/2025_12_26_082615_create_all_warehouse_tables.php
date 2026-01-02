<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 50);
            $table->timestamps(); 
        });

        // 2. Users Table (Modifikasi standar Laravel)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique(); // Login pakai username sesuai request
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->rememberToken();
            $table->timestamps();
        });

        // 3. Products (Parent)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('sku_base', 50); // Contoh: KMJ-FLN01
            $table->string('photo_main')->nullable();
            $table->timestamps();
        });

        // 4. Product Variants (Child - SKUs)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku_variant', 50)->unique(); // Contoh: KMJ-FLN01-RED-L
            $table->string('size', 10);
            $table->string('color', 20);
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock_qty')->default(0); // Stok berjalan
            $table->string('photo_variant')->nullable();
            $table->timestamps();
        });

        // 5. Inbounds (Header)
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->date('inbound_date');
            $table->string('photo_proof')->nullable();
            $table->enum('status', ['Requested', 'Received', 'Approved', 'Cancel'])->default('Requested');
            $table->foreignId('user_id')->constrained('users'); // Yang request (Staff Gudang)
            $table->unsignedBigInteger('approved_by')->nullable(); // Supervisor
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            // Relasi manual untuk approved_by ke users
            $table->foreign('approved_by')->references('id')->on('users');
        });

        // 6. Inbound Details
        Schema::create('inbound_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_id')->constrained('inbounds')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants');
            $table->integer('qty');
            $table->timestamps();
        });

        // 7. Outbounds (Header)
        Schema::create('outbounds', function (Blueprint $table) {
            $table->id();
            $table->date('outbound_date');
            $table->text('delivery_data')->nullable(); // Alamat/Ekspedisi
            $table->string('photo_proof')->nullable();
            $table->enum('status', ['Requested', 'Sent', 'Cancel'])->default('Requested');
            $table->foreignId('user_id')->constrained('users'); // Staff Gudang
            $table->unsignedBigInteger('approved_by')->nullable(); // Supervisor
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users');
        });

        // 8. Outbound Details
        Schema::create('outbound_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_id')->constrained('outbounds')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants');
            $table->integer('qty');
            $table->timestamps();
        });

        // 9. Stock Opnames (Header)
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->date('so_date');
            $table->string('photo_proof')->nullable();
            $table->enum('status', ['Requested', 'Approved', 'Cancel'])->default('Requested');
            $table->foreignId('user_id')->constrained('users'); // Staff yang hitung
            $table->unsignedBigInteger('approved_by')->nullable(); // Supervisor
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users');
        });

        // 10. Stock Opname Details (Logika Blind Count)
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('so_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants');
            
            $table->integer('qty_system'); // Snapshot stok sistem saat SO dibuat
            $table->integer('qty_actual'); // Hasil hitungan fisik
            
            // Kolom ini bisa dihitung otomatis (actual - system), tapi boleh disimpan
            // Jika positif = Adjustment Plus, Negatif = Adjustment Minus
            $table->integer('qty_diff')->default(0); 
            
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Urutan drop table harus dibalik karena foreign key
        Schema::dropIfExists('stock_opname_details');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('outbound_details');
        Schema::dropIfExists('outbounds');
        Schema::dropIfExists('inbound_details');
        Schema::dropIfExists('inbounds');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
