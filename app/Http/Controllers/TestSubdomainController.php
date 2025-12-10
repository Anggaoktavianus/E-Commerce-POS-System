<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class TestSubdomainController extends Controller
{
    public function index($store = null)
    {
        $data = [
            'subdomain' => $store,
            'host' => request()->getHost(),
            'url' => request()->fullUrl(),
            'stores' => Store::all(),
        ];
        
        return view('test.subdomain', $data);
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
            ], 404);
        }
        
        return response()->json([
            'store' => $storeModel,
            'subdomain' => $store,
            'host' => request()->getHost(),
            'message' => 'Store found successfully!'
        ]);
    }
}
