<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Check if request is from mobile route
        if ($request->is('m/*') || $request->routeIs('mobile.*')) {
            return view('mobile.login');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'Kredensial tidak sesuai.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Load cart from database and merge with session cart if exists
        $user = Auth::user();
        $sessionCart = $request->session()->get('cart', []);
        
        if ($user) {
            $cart = \App\Models\Cart::getOrCreateCart($user->id, null);
            
            // If there's a session cart, merge it with database cart
            if (!empty($sessionCart)) {
                $cart->mergeWithSessionCart($sessionCart);
            }
            
            // Load cart from database to session
            $dbCart = $cart->toSessionArray();
            if (!empty($dbCart)) {
                $request->session()->put('cart', $dbCart);
            }
        }

        // Check if redirect parameter is provided
        $redirect = $request->input('redirect');
        if ($redirect) {
            return redirect($redirect);
        }
        
        // Check if request is from mobile route
        if ($request->is('m/*') || $request->routeIs('mobile.*')) {
            // Redirect berdasarkan role untuk mobile
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }
            if (method_exists($user, 'isMitra') && $user->isMitra()) {
                return redirect()->intended(route('mitra.dashboard'));
            }
            return redirect()->intended(route('mobile.account'));
        }
        
        // Redirect berdasarkan role
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        if (method_exists($user, 'isMitra') && $user->isMitra()) {
            return redirect()->intended(route('mitra.dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        // Save current cart to database before logout (cart is already saved in putCart)
        $user = Auth::user();
        if ($user) {
            $sessionCart = $request->session()->get('cart', []);
            if (!empty($sessionCart)) {
                $cart = \App\Models\Cart::getOrCreateCart($user->id, null);
                $cart->items()->delete();
                foreach ($sessionCart as $productId => $item) {
                    $cart->items()->create([
                        'product_id' => $productId,
                        'quantity' => $item['qty'] ?? $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                    ]);
                }
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
