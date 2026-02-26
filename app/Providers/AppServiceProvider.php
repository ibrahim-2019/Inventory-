<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register Services
        $this->app->singleton(\App\Services\InventoryService::class);
        $this->app->singleton(\App\Services\UnitConversionService::class);
        $this->app->singleton(\App\Services\NotificationService::class);
        $this->app->singleton(\App\Services\ReportService::class);
        $this->app->singleton(\App\Services\QRCodeService::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}