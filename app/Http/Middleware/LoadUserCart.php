<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadUserCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is logged in, always sync cart from database to session
        // This ensures session cart is always up-to-date with database
        if ($user) {
            $cart = Cart::where('user_id', $user->id)->first();
            if ($cart && $cart->items->count() > 0) {
                // Always update session cart from database for logged-in users
                $request->session()->put('cart', $cart->toSessionArray());
            } else {
                // If no cart in database, clear session cart too
                $request->session()->forget('cart');
            }
        }
        
        return $next($request);
    }
}
