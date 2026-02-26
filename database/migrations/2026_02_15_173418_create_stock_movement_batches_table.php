<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movement_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_movement_id')->constrained('stock_movements')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('product_stock_batches')->onDelete('cascade');
            
            // Quantity used from this batch (in BASE UNIT!)
            $table->decimal('quantity', 10, 2);
            
            // Cost from this batch
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes
            $table->index('stock_movement_id');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movement_batches');
    }
};