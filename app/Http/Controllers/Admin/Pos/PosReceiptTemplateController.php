<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosReceiptTemplate;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosReceiptTemplateController extends Controller
{
    /**
     * Display receipt templates for outlet
     */
    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $outlets = Outlet::where('is_active', true)->get();
        
        $query = PosReceiptTemplate::with('outlet');
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        } else {
            $query->whereNull('outlet_id'); // Global templates
        }
        
        $templates = $query->latest()->get();
        
        return view('admin.pos.receipt-templates.index', compact('templates', 'outlets', 'outletId'));
    }

    /**
     * Show receipt template editor
     */
    public function create(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $outlets = Outlet::where('is_active', true)->get();
        
        // Get default template as example
        $defaultTemplate = PosReceiptTemplate::whereNull('outlet_id')
            ->where('is_default', true)
            ->first();
        
        return view('admin.pos.receipt-templates.create', compact('outlets', 'outletId', 'defaultTemplate'));
    }

    /**
     * Store new receipt template
     */
    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'name' => 'required|string|max:255',
            'template_content' => 'required|string',
            'is_default' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // If setting as default, unset other defaults for this outlet
            if ($request->is_default) {
                PosReceiptTemplate::where('outlet_id', $request->outlet_id)
                    ->update(['is_default' => false]);
            }

            $template = PosReceiptTemplate::create([
                'outlet_id' => $request->outlet_id,
                'name' => $request->name,
                'template_content' => $request->template_content,
                'is_default' => $request->is_default ?? false,
                'is_active' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dibuat',
                'data' => $template
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Receipt Template Create Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show template editor for editing
     */
    public function edit($id)
    {
        $template = PosReceiptTemplate::with('outlet')->findOrFail($id);
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.pos.receipt-templates.edit', compact('template', 'outlets'));
    }

    /**
     * Update receipt template
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'name' => 'required|string|max:255',
            'template_content' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $template = PosReceiptTemplate::findOrFail($id);

        DB::beginTransaction();
        try {
            // If setting as default, unset other defaults for this outlet
            if ($request->is_default) {
                PosReceiptTemplate::where('outlet_id', $request->outlet_id)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $template->update([
                'outlet_id' => $request->outlet_id,
                'name' => $request->name,
                'template_content' => $request->template_content,
                'is_default' => $request->is_default ?? $template->is_default,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil diupdate',
                'data' => $template
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Receipt Template Update Error: ' . $e->getMessage(), [
                'template_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal update template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview template with sample data
     */
    public function preview($id)
    {
        $template = PosReceiptTemplate::findOrFail($id);
        
        // Create sample transaction data
        $sampleTransaction = (object)[
            'transaction_number' => 'POS-SAMPLE-001',
            'created_at' => now(),
            'total_amount' => 150000,
            'payment_method' => 'cash',
            'cash_received' => 200000,
            'change_amount' => 50000,
            'items' => [
                (object)[
                    'product_name' => 'Sample Product 1',
                    'quantity' => 2,
                    'unit_price' => 50000,
                    'total_amount' => 100000,
                ],
                (object)[
                    'product_name' => 'Sample Product 2',
                    'quantity' => 1,
                    'unit_price' => 50000,
                    'total_amount' => 50000,
                ],
            ],
            'outlet' => (object)['name' => 'Sample Outlet'],
            'user' => (object)['name' => 'Sample Cashier'],
        ];
        
        return view('admin.pos.receipt-templates.preview', compact('template', 'sampleTransaction'));
    }

    /**
     * Delete receipt template
     */
    public function destroy($id)
    {
        $template = PosReceiptTemplate::findOrFail($id);

        // Don't allow delete if it's the only template for outlet
        $otherTemplates = PosReceiptTemplate::where('outlet_id', $template->outlet_id)
            ->where('id', '!=', $id)
            ->count();

        if ($otherTemplates === 0 && $template->outlet_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus template terakhir untuk outlet ini'
            ], 400);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dihapus'
        ]);
    }
}
