<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileLoyaltyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('mobile.login');
        }
        
        $balance = LoyaltyPoint::getUserBalance($user->id);
        
        $transactions = LoyaltyPoint::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        
        // Calculate points that will expire soon (within 30 days)
        $expiringSoon = LoyaltyPoint::where('user_id', $user->id)
            ->where('type', 'earn')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays(30))
            ->sum('points');
        
        return view('mobile.loyalty', compact('balance', 'transactions', 'expiringSoon'));
    }
    
    public function redeem(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }
        
        $request->validate([
            'points' => 'required|integer|min:100'
        ]);
        
        $balance = LoyaltyPoint::getUserBalance($user->id);
        
        if ($request->points > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Poin tidak mencukupi'
            ], 422);
        }
        
        // Create redeem transaction
        LoyaltyPoint::create([
            'user_id' => $user->id,
            'type' => 'redeem',
            'points' => $request->points,
            'description' => 'Redeem poin untuk discount'
        ]);
        
        // Calculate discount (1 point = Rp1, or customize)
        $discount = $request->points; // 1 point = Rp1
        
        return response()->json([
            'success' => true,
            'discount' => $discount,
            'remaining_points' => $balance - $request->points,
            'message' => 'Poin berhasil ditukar'
        ]);
    }
}
