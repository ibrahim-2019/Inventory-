<?php

namespace App\Services;

use App\Models\User;
use App\Models\NotificationSetting;
use App\Models\NotificationLog;
use App\Models\Product;
use App\Models\ProductStockBatch;
use App\Models\ProductStock;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Exception;

class NotificationService
{
    /**
     * إرسال تنبيه نقص المخزون
     */
    public function sendLowStockAlert($productId, $warehouseId)
    {
        $product = Product::with('baseUnit', 'stock')->findOrFail($productId);
        $stock = $product->stock()->where('warehouse_id', $warehouseId)->first();
        
        if (!$stock || $stock->available_quantity > $product->alert_quantity) {
            return false; // المخزون كافي
        }
        
        $data = [
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'current_quantity' => $stock->available_quantity,
            'alert_quantity' => $product->alert_quantity,
            'unit' => $product->baseUnit->short_name,
            'warehouse_id' => $warehouseId,
        ];
        
        return $this->sendNotification(
            'low_stock',
            'تنبيه نقص مخزون',
            "المنتج {$product->name} وصل للحد الأدنى. المتاح: {$stock->available_quantity} {$product->baseUnit->short_name}",
            $data
        );
    }
    
    /**
     * إرسال تنبيه اقتراب انتهاء الصلاحية
     */
    public function sendExpiryAlert($batchId)
    {
        $batch = ProductStockBatch::with('product.baseUnit', 'warehouse')->findOrFail($batchId);
        
        if (!$batch->expiry_date || !$batch->isExpiringSoon($batch->product->expiry_alert_days)) {
            return false;
        }
        
        $daysRemaining = $batch->daysUntilExpiry();
        
        $data = [
            'product_name' => $batch->product->name,
            'product_sku' => $batch->product->sku,
            'batch_number' => $batch->batch_number,
            'quantity' => $batch->quantity_remaining,
            'unit' => $batch->product->baseUnit->short_name,
            'expiry_date' => $batch->expiry_date->format('Y-m-d'),
            'days_remaining' => $daysRemaining,
            'warehouse' => $batch->warehouse->name,
        ];
        
        return $this->sendNotification(
            'expiry_alert',
            'تنبيه اقتراب انتهاء الصلاحية',
            "المنتج {$batch->product->name} - Batch #{$batch->batch_number} سينتهي خلال {$daysRemaining} يوم",
            $data
        );
    }
    
    /**
     * إرسال تنبيه منتجات منتهية الصلاحية
     */
    public function sendExpiredProductsAlert()
    {
        $expiredBatches = ProductStockBatch::with('product.baseUnit', 'warehouse')
            ->expired()
            ->where('status', 'active')
            ->where('quantity_remaining', '>', 0)
            ->get();
        
        if ($expiredBatches->isEmpty()) {
            return false;
        }
        
        $data = [
            'expired_count' => $expiredBatches->count(),
            'batches' => $expiredBatches->map(function($batch) {
                return [
                    'product_name' => $batch->product->name,
                    'batch_number' => $batch->batch_number,
                    'quantity' => $batch->quantity_remaining,
                    'unit' => $batch->product->baseUnit->short_name,
                    'expiry_date' => $batch->expiry_date->format('Y-m-d'),
                    'warehouse' => $batch->warehouse->name,
                ];
            })->toArray(),
        ];
        
        return $this->sendNotification(
            'expired_products',
            'منتجات منتهية الصلاحية',
            "يوجد {$expiredBatches->count()} منتج منتهي الصلاحية يحتاج إلى معالجة",
            $data
        );
    }
    
    /**
     * إرسال تنبيه حركة مخزون
     */
    public function sendStockMovementNotification($movementId, $type = 'stock_in')
    {
        $movement = \App\Models\StockMovement::with('product', 'warehouse', 'creator')
            ->findOrFail($movementId);
        
        $title = match($type) {
            'stock_in' => 'إضافة مخزون',
            'stock_out' => 'خصم مخزون',
            'stock_transfer' => 'نقل مخزون',
            default => 'حركة مخزون',
        };
        
        $data = [
            'movement_id' => $movement->id,
            'movement_type' => $movement->movement_type,
            'product_name' => $movement->product->name,
            'quantity' => $movement->quantity,
            'warehouse' => $movement->warehouse->name,
            'created_by' => $movement->creator->name ?? 'System',
            'created_at' => $movement->created_at->format('Y-m-d H:i'),
        ];
        
        return $this->sendNotification(
            $type,
            $title,
            "{$title}: {$movement->product->name} - الكمية: {$movement->quantity}",
            $data
        );
    }
    
