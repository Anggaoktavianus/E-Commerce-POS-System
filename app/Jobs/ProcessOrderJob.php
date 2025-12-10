<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Services\MidtransService;

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
            Log::info('Processing order', ['order_id' => $this->order->id]);

            // Update inventory
            $this->updateInventory();

            // Send confirmation email
            if ($this->order->user) {
                SendEmailJob::dispatch(
                    $this->order->user->email,
                    'Order Confirmation #' . $this->order->order_number,
                    'emails.order.confirmation',
                    ['order' => $this->order]
                );
            }

            // Update order status
            $this->order->update(['status' => 'processing']);

            Log::info('Order processed successfully', ['order_id' => $this->order->id]);

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
        foreach ($this->order->items as $item) {
            // Update product inventory if applicable
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
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
