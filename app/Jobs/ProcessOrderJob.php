<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Services\MidtransService;
use App\Services\StockMovementService;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [5, 15, 30];
    public $timeout = 60;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        try {
            // Refresh order untuk mendapatkan status terbaru
            $this->order->refresh();
            
            Log::info('Processing order', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'current_status' => $this->order->status
            ]);

            // Cek apakah order sudah diproses sebelumnya (untuk menghindari double processing)
            if ($this->order->processed_at) {
                Log::info('Order already processed, skipping job', [
                    'order_id' => $this->order->id,
                    'processed_at' => $this->order->processed_at,
                    'status' => $this->order->status
                ]);
                return;
            }

            // Pastikan order sudah paid sebelum mengurangi stok
            if ($this->order->status !== 'paid') {
                Log::warning('Order is not paid, cannot process', [
                    'order_id' => $this->order->id,
                    'status' => $this->order->status
                ]);
                return;
            }

            // Double-check processed_at again after refresh (prevent race condition)
            $this->order->refresh();
            if ($this->order->processed_at) {
                Log::info('Order already processed by another process, skipping inventory update', [
                    'order_id' => $this->order->id,
                    'processed_at' => $this->order->processed_at
                ]);
                // Still send email if not sent yet
                if ($this->order->user) {
                    SendEmailJob::dispatch(
                        $this->order->user->email,
                        'Order Confirmation #' . $this->order->order_number,
                        'emails.order.confirmation',
                        ['order' => $this->order]
                    );
                }
                return;
            }

            // Update inventory (kurangi stok) - use DB transaction
            DB::beginTransaction();
            try {
                $this->updateInventory();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            // Send confirmation email
            if ($this->order->user) {
                SendEmailJob::dispatch(
                    $this->order->user->email,
                    'Order Confirmation #' . $this->order->order_number,
                    'emails.order.confirmation',
                    ['order' => $this->order]
                );
            }

            // Update order status dan processed_at (use DB transaction to prevent race condition)
            $orderUpdated = false;
            DB::transaction(function() use (&$orderUpdated) {
                // Lock the order row
                $lockedOrder = Order::where('id', $this->order->id)
                    ->whereNull('processed_at')
                    ->lockForUpdate()
                    ->first();
                
                if ($lockedOrder) {
                    $lockedOrder->update([
                        'status' => 'processing',
                        'processed_at' => now()
                    ]);
                    $orderUpdated = true;
                } else {
                    Log::info('Order already processed by another process during transaction', [
                        'order_id' => $this->order->id
                    ]);
                    $orderUpdated = false;
                }
            });
            
            // Refresh order after transaction
            $this->order->refresh();

            Log::info('Order processed successfully', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process order', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    private function updateInventory()
    {
        // Reload order with items and products relationship
        $this->order->load(['items.product']);
        
        foreach ($this->order->items as $item) {
            // Try to get product by ID if relationship fails
            $product = $item->product;
            if (!$product && $item->product_id) {
                $product = \App\Models\Product::find($item->product_id);
            }
            
            // Update product inventory if applicable
            if ($product) {
                $oldStock = $product->stock_qty ?? 0;
                $product->decrement('stock_qty', $item->quantity);
                $product->refresh();
                $newStock = $product->stock_qty ?? 0;
                
                // Log stock movement
                StockMovementService::logDecrease(
                    $product,
                    $item->quantity,
                    Order::class,
                    $this->order->id,
                    $this->order->order_number,
                    "Stock keluar untuk order #{$this->order->order_number}"
                );
                
                Log::info('Stock updated', [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name ?? $product->name,
                    'quantity' => $item->quantity,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock
                ]);
            } else {
                Log::warning('Product not found for order item', [
                    'order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name ?? 'N/A'
                ]);
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Order processing job failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);

        // Update order status to failed
        $this->order->update([
            'status' => 'failed',
            'cancel_reason' => 'Processing failed: ' . $exception->getMessage()
        ]);
    }
}
