<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->enum('notification_type', [
                'low_stock',
                'expiry_alert',
                'expired_products',
                'stock_in',
                'stock_out',
                'stock_transfer',
                'daily_summary',
                'weekly_summary'
            ]);
            
            // Channels: email, whatsapp, sms, in_app
            $table->json('channels');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'notification_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};