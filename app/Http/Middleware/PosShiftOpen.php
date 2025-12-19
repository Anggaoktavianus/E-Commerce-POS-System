<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PosShift;

class PosShiftOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $outletId = $request->route('outlet_id') ?? $request->input('outlet_id') ?? session('pos_outlet_id');

        if (!$outletId) {
            return redirect()->route('admin.pos.dashboard')
                ->with('error', 'Silakan pilih outlet terlebih dahulu');
        }

        // Check if there's an open shift for this outlet
        $openShift = PosShift::where('outlet_id', $outletId)
            ->where('status', 'open')
            ->where('shift_date', today())
            ->first();

        if (!$openShift) {
            return redirect()->route('admin.pos.shifts.index', ['outlet_id' => $outletId])
                ->with('error', 'Tidak ada shift yang terbuka. Silakan buka shift terlebih dahulu.');
        }

        // Store shift in request for easy access
        $request->attributes->set('pos_shift', $openShift);

        return $next($request);
    }
}
