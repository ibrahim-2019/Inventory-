<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            
            // Movement Type
            $table->enum('movement_type', [
                'in',
                'out',
                'transfer',
                'adjustment',
                'damaged',
                'returned',
                'expired'
            ]);
            
            // Quantity (always in BASE UNIT!)
            $table->decimal('quantity', 10, 2);
            
            // Costing (calculated from batches)
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->decimal('average_unit_cost', 10, 2)->nullable();
            
            // Reference
            $table->string('reference_type')->nullable();
            $table->bigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();
            
            // Details
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            
            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes
            $table->index('movement_type');
            $table->index(['product_id', 'warehouse_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};