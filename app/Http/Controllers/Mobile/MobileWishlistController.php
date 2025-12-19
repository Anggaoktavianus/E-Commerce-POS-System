<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MobileWishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('mobile.login');
        }
        
        $wishlists = Wishlist::where('user_id', $user->id)
            ->with('product')
            ->orderByDesc('created_at')
            ->get();
        
        return view('mobile.wishlist', compact('wishlists'));
    }
    
    public function toggle(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }
        
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        
        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();
        
        if ($wishlist) {
            $wishlist->delete();
            $isWishlisted = false;
            $message = 'Dihapus dari wishlist';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id
            ]);
            $isWishlisted = true;
            $message = 'Ditambahkan ke wishlist';
        }
        
        $count = Wishlist::where('user_id', $user->id)->count();
        
        return response()->json([
            'success' => true,
            'is_wishlisted' => $isWishlisted,
            'message' => $message,
            'count' => $count
        ]);
    }
    
    public function check($productId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['is_wishlisted' => false]);
        }
        
        $isWishlisted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();
        
        return response()->json(['is_wishlisted' => $isWishlisted]);
    }
    
    public function getCount()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        
        $count = Wishlist::where('user_id', $user->id)->count();
        
        return response()->json(['count' => $count]);
    }
}
