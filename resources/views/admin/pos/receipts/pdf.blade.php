<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $transaction->transaction_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .receipt-body {
            margin: 10px 0;
        }
        .receipt-footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 3px 0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h2 style="margin: 0; font-size: 18px;">{{ $template['header'] ?? 'TERIMA KASIH' }}</h2>
        @if($template['show_outlet_info'] ?? true)
        <p style="margin: 5px 0;">
            <strong>{{ $transaction->outlet->name ?? 'OUTLET' }}</strong><br>
            {{ $transaction->outlet->address ?? '' }}<br>
            {{ $transaction->outlet->phone ?? '' }}
        </p>
        @endif
    </div>

    <div class="receipt-body">
        <table>
            <tr>
                <td>No. Transaksi:</td>
                <td class="text-right"><strong>{{ $transaction->transaction_number }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal:</td>
                <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            @if($template['show_cashier_info'] ?? true)
            <tr>
                <td>Kasir:</td>
                <td class="text-right">{{ $transaction->user->name ?? '-' }}</td>
            </tr>
            @endif
            @if($template['show_customer_info'] ?? true && $transaction->customer)
            <tr>
                <td>Customer:</td>
                <td class="text-right">{{ $transaction->customer->name }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <td><strong>Item</strong></td>
                    <td class="text-right"><strong>Qty</strong></td>
                    <td class="text-right"><strong>Total</strong></td>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td colspan="3">
                        <strong>{{ $item->product_name }}</strong><br>
                        <small>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</small>
                        @if($item->discount_amount > 0)
                        <br><small style="color: red;">Diskon: -Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</small>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->discount_amount > 0)
            <tr>
                <td>Diskon:</td>
                <td class="text-right">-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($transaction->tax_amount > 0)
            <tr>
                <td>Pajak:</td>
                <td class="text-right">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="border-top: 1px solid #000;">
                <td><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        @if($template['show_payment_info'] ?? true)
        <div class="divider"></div>
        <table>
            <tr>
                <td>Metode:</td>
                <td class="text-right"><strong>{{ strtoupper($transaction->payment_method) }}</strong></td>
            </tr>
            @if($transaction->payment_method == 'cash')
            <tr>
                <td>Cash:</td>
                <td class="text-right">Rp {{ number_format($transaction->cash_received, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian:</td>
                <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
        @endif
    </div>

    <div class="receipt-footer">
        <p style="margin: 5px 0;">{{ $template['footer'] ?? 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan' }}</p>
        <p style="margin: 5px 0;">Terima kasih atas kunjungan Anda!</p>
    </div>
</body>
</html>
