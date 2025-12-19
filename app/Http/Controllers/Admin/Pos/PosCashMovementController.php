<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosCashMovement;
use App\Models\PosShift;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosCashMovementController extends Controller
{
    /**
     * Display cash movements for a shift
     */
    public function index(Request $request, $shiftId)
    {
        $shift = PosShift::with(['outlet', 'user', 'cashMovements.user'])
            ->findOrFail($shiftId);

        $cashMovements = $shift->cashMovements()
            ->with('user')
            ->latest()
            ->get();

        return view('admin.pos.cash-movements.index', compact('shift', 'cashMovements'));
    }

    /**
     * Store new cash movement
     */
    public function store(Request $request, $shiftId)
    {
        $request->validate([
            'type' => 'required|in:deposit,withdrawal,transfer',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $shift = PosShift::findOrFail($shiftId);

        if (!$shift->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Shift harus dalam status open'
            ], 400);
        }

        // Check permission for withdrawal/transfer
        if (in_array($request->type, ['withdrawal', 'transfer']) && !auth()->user()->canManageCash()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk melakukan withdrawal/transfer'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $cashMovement = PosCashMovement::create([
                'shift_id' => $shiftId,
                'outlet_id' => $shift->outlet_id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'reference_number' => $request->reference_number,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cash movement berhasil dibuat',
                'data' => $cashMovement->load('user')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Cash Movement Create Error: ' . $e->getMessage(), [
                'shift_id' => $shiftId,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat cash movement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete cash movement
     */
    public function destroy($shiftId, $id)
    {
        $cashMovement = PosCashMovement::findOrFail($id);

        // Only allow delete if shift is still open
        if (!$cashMovement->shift->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus cash movement dari shift yang sudah ditutup'
            ], 400);
        }

        // Check permission
        if (in_array($cashMovement->type, ['withdrawal', 'transfer']) && !auth()->user()->canManageCash()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk menghapus cash movement ini'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $cashMovement->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cash movement berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Cash Movement Delete Error: ' . $e->getMessage(), [
                'cash_movement_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus cash movement: ' . $e->getMessage()
            ], 500);
        }
    }
}
