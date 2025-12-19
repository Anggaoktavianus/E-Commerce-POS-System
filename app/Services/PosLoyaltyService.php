<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\PosTransaction;
use App\Models\User;

class PosLoyaltyService
{
    /**
     * Award loyalty points from POS transaction
     */
    public static function awardPoints(PosTransaction $transaction)
    {
        // Only award if customer is linked
        if (!$transaction->customer_id) {
            return null;
        }

        $customer = User::find($transaction->customer_id);
        if (!$customer) {
            return null;
        }

        // Calculate points (1% of total amount, or customize)
        // 1 point per Rp100 = 1% of total
        $pointsEarned = floor($transaction->total_amount * 0.01);

        if ($pointsEarned <= 0) {
            return null;
        }

        // Create loyalty point record
        $loyaltyPoint = LoyaltyPoint::create([
            'user_id' => $transaction->customer_id,
            'type' => 'earn',
            'points' => $pointsEarned,
            'description' => "Poin dari transaksi POS #{$transaction->transaction_number}",
            'reference_type' => 'App\Models\PosTransaction',
            'reference_id' => $transaction->id,
            'expires_at' => now()->addYear(), // Points expire in 1 year
        ]);

        return $loyaltyPoint;
    }

    /**
     * Redeem loyalty points in POS transaction
     */
    public static function redeemPoints($userId, $points, $transactionId)
    {
        $balance = LoyaltyPoint::getUserBalance($userId);

        if ($points > $balance) {
            throw new \Exception("Poin tidak mencukupi. Saldo: {$balance}");
        }

        // Create redeem record
        $loyaltyPoint = LoyaltyPoint::create([
            'user_id' => $userId,
            'type' => 'redeem',
            'points' => $points,
            'description' => "Redeem poin untuk transaksi POS",
            'reference_type' => 'App\Models\PosTransaction',
            'reference_id' => $transactionId,
        ]);

        // Calculate discount (1 point = Rp1, or customize)
        $discount = $points;

        return [
            'loyalty_point' => $loyaltyPoint,
            'discount' => $discount,
            'remaining_points' => $balance - $points,
        ];
    }

    /**
     * Get customer loyalty balance
     */
    public static function getBalance($userId)
    {
        return LoyaltyPoint::getUserBalance($userId);
    }
}
