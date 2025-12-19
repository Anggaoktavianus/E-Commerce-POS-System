<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Template Preview - {{ $template->name }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">Print Preview</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    {!! str_replace(
        [
            '{{transaction_number}}',
            '{{date}}',
            '{{time}}',
            '{{outlet_name}}',
            '{{cashier_name}}',
            '{{customer_name}}',
            '{{total_amount}}',
            '{{payment_method}}',
            '{{items}}'
        ],
        [
            $sampleTransaction->transaction_number,
            $sampleTransaction->created_at->format('d/m/Y'),
            $sampleTransaction->created_at->format('H:i:s'),
            $sampleTransaction->outlet->name,
            $sampleTransaction->user->name,
            'Sample Customer',
            'Rp ' . number_format($sampleTransaction->total_amount, 0, ',', '.'),
            strtoupper($sampleTransaction->payment_method),
            view('admin.pos.receipt-templates.partials.sample-items', ['items' => $sampleTransaction->items])->render()
        ],
        $template->template_content
    ) !!}
</body>
</html>
