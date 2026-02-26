<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('base_unit_id')->constrained('units');
            
            // 1 unit = conversion_factor × base_unit
            // Example: 1 Carton = 24 Pieces
            $table->decimal('conversion_factor', 10, 3);
            
            // Usage flags
            $table->boolean('is_purchase_unit')->default(false);
            $table->boolean('is_sale_unit')->default(false);
            
            // Optional
            $table->string('barcode')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            
            $table->timestamps();
            
            $table->unique(['product_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_unit_conversions');
    }
};