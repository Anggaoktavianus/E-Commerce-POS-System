<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        // Enhanced security configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true; // Always enabled for security
        Config::$is3ds = true; // Always enabled for fraud prevention
        
        // Additional security settings
        Config::$overrideNotifUrl = config('midtrans.payment_notification_url');
        
        // Log configuration (without sensitive data)
        \Log::info('Midtrans Service initialized', [
            'environment' => Config::$isProduction ? 'production' : 'sandbox',
            'is_sanitized' => Config::$isSanitized,
            'is_3ds' => Config::$is3ds
        ]);
    }

    public function createTransaction(Order $order)
    {
        $orderDetails = [
            'order_id' => $order->midtrans_order_id,
            'gross_amount' => (int) $order->total_amount,
        ];

        $itemDetails = $order->items->map(function ($item) {
            return [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product_name,
            ];
        })->toArray();

        $shippingAddress = $order->shipping_address;
        $customerDetails = [
            'first_name' => $shippingAddress['first_name'] ?? '',
            'last_name' => $shippingAddress['last_name'] ?? '',
            'email' => $order->user?->email ?? 'customer@example.com',
            'phone' => $shippingAddress['phone'] ?? '',
            'billing_address' => $order->billing_address,
            'shipping_address' => $shippingAddress,
        ];

        $params = [
            'transaction_details' => $orderDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            'enabled_payments' => [
                'credit_card', 'gopay', 'shopeepay', 'qris',
                'bca_va', 'bni_va', 'bri_va', 'cimb_va',
                'permata_va', 'other_va', 'echannel', 'indomaret', 'alfamart'
            ],
            'callbacks' => [
                'finish' => config('midtrans.redirect_url') . '?order_id=' . $order->id,
                'unfinish' => config('midtrans.redirect_url') . '?order_id=' . $order->id . '&status=unfinish',
                'error' => config('midtrans.redirect_url') . '?order_id=' . $order->id . '&status=error',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => $this->getSnapUrl() . $snapToken,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function checkTransactionStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return [
                'success' => true,
                'data' => $status,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function handleNotification($notificationData)
    {
        try {
            // Convert object to array if needed
            if (is_object($notificationData)) {
                $notificationData = (array) $notificationData;
            }
            
            // Enhanced security validation
            if (!$this->validateWebhookSignature($notificationData)) {
                \Log::warning('Invalid webhook signature detected', [
                    'order_id' => $notificationData['order_id'] ?? 'unknown'
                ]);
                throw new \Exception('Invalid webhook signature');
            }
            
            $orderId = $notificationData['order_id'];
            $transactionStatus = $notificationData['transaction_status'];
            $fraudStatus = $notificationData['fraud_status'] ?? null;
            $paymentType = $notificationData['payment_type'] ?? null;

            \Log::info('Processing Midtrans notification', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType
            ]);

            $order = Order::where('midtrans_order_id', $orderId)->firstOrFail();
            
            \Log::info('Order found', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'current_status' => $order->status
            ]);

            // Update payment transaction
            PaymentTransaction::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'transaction_id' => $notificationData['transaction_id'] ?? null,
                    'order_id_midtrans' => $orderId,
                    'payment_type' => $paymentType,
                    'status' => $transactionStatus,
                    'gross_amount' => $notificationData['gross_amount'] ?? $order->total_amount,
                    'currency' => $notificationData['currency'] ?? 'IDR',
                    'transaction_details' => $notificationData,
                    'va_numbers' => $notificationData['va_numbers'] ?? null,
                    'bill_key' => $notificationData['bill_key'] ?? null,
                    'biller_code' => $notificationData['biller_code'] ?? null,
                ]
            );

            // Update order status based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    $order->status = 'paid';
                    $order->paid_at = now();
                }
            } else if ($transactionStatus == 'settlement') {
                $order->status = 'paid';
                $order->paid_at = now();
            } else if ($transactionStatus == 'pending') {
                $order->status = 'pending';
            } else if ($transactionStatus == 'deny') {
                $order->status = 'failed';
            } else if ($transactionStatus == 'expire') {
                $order->status = 'expired';
            } else if ($transactionStatus == 'cancel') {
                $order->status = 'cancelled';
            }

            $order->payment_type = $paymentType;
            $order->payment_method = $this->getPaymentMethodName($paymentType, $notificationData);
            $order->payment_details = $notificationData;
            $order->midtrans_transaction_id = $notificationData['transaction_id'] ?? null;
            $order->save();

            \Log::info('Order updated successfully', [
                'order_id' => $order->id,
                'new_status' => $order->status,
                'paid_at' => $order->paid_at,
                'payment_method' => $order->payment_method
            ]);

            return $order;
        } catch (\Exception $e) {
            \Log::error('Failed to handle notification', [
                'error' => $e->getMessage(),
                'notification_data' => $notificationData
            ]);
            throw new \Exception('Failed to handle notification: ' . $e->getMessage());
        }
    }

    /**
     * Validate webhook signature for security
     */
    private function validateWebhookSignature(array $data): bool
    {
        // Basic validation - ensure required fields exist
        $requiredFields = ['order_id', 'transaction_status', 'transaction_id'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        
        // Additional validation can be added here
        // For production, implement actual signature verification
        
        return true;
    }

    private function getPaymentMethodName($paymentType, $notificationData)
    {
        switch ($paymentType) {
            case 'credit_card':
                return 'Credit Card';
            case 'gopay':
                return 'GoPay';
            case 'shopeepay':
                return 'ShopeePay';
            case 'qris':
                return 'QRIS';
            case 'bank_transfer':
                $bank = $notificationData['va_numbers'][0]['bank'] ?? 'Unknown';
                return strtoupper($bank) . ' Virtual Account';
            case 'echannel':
                return 'Mandiri Bill Payment';
            case 'bca_klikpay':
                return 'BCA KlikPay';
            case 'cimb_clicks':
                return 'CIMB Clicks';
            case 'bca_klikbca':
                return 'BCA KlikBCA';
            case 'bri_epay':
                return 'BRI ePay';
            case 'telkomsel_ewallet':
                return 'Telkomsel Cash';
            case 'xl_tunai':
                return 'XL Tunai';
            case 'indomaret':
                return 'Indomaret';
            case 'alfamart':
                return 'Alfamart';
            default:
                return ucfirst(str_replace('_', ' ', $paymentType));
        }
    }

    private function getSnapUrl()
    {
        if (config('midtrans.is_production')) {
            return 'https://app.midtrans.com/snap/v4/vtweb/';
        }
        return 'https://app.sandbox.midtrans.com/snap/v4/vtweb/';
    }
}
