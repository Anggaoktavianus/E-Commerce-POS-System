<?php

namespace App\Services;

use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\PosShift;
use App\Models\PosPayment;
use App\Services\PosLoyaltyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosService
{
    /**
     * Create new POS transaction
     */
    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Validate shift is open
            $shift = PosShift::findOrFail($data['shift_id']);
            if (!$shift->isOpen()) {
                throw new \Exception('Shift is not open');
            }

            // Generate transaction number
            $transactionNumber = PosTransaction::generateTransactionNumber($data['outlet_id']);

            // Create transaction
            $transaction = PosTransaction::create([
                'transaction_number' => $transactionNumber,
                'outlet_id' => $data['outlet_id'],
                'shift_id' => $data['shift_id'],
                'user_id' => $data['user_id'],
                'customer_id' => $data['customer_id'] ?? null,
                'subtotal' => $data['subtotal'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total_amount' => $data['total_amount'],
                'payment_method' => $data['payment_method'],
                'payment_details' => $data['payment_details'] ?? null,
                'cash_received' => $data['cash_received'] ?? null,
                'change_amount' => $data['change_amount'] ?? null,
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
            ]);

            // Create transaction items and update inventory
            foreach ($data['items'] as $item) {
                $this->createTransactionItem($transaction, $item);
            }

            // Create payments if split payment
            if ($data['payment_method'] === 'split' && isset($data['payments'])) {
                foreach ($data['payments'] as $payment) {
                    PosPayment::create([
                        'transaction_id' => $transaction->id,
                        'payment_method' => $payment['method'],
                        'amount' => $payment['amount'],
                        'payment_details' => $payment['details'] ?? null,
                        'reference_number' => $payment['reference_number'] ?? null,
                    ]);
                }
            }

            // Apply coupon if exists (coupon_code is now passed from frontend)
            if (isset($data['coupon_code']) && $data['coupon_code']) {
                try {
                    // Recalculate subtotal after item discounts
                    $finalSubtotal = $transaction->subtotal;
                    
                    $couponResult = PosCouponService::applyCoupon(
                        $data['coupon_code'],
                        $finalSubtotal,
                        $data['customer_id'] ?? null
                    );

                    // Update discount
                    $transaction->discount_amount += $couponResult['discount'];
                    $transaction->total_amount -= $couponResult['discount'];
                    $transaction->save();

                    // Mark coupon as used
                    PosCouponService::markAsUsed(
                        $couponResult['coupon']->id,
                        $data['customer_id'] ?? null,
                        $transaction->id
                    );
                } catch (\Exception $e) {
                    Log::warning('Failed to apply coupon in POS transaction', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't throw - transaction continues without coupon
                }
            }

            // Apply loyalty points redemption if exists
            if (isset($data['loyalty_points']) && $data['loyalty_points'] > 0 && $transaction->customer_id) {
                try {
                    $loyaltyResult = PosLoyaltyService::redeemPoints(
                        $transaction->customer_id,
                        $data['loyalty_points'],
                        $transaction->id
                    );

                    // Update discount
                    $transaction->discount_amount += $loyaltyResult['discount'];
                    $transaction->total_amount -= $loyaltyResult['discount'];
                    $transaction->save();
                } catch (\Exception $e) {
                    Log::warning('Failed to redeem loyalty points in POS transaction', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't throw - transaction continues without loyalty points
                }
            }

            // Apply member discount if customer is verified member
            if ($transaction->customer_id && isset($data['apply_member_discount']) && $data['apply_member_discount']) {
                try {
                    $customer = \App\Models\User::find($transaction->customer_id);
                    if ($customer && $customer->is_verified) {
                        // Get member discount rate from settings (default 5%)
                        $memberDiscountRate = \App\Models\PosSetting::get(
                            $transaction->outlet_id,
                            'member_discount_rate',
                            5
                        ) / 100;
                        
                        $memberDiscount = $transaction->subtotal * $memberDiscountRate;
                        
                        // Update transaction
                        $transaction->discount_amount += $memberDiscount;
                        $transaction->total_amount -= $memberDiscount;
                        $transaction->save();
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to apply member discount', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Award loyalty points
            if ($transaction->customer_id) {
                try {
                    PosLoyaltyService::awardPoints($transaction);
                } catch (\Exception $e) {
                    Log::warning('Failed to award loyalty points', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update shift totals
            $this->updateShiftTotals($shift);

            return $transaction->load('items', 'customer', 'payments');
        });
    }

    /**
     * Create transaction item and update inventory
     */
    protected function createTransactionItem(PosTransaction $transaction, array $item)
    {
        $product = \App\Models\Product::findOrFail($item['product_id']);
        $outletId = $transaction->outlet_id;

        // Get current stock from outlet inventory
        $inventory = \App\Models\OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $item['product_id'])
            ->firstOrFail();

        $stockBefore = $inventory->stock;

        // Validate stock
        if ($inventory->stock < $item['quantity']) {
            throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$inventory->stock}, Requested: {$item['quantity']}");
        }

        // Update inventory using PosInventoryService
        $stockResult = PosInventoryService::decreaseStock(
            $outletId,
            $item['product_id'],
            $item['quantity'],
            $transaction->id,
            $transaction->transaction_number
        );

        $stockAfter = $stockResult['stock_after'];

        // Create transaction item
        $transactionItem = PosTransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $item['product_id'],
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'discount_amount' => $item['discount_amount'] ?? 0,
            'tax_amount' => $item['tax_amount'] ?? 0,
            'total_amount' => $item['total_amount'],
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ]);

        return $transactionItem;
    }

    /**
     * Update shift totals
     */
    protected function updateShiftTotals(PosShift $shift)
    {
        $shift->total_sales = $shift->transactions()
            ->where('status', 'completed')
            ->sum('total_amount');
        
        $shift->total_transactions = $shift->transactions()
            ->where('status', 'completed')
            ->count();
        
        $shift->save();
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction($transactionId, $userId, $reason)
    {
        return DB::transaction(function () use ($transactionId, $userId, $reason) {
            $transaction = PosTransaction::findOrFail($transactionId);

            if (!$transaction->canCancel()) {
                throw new \Exception('Transaction cannot be cancelled');
            }

            // Restore inventory for each item
            foreach ($transaction->items as $item) {
                PosInventoryService::restoreStock(
                    $transaction->outlet_id,
                    $item->product_id,
                    $item->quantity,
                    $transaction->id,
                    $transaction->transaction_number
                );
            }

            // Update transaction
            $transaction->status = 'cancelled';
            $transaction->cancelled_at = now();
            $transaction->cancelled_by = $userId;
            $transaction->cancel_reason = $reason;
            $transaction->save();

            // Update shift totals
            $this->updateShiftTotals($transaction->shift);

            return $transaction;
        });
    }

    /**
     * Refund transaction
     */
    public function refundTransaction($transactionId, $userId, $reason, $refundAmount = null)
    {
        return DB::transaction(function () use ($transactionId, $userId, $reason, $refundAmount) {
            $transaction = PosTransaction::findOrFail($transactionId);

            if ($transaction->status !== 'completed') {
                throw new \Exception('Hanya transaksi yang sudah completed yang bisa di-refund');
            }

            // Check if already refunded
            if ($transaction->status === 'refunded') {
                throw new \Exception('Transaksi sudah di-refund sebelumnya');
            }

            // Use provided refund amount or full amount
            $amountToRefund = $refundAmount ?? $transaction->total_amount;

            if ($amountToRefund > $transaction->total_amount) {
                throw new \Exception('Jumlah refund tidak boleh melebihi total transaksi');
            }

            // Restore inventory for each item (full refund only)
            if (!$refundAmount || $refundAmount >= $transaction->total_amount) {
                foreach ($transaction->items as $item) {
                    PosInventoryService::restoreStock(
                        $transaction->outlet_id,
                        $item->product_id,
                        $item->quantity,
                        $transaction->id,
                        $transaction->transaction_number
                    );
                }
            }

            // Update transaction status
            $transaction->status = 'refunded';
            $transaction->cancelled_at = now();
            $transaction->cancelled_by = $userId;
            $transaction->cancel_reason = $reason . ($refundAmount ? " (Partial refund: Rp " . number_format($refundAmount, 0, ',', '.') . ")" : ' (Full refund)');
            $transaction->save();

            // Update shift totals
            $this->updateShiftTotals($transaction->shift);

            return $transaction;
        });
    }
}
