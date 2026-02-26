<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            
            // Quantities (always in BASE UNIT!)
            $table->decimal('total_quantity', 10, 2)->default(0);
            $table->decimal('reserved_quantity', 10, 2)->default(0);
            $table->decimal('available_quantity', 10, 2)
                ->storedAs('total_quantity - reserved_quantity');
            
            // Costing (calculated from batches)
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('average_cost', 10, 2)->default(0);
            
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock');
    }
};