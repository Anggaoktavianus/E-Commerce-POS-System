<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileCouponController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $availableCoupons = Coupon::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
            })
            ->where(function($query) {
                $query->whereNull('usage_limit')
                      ->orWhereColumn('usage_count', '<', 'usage_limit');
            })
            ->orderByDesc('created_at')
            ->get()
            ->filter(function($coupon) {
                return $coupon->isValid();
            });
        
        // Get user's used coupons
        $usedCoupons = [];
        if ($user) {
            $usedCoupons = DB::table('user_coupons')
                ->join('coupons', 'user_coupons.coupon_id', '=', 'coupons.id')
                ->where('user_coupons.user_id', $user->id)
                ->whereNotNull('user_coupons.used_at')
                ->select('coupons.*', 'user_coupons.used_at')
                ->orderByDesc('user_coupons.used_at')
                ->get();
        }
        
        return view('mobile.coupons', compact('availableCoupons', 'usedCoupons'));
    }
    
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'nullable|numeric|min:0'
        ]);
        
        $coupon = Coupon::where('code', strtoupper($request->code))->first();
        
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Kode kupon tidak ditemukan'
            ], 404);
        }
        
        $subtotal = $request->subtotal ?? 0;
        $user = Auth::user();
        
        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon tidak valid atau sudah kadaluarsa'
            ], 422);
        }
        
        if ($user && !$coupon->canBeUsedByUser($user->id, $subtotal)) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon tidak dapat digunakan. Cek syarat dan ketentuan.'
            ], 422);
        }
        
        if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum pembelian: Rp' . number_format($coupon->min_purchase, 0, ',', '.')
            ], 422);
        }
        
        $discount = $coupon->calculateDiscount($subtotal);
        
        return response()->json([
            'success' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount' => $discount
            ],
            'message' => 'Kupon berhasil diterapkan'
        ]);
    }
}