    /**
     * إرسال تقرير يومي
     */
    public function sendDailySummary()
    {
        $today = now()->toDateString();
        
        // إحصائيات اليوم
        $stockInToday = \App\Models\StockMovement::whereDate('created_at', $today)
            ->where('movement_type', 'in')
            ->count();
        
        $stockOutToday = \App\Models\StockMovement::whereDate('created_at', $today)
            ->where('movement_type', 'out')
            ->count();
        
        $lowStockProducts = ProductStock::whereHas('product', function($q) {
            $q->whereRaw('product_stock.available_quantity <= products.alert_quantity');
        })->count();
        
        $expiringSoon = ProductStockBatch::expiringSoon(7)
            ->where('status', 'active')
            ->count();
        
        $data = [
            'date' => $today,
            'stock_in_count' => $stockInToday,
            'stock_out_count' => $stockOutToday,
            'low_stock_count' => $lowStockProducts,
            'expiring_soon_count' => $expiringSoon,
        ];
        
        return $this->sendNotification(
            'daily_summary',
            'التقرير اليومي',
            "ملخص عمليات المخزون اليوم: {$stockInToday} إضافة، {$stockOutToday} خصم، {$lowStockProducts} منتج منخفض",
            $data
        );
    }
    
    /**
     * إرسال الإشعار عبر القنوات المختلفة
     */
    protected function sendNotification($type, $title, $message, $data = [])
    {
        // جلب المستخدمين المشتركين في هذا النوع من التنبيهات
        $settings = NotificationSetting::with('user')
            ->where('notification_type', $type)
            ->where('is_active', true)
            ->get();
        
        $results = [];
        
        foreach ($settings as $setting) {
            $channelsStatus = [];
            
            foreach ($setting->channels as $channel) {
                try {
                    $success = match($channel) {
                        'email' => $this->sendEmail($setting->user, $title, $message, $data),
                        'whatsapp' => $this->sendWhatsApp($setting->user, $message, $data),
                        'sms' => $this->sendSMS($setting->user, $message),
                        'in_app' => true, // نحفظ في الـ Database دايماً
                        default => false,
                    };
                    
                    $channelsStatus[$channel] = $success ? 'success' : 'failed';
                } catch (Exception $e) {
                    $channelsStatus[$channel] = 'failed';
                }
            }
            
            // حفظ الإشعار في قاعدة البيانات
            $log = NotificationLog::create([
                'user_id' => $setting->user_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'channels_sent' => $setting->channels,
                'status' => $channelsStatus,
                'is_read' => false,
            ]);
            
            $results[] = $log;
        }
        
        return $results;
    }
    
    /**
     * إرسال Email
     */
    protected function sendEmail($user, $title, $message, $data)
    {
        try {
            Mail::raw($message, function ($mail) use ($user, $title) {
                $mail->to($user->email)
                     ->subject($title);
            });
            
            return true;
        } catch (Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * إرسال WhatsApp (عبر Twilio أو API أخرى)
     */
    protected function sendWhatsApp($user, $message, $data)
    {
        try {
            // مثال باستخدام Twilio
            // يجب تثبيت: composer require twilio/sdk
            
            if (!$user->phone) {
                return false;
            }
            
            // هنا يتم التكامل مع WhatsApp API
            // هذا مثال افتراضي - يحتاج إعدادات حقيقية
            
            /*
            $twilio = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            
            $twilio->messages->create(
                "whatsapp:{$user->phone}",
                [
                    'from' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
                    'body' => $message
                ]
            );
            */
            
            // للتجربة نرجع true
            return true;
            
        } catch (Exception $e) {
            \Log::error('WhatsApp sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * إرسال SMS
     */
    protected function sendSMS($user, $message)
    {
        try {
            if (!$user->phone) {
                return false;
            }
            
            // هنا يتم التكامل مع SMS API
            // مثال: Twilio, Nexmo, إلخ
            
            return true;
            
        } catch (Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * تشغيل الفحص الدوري للتنبيهات
     */
    public function checkAndSendAlerts()
    {
        // 1. فحص المخزون المنخفض
        $lowStockProducts = ProductStock::with('product')
            ->whereHas('product', function($q) {
                $q->whereRaw('product_stock.available_quantity <= products.alert_quantity');
            })
            ->get();
        
        foreach ($lowStockProducts as $stock) {
            $this->sendLowStockAlert($stock->product_id, $stock->warehouse_id);
        }
        
        // 2. فحص المنتجات القريبة من الانتهاء
        $expiringBatches = ProductStockBatch::with('product')
            ->where('status', 'active')
            ->where('quantity_remaining', '>', 0)
            ->whereNotNull('expiry_date')
            ->get()
            ->filter(function($batch) {
                return $batch->isExpiringSoon($batch->product->expiry_alert_days ?? 30);
            });
        
        foreach ($expiringBatches as $batch) {
            $this->sendExpiryAlert($batch->id);
        }
        
        // 3. فحص المنتجات منتهية الصلاحية
        $this->sendExpiredProductsAlert();
        
        return [
            'low_stock_alerts' => $lowStockProducts->count(),
            'expiring_alerts' => $expiringBatches->count(),
        ];
    }
}