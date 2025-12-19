<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class PosCouponService
{
    /**
     * Validate and apply coupon
     */
    public static function applyCoupon($code, $subtotal, $userId = null)
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            throw new \Exception('Kode kupon tidak ditemukan');
        }

        if (!$coupon->isValid()) {
            throw new \Exception('Kupon tidak valid atau sudah kadaluarsa');
        }

        // Check user usage limit
        if ($userId && !$coupon->canBeUsedByUser($userId, $subtotal)) {
            throw new \Exception('Kupon tidak dapat digunakan. Cek syarat dan ketentuan.');
        }

        // Check minimum purchase
        if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
            throw new \Exception('Minimum pembelian: Rp' . number_format($coupon->min_purchase, 0, ',', '.'));
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'coupon' => $coupon,
            'discount' => $discount,
        ];
    }

    /**
     * Mark coupon as used
     */
    public static function markAsUsed($couponId, $userId, $transactionId)
    {
        DB::beginTransaction();
        try {
            // Increment usage count
            $coupon = Coupon::findOrFail($couponId);
            $coupon->increment('usage_count');

            // Record user usage
            if ($userId) {
                DB::table('user_coupons')->insert([
                    'user_id' => $userId,
                    'coupon_id' => $couponId,
                    'used_at' => now(),
                    'reference_type' => 'App\Models\PosTransaction',
                    'reference_id' => $transactionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
