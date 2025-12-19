<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\DeliveryTracking;
use App\Models\ShippingMethod;

class BackfillDeliveryTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracking:backfill {--order-id= : Specific order ID to backfill}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill delivery tracking for existing orders with instant delivery';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->option('order-id');
        
        if ($orderId) {
            $orders = Order::where('id', $orderId)->get();
        } else {
            // Get all orders with instant delivery that don't have tracking
            $orders = Order::whereNotNull('shipping_method_id')
                ->whereDoesntHave('deliveryTracking')
                ->get()
                ->filter(function($order) {
                    $shippingMethod = ShippingMethod::find($order->shipping_method_id);
                    return $shippingMethod && $shippingMethod->type === 'instant';
                });
        }
        
        if ($orders->isEmpty()) {
            $this->info('No orders found to backfill.');
            return;
        }
        
        $this->info("Found {$orders->count()} order(s) to backfill.");
        
        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();
        
        $created = 0;
        $skipped = 0;
        
        foreach ($orders as $order) {
            try {
                $shippingMethod = ShippingMethod::find($order->shipping_method_id);
                
                // Check if instant delivery (by type or by ID = 1 as fallback)
                $isInstant = false;
                if ($shippingMethod) {
                    $isInstant = $shippingMethod->type === 'instant';
                } elseif ($order->shipping_method_id == 1) {
                    // Fallback: ID 1 is instant delivery
                    $isInstant = true;
                }
                
                if ($isInstant) {
                    DeliveryTracking::firstOrCreate(
                        ['order_id' => $order->id],
                        [
                            'status' => DeliveryTracking::STATUS_PENDING
                        ]
                    );
                    $created++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("\nError processing order {$order->id}: " . $e->getMessage());
                $skipped++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Completed: {$created} tracking records created, {$skipped} skipped.");
    }
}
