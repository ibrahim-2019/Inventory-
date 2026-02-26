<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Relations
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('base_unit_id')->constrained('units');
            
            // Pricing (optional for reports)
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            
            // Stock Settings
            $table->boolean('track_batches')->default(true);
            $table->boolean('has_expiry_date')->default(false);
            $table->enum('withdrawal_strategy', ['fifo', 'fefo', 'manual'])->default('fifo');
            $table->integer('alert_quantity')->default(10); // Low stock alert
            $table->integer('expiry_alert_days')->default(30); // Alert before expiry
            $table->boolean('auto_block_expired')->default(true);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};