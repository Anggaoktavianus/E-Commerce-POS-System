<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\OpenShiftRequest;
use App\Http\Requests\Pos\CloseShiftRequest;
use App\Models\PosShift;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosShiftController extends Controller
{
    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        
        $outlets = Outlet::where('is_active', true)->get();
        
        $query = PosShift::with(['outlet', 'user'])->latest();

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('shift_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('shift_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shifts = $query->paginate(20);

        return view('admin.pos.shifts.index', compact('shifts', 'outlets', 'outletId'));
    }

    public function current(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');

        if (!$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet tidak dipilih'
            ], 400);
        }

        $shift = PosShift::where('outlet_id', $outletId)
            ->where('status', 'open')
            ->where('shift_date', today())
            ->with(['outlet', 'user'])
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift yang terbuka'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shift
        ]);
    }

    public function open(OpenShiftRequest $request)
    {

        $outletId = $request->outlet_id;

        // Check if there's already an open shift
        $existingShift = PosShift::where('outlet_id', $outletId)
            ->where('status', 'open')
            ->where('shift_date', today())
            ->first();

        if ($existingShift) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada shift yang terbuka untuk outlet ini'
            ], 400);
        }

        // Check if previous shift is closed
        $previousShift = PosShift::where('outlet_id', $outletId)
            ->where('shift_date', today())
            ->where('shift_number', '<', $request->shift_number)
            ->latest()
            ->first();

        if ($previousShift && $previousShift->status !== 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Shift sebelumnya belum ditutup'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $shift = PosShift::create([
                'outlet_id' => $outletId,
                'user_id' => auth()->id(),
                'shift_date' => today(),
                'shift_number' => $request->shift_number,
                'opening_balance' => $request->opening_balance,
                'status' => 'open',
                'opened_at' => now(),
                'notes' => $request->notes,
            ]);

            session(['pos_outlet_id' => $outletId]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil dibuka',
                'data' => $shift->load('outlet', 'user')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Shift Open Error: ' . $e->getMessage(), [
                'outlet_id' => $outletId,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuka shift: ' . $e->getMessage()
            ], 500);
        }
    }

    public function close(CloseShiftRequest $request, $id)
    {

        $shift = PosShift::findOrFail($id);

        if ($shift->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Shift sudah ditutup atau tidak valid'
            ], 400);
        }

        if (!$shift->canClose()) {
            return response()->json([
                'success' => false,
                'message' => 'Shift tidak dapat ditutup'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Calculate expected cash
            $expectedCash = $shift->calculateExpectedCash();
            
            // Calculate variance
            $variance = $request->actual_cash - $expectedCash;

            $shift->update([
                'expected_cash' => $expectedCash,
                'actual_cash' => $request->actual_cash,
                'variance' => $variance,
                'closing_balance' => $request->actual_cash,
                'status' => 'closed',
                'closed_at' => now(),
                'notes' => $request->notes ?? $shift->notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil ditutup',
                'data' => $shift->load('outlet', 'user')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Shift Close Error: ' . $e->getMessage(), [
                'shift_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup shift: ' . $e->getMessage()
            ], 500);
        }
    }

    public function report($id)
    {
        $shift = PosShift::with(['outlet', 'user', 'transactions.items', 'cashMovements'])
            ->findOrFail($id);

        return view('admin.pos.shifts.report', compact('shift'));
    }
}
