<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class SubdomainTestController extends Controller
{
    public function index($store = null)
    {
        $data = [
            'subdomain' => $store,
            'host' => request()->getHost(),
            'url' => request()->fullUrl(),
            'stores' => Store::all(),
        ];
        
        return response()->json([
            'message' => 'Subdomain test successful!',
            'subdomain' => $store,
            'host' => request()->getHost(),
            'url' => request()->fullUrl(),
            'stores_count' => Store::count(),
            'stores' => Store::pluck('name', 'code'),
        ]);
    }
    
    public function store($store)
    {
        // Find store by code or domain
        $storeModel = Store::where('code', $store)
                          ->orWhere('domain', $store)
                          ->first();
        
        if (!$storeModel) {
            return response()->json([
                'error' => 'Store not found',
                'subdomain' => $store,
                'available_stores' => Store::pluck('name', 'code'),
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'store' => $storeModel,
            'subdomain' => $store,
            'host' => request()->getHost(),
            'message' => 'Store found successfully!'
        ]);
    }
}
