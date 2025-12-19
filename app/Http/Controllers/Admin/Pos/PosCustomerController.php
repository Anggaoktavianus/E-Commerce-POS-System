<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PosLoyaltyService;
use Illuminate\Http\Request;

class PosCustomerController extends Controller
{
    /**
     * Search customers
     */
    public function search(Request $request)
    {
        // Handle loyalty balance check
        if ($request->has('loyalty_check') && $request->loyalty_check) {
            return $this->checkLoyaltyBalance($request);
        }

        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $query = $request->query;

        $customers = User::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->where(function($q) {
                $q->where('role', 'customer')
                  ->orWhereNull('role');
            })
            ->limit(20)
            ->get();

        // Add loyalty points balance and member status
        $customers->each(function($customer) {
            $customer->loyalty_balance = PosLoyaltyService::getBalance($customer->id);
            $customer->is_member = $customer->is_verified ?? false; // Member if verified
        });

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Check loyalty balance for customer
     */
    private function checkLoyaltyBalance(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
        ]);

        $balance = PosLoyaltyService::getBalance($request->customer_id);

        return response()->json([
            'success' => true,
            'balance' => $balance
        ]);
    }

    /**
     * Create customer (quick add)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'customer',
            'password' => bcrypt('password123'), // Default password, should be changed
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201);
    }

    /**
     * Get customer purchase history
     */
    public function history($id)
    {
        $customer = User::findOrFail($id);

        $transactions = \App\Models\PosTransaction::where('customer_id', $id)
            ->with(['outlet', 'items.product'])
            ->latest()
            ->paginate(10);

        $loyaltyBalance = PosLoyaltyService::getBalance($id);

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => $customer,
                'transactions' => $transactions,
                'loyalty_balance' => $loyaltyBalance,
            ]
        ]);
    }
}
