<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Store;

class StoreContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get store from different sources
        $storeCode = $this->getStoreFromRequest($request);
        
        if (!$storeCode) {
            return response()->json([
                'error' => 'Store identifier required',
                'message' => 'Please provide store code or subdomain'
            ], 400);
        }
        
        // Find store
        $store = Store::where('code', $storeCode)
                     ->orWhere('domain', $storeCode)
                     ->first();
        
        if (!$store) {
            return response()->json([
                'error' => 'Store not found',
                'store_code' => $storeCode,
                'available_stores' => Store::pluck('name', 'code'),
                'message' => 'Store not found. Please check the store code.'
            ], 404);
        }
        
        // Check if store is active
        if (!$store->is_active) {
            return response()->json([
                'error' => 'Store inactive',
                'store' => $store->name,
                'message' => 'Store is currently inactive.'
            ], 403);
        }
        
        // Set global store context
        app()->instance('current_store', $store);
        
        // Share store with all views
        view()->share('current_store', $store);
        
        // Add store to request for easy access
        $request->merge(['current_store' => $store]);
        
        return $next($request);
    }
    
    /**
     * Extract store code from request
     */
    private function getStoreFromRequest(Request $request): ?string
    {
        // Method 1: From subdomain
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0] ?? null;
        
        if ($subdomain && $subdomain !== 'www' && $subdomain !== 'localhost' && $subdomain !== '127') {
            return $subdomain;
        }
        
        // Method 2: From route parameter
        if ($request->route('store')) {
            return $request->route('store');
        }
        
        // Method 3: From query parameter
        if ($request->get('store')) {
            return $request->get('store');
        }
        
        // Method 4: From header
        if ($request->header('X-Store-Code')) {
            return $request->header('X-Store-Code');
        }
        
        return null;
    }
}
