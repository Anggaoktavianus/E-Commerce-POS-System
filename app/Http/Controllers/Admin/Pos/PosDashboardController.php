<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosShift;
use App\Models\PosTransaction;
use App\Models\Outlet;
use Illuminate\Http\Request;

class PosDashboardController extends Controller
{
    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        
        // Get outlets for selection
        $outlets = Outlet::where('is_active', true)->get();
        
        if (!$outletId && $outlets->count() > 0) {
            $outletId = $outlets->first()->id;
        }

        $todaySales = 0;
        $todayTransactions = 0;
        $cashBalance = 0;
        $currentShift = null;

        if ($outletId) {
            session(['pos_outlet_id' => $outletId]);
            
            // Get current shift
            $currentShift = PosShift::where('outlet_id', $outletId)
                ->where('status', 'open')
                ->where('shift_date', today())
                ->with('user')
                ->first();

            if ($currentShift) {
                $todaySales = $currentShift->total_sales;
                $todayTransactions = $currentShift->total_transactions;
                $cashBalance = $currentShift->calculateExpectedCash();
            } else {
                // Get today's sales even if shift is closed
                $todaySales = PosTransaction::where('outlet_id', $outletId)
                    ->whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->sum('total_amount');
                
                $todayTransactions = PosTransaction::where('outlet_id', $outletId)
                    ->whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->count();
            }

            // Get recent transactions
            $recentTransactions = PosTransaction::where('outlet_id', $outletId)
                ->with(['user', 'customer'])
                ->latest()
                ->take(10)
                ->get();
        } else {
            $recentTransactions = collect();
        }

        return view('admin.pos.dashboard', compact(
            'outlets',
            'outletId',
            'todaySales',
            'todayTransactions',
            'cashBalance',
            'currentShift',
            'recentTransactions'
        ));
    }
}
