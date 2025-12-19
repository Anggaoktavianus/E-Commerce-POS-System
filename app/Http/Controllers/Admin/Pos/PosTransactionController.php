<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreTransactionRequest;
use App\Services\PosService;
use App\Models\PosTransaction;
use Illuminate\Http\Request;

class PosTransactionController extends Controller
{
    protected $posService;

    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    public function create(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        
        if (!$outletId) {
            return redirect()->route('admin.pos.dashboard')
                ->with('error', 'Silakan pilih outlet terlebih dahulu');
        }

        // Check if shift is open
        $shift = \App\Models\PosShift::where('outlet_id', $outletId)
            ->where('status', 'open')
            ->where('shift_date', today())
            ->first();

        if (!$shift) {
            return redirect()->route('admin.pos.shifts.index', ['outlet_id' => $outletId])
                ->with('error', 'Tidak ada shift yang terbuka. Silakan buka shift terlebih dahulu.');
        }

        $outlet = \App\Models\Outlet::findOrFail($outletId);
        $products = \App\Models\Product::where('is_active', true)
            ->with('category')
            ->limit(50)
            ->get();

        return view('admin.pos.transactions.create', compact('outlet', 'shift', 'products', 'outletId'));
    }

    public function index(Request $request)
    {
        $query = PosTransaction::with(['outlet', 'user', 'customer', 'items']);

        // Filters
        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.pos.transactions.index', compact('transactions'));
    }

    public function store(StoreTransactionRequest $request)
    {

        try {
            $data = $request->all();
            $data['user_id'] = auth()->id();

            $transaction = $this->posService->createTransaction($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaction
            ], 201);
        } catch (\Exception $e) {
            \Log::error('POS Transaction Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $transaction = PosTransaction::with([
            'outlet',
            'shift',
            'user',
            'customer',
            'items.product',
            'payments'
        ])->findOrFail($id);

        return view('admin.pos.transactions.show', compact('transaction'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $transaction = $this->posService->cancelTransaction(
                $id,
                auth()->id(),
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            \Log::error('POS Transaction Cancel Error: ' . $e->getMessage(), [
                'transaction_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Refund transaction
     */
    public function refund(Request $request, $id)
    {
        // Check permission
        if (!auth()->user()->canRefundTransaction()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk refund transaction'
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
            'refund_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            $transaction = $this->posService->refundTransaction(
                $id,
                auth()->id(),
                $request->reason,
                $request->refund_amount
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil di-refund',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            \Log::error('POS Transaction Refund Error: ' . $e->getMessage(), [
                'transaction_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal refund transaksi: ' . $e->getMessage()
            ], 400);
        }
    }
}
