<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            /*$table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();*/
            
            // Batch Information
            $table->string('batch_number')->unique();
            $table->string('supplier_name')->nullable();
            
            // Quantities (always in BASE UNIT!)
            $table->decimal('quantity_in', 10, 2); // Original quantity
            $table->decimal('quantity_remaining', 10, 2); // Current remaining
            $table->decimal('quantity_used', 10, 2)->default(0); // Used/sold
            
            // Costing (in BASE UNIT!)
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            
            // Dates
            $table->date('purchase_date');
            $table->date('expiry_date')->nullable();
            $table->date('manufacture_date')->nullable();
            
            // Status
            $table->enum('status', ['active', 'exhausted', 'expired', 'damaged'])->default('active');
            
            // Notes & Tracking
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'warehouse_id']);
            $table->index('batch_number');
            $table->index('purchase_date');
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_batches');
    }
};