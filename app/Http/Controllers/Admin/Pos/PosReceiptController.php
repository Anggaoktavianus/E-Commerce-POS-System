<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosTransaction;
use App\Models\PosReceiptTemplate;
use App\Models\PosSetting;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class PosReceiptController extends Controller
{
    /**
     * Print receipt (thermal printer)
     */
    public function print($transactionId)
    {
        $transaction = PosTransaction::with([
            'outlet',
            'shift',
            'user',
            'customer',
            'items.product',
            'payments'
        ])->findOrFail($transactionId);

        // Get receipt template
        $template = $this->getReceiptTemplate($transaction->outlet_id);

        return view('admin.pos.receipts.print', compact('transaction', 'template'));
    }

    /**
     * Generate PDF receipt
     */
    public function pdf($transactionId)
    {
        $transaction = PosTransaction::with([
            'outlet',
            'shift',
            'user',
            'customer',
            'items.product',
            'payments'
        ])->findOrFail($transactionId);

        // Get receipt template
        $template = $this->getReceiptTemplate($transaction->outlet_id);

        // Generate PDF using dompdf
        $html = view('admin.pos.receipts.pdf', compact('transaction', 'template'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Courier');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width (thermal printer size)
        $dompdf->render();

        $filename = 'receipt_' . $transaction->transaction_number . '_' . date('Y-m-d_His') . '.pdf';

        return $dompdf->stream($filename, ['Attachment' => 0]);
    }

    /**
     * Preview receipt
     */
    public function preview($transactionId)
    {
        $transaction = PosTransaction::with([
            'outlet',
            'shift',
            'user',
            'customer',
            'items.product',
            'payments'
        ])->findOrFail($transactionId);

        // Get receipt template
        $template = $this->getReceiptTemplate($transaction->outlet_id);

        return view('admin.pos.receipts.preview', compact('transaction', 'template'));
    }

    /**
     * Mark receipt as printed
     */
    public function markPrinted($transactionId)
    {
        $transaction = PosTransaction::findOrFail($transactionId);
        
        $transaction->update([
            'receipt_printed' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Receipt marked as printed'
        ]);
    }

    /**
     * Get receipt template for outlet
     */
    private function getReceiptTemplate($outletId)
    {
        // Try to get outlet-specific template
        $template = PosReceiptTemplate::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->first();

        // If not found, get default template
        if (!$template) {
            $template = PosReceiptTemplate::whereNull('outlet_id')
                ->where('is_active', true)
                ->first();
        }

        // If still not found, return default template data
        if (!$template) {
            return [
                'header' => 'TERIMA KASIH',
                'footer' => 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan',
                'show_outlet_info' => true,
                'show_cashier_info' => true,
                'show_customer_info' => true,
                'show_payment_info' => true,
            ];
        }

        return $template;
    }
}
