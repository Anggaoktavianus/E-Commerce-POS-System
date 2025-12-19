<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosSetting;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosSettingController extends Controller
{
    /**
     * Display settings index (list of outlets)
     */
    public function index()
    {
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.pos.settings.index', compact('outlets'));
    }

    /**
     * Show settings for specific outlet
     */
    public function show($outletId)
    {
        $outlet = Outlet::findOrFail($outletId);
        
        // Get all settings for this outlet
        $settings = PosSetting::where('outlet_id', $outletId)
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        // Default settings structure
        $defaultSettings = [
            'receipt_template_id' => $settings['receipt_template_id'] ?? null,
            'tax_rate' => $settings['tax_rate'] ?? 0,
            'tax_enabled' => $settings['tax_enabled'] ?? false,
            'discount_enabled' => $settings['discount_enabled'] ?? true,
            'max_discount_percentage' => $settings['max_discount_percentage'] ?? 50,
            'payment_methods' => json_decode($settings['payment_methods'] ?? '["cash","card","ewallet","qris"]', true),
            'loyalty_points_enabled' => $settings['loyalty_points_enabled'] ?? true,
            'loyalty_points_rate' => $settings['loyalty_points_rate'] ?? 1, // 1% of transaction
            'member_discount_enabled' => $settings['member_discount_enabled'] ?? true,
            'member_discount_rate' => $settings['member_discount_rate'] ?? 5, // 5% discount for members
            'auto_print_receipt' => $settings['auto_print_receipt'] ?? false,
            'receipt_footer_text' => $settings['receipt_footer_text'] ?? '',
            'receipt_show_logo' => $settings['receipt_show_logo'] ?? true,
        ];

        return view('admin.pos.settings.show', compact('outlet', 'defaultSettings'));
    }

    /**
     * Update settings for outlet
     */
    public function update(Request $request, $outletId)
    {
        $request->validate([
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_enabled' => 'boolean',
            'discount_enabled' => 'boolean',
            'max_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'payment_methods' => 'nullable|array',
            'loyalty_points_enabled' => 'boolean',
            'loyalty_points_rate' => 'nullable|numeric|min:0|max:100',
            'auto_print_receipt' => 'boolean',
            'receipt_footer_text' => 'nullable|string|max:500',
            'receipt_show_logo' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $settings = [
                'tax_rate' => $request->tax_rate ?? 0,
                'tax_enabled' => $request->has('tax_enabled') ? 1 : 0,
                'discount_enabled' => $request->has('discount_enabled') ? 1 : 0,
                'max_discount_percentage' => $request->max_discount_percentage ?? 50,
                'payment_methods' => json_encode($request->payment_methods ?? ['cash', 'card', 'ewallet', 'qris']),
            'loyalty_points_enabled' => $request->has('loyalty_points_enabled') ? 1 : 0,
            'loyalty_points_rate' => $request->loyalty_points_rate ?? 1,
            'member_discount_enabled' => $request->has('member_discount_enabled') ? 1 : 0,
            'member_discount_rate' => $request->member_discount_rate ?? 5,
            'auto_print_receipt' => $request->has('auto_print_receipt') ? 1 : 0,
                'receipt_footer_text' => $request->receipt_footer_text ?? '',
                'receipt_show_logo' => $request->has('receipt_show_logo') ? 1 : 0,
            ];

            foreach ($settings as $key => $value) {
                PosSetting::set($outletId, $key, $value);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settings berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Settings Update Error: ' . $e->getMessage(), [
                'outlet_id' => $outletId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal update settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
