<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add processed_at column if it doesn't exist
            if (!Schema::hasColumn('orders', 'processed_at')) {
                // Check if paid_at exists to determine position
                if (Schema::hasColumn('orders', 'paid_at')) {
                    $table->timestamp('processed_at')->nullable()->after('paid_at');
                } else {
                    $table->timestamp('processed_at')->nullable();
                }
            }
            
            // Add shipped_at column if it doesn't exist
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                if (Schema::hasColumn('orders', 'processed_at')) {
                    $table->timestamp('shipped_at')->nullable()->after('processed_at');
                } else {
                    $table->timestamp('shipped_at')->nullable();
                }
            }
            
            // Add delivered_at column if it doesn't exist
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                if (Schema::hasColumn('orders', 'shipped_at')) {
                    $table->timestamp('delivered_at')->nullable()->after('shipped_at');
                } else {
                    $table->timestamp('delivered_at')->nullable();
                }
            }
            
            // Add cancelled_at column if it doesn't exist
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                if (Schema::hasColumn('orders', 'delivered_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
                } else {
                    $table->timestamp('cancelled_at')->nullable();
                }
            }
            
            // Add cancel_reason column if it doesn't exist
            if (!Schema::hasColumn('orders', 'cancel_reason')) {
                if (Schema::hasColumn('orders', 'cancelled_at')) {
                    $table->text('cancel_reason')->nullable()->after('cancelled_at');
                } else {
                    $table->text('cancel_reason')->nullable();
                }
            }
            
            // Add tracking_number column if it doesn't exist
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                if (Schema::hasColumn('orders', 'cancel_reason')) {
                    $table->string('tracking_number')->nullable()->after('cancel_reason');
                } else {
                    $table->string('tracking_number')->nullable();
                }
            }
        });
        
        // Update status enum to include new statuses if needed
        try {
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'failed', 'cancelled', 'expired') DEFAULT 'pending'");
        } catch (\Exception $e) {
            // If enum modification fails, it might already have the values or table doesn't exist
            \Log::warning('Failed to modify status enum: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
            if (Schema::hasColumn('orders', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
            if (Schema::hasColumn('orders', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            if (Schema::hasColumn('orders', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }
            if (Schema::hasColumn('orders', 'shipped_at')) {
                $table->dropColumn('shipped_at');
            }
            if (Schema::hasColumn('orders', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
        });
    }
};
