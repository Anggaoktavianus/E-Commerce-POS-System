@extends('admin.layouts.app')

@section('title', 'Preview Receipt - ' . $transaction->transaction_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-receipt me-2 text-primary"></i>Preview Receipt
          </h4>
          <p class="text-muted mb-0">{{ $transaction->transaction_number }}</p>
        </div>
        <div>
          <a href="{{ route('admin.pos.transactions.show', $transaction->id) }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
          <a href="{{ route('admin.pos.receipts.print', $transaction->id) }}" target="_blank" class="btn btn-primary">
            <i class="bx bx-printer me-1"></i>Print
          </a>
          <a href="{{ route('admin.pos.receipts.pdf', $transaction->id) }}" target="_blank" class="btn btn-success">
            <i class="bx bx-download me-1"></i>Download PDF
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body" style="background: white; max-width: 80mm; margin: 0 auto; padding: 20px; font-family: 'Courier New', monospace; font-size: 12px;">
          <!-- Receipt Content (same as print.blade.php) -->
          <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
            <h2 style="margin: 0; font-size: 18px;">{{ $template['header'] ?? 'TERIMA KASIH' }}</h2>
            @if($template['show_outlet_info'] ?? true)
            <p style="margin: 5px 0;">
              <strong>{{ $transaction->outlet->name ?? 'OUTLET' }}</strong><br>
              {{ $transaction->outlet->address ?? '' }}<br>
              {{ $transaction->outlet->phone ?? '' }}
            </p>
            @endif
          </div>

          <div style="margin: 10px 0;">
            <table style="width: 100%; border-collapse: collapse;">
              <tr>
                <td>No. Transaksi:</td>
                <td style="text-align: right;"><strong>{{ $transaction->transaction_number }}</strong></td>
              </tr>
              <tr>
                <td>Tanggal:</td>
                <td style="text-align: right;">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
              </tr>
              @if($template['show_cashier_info'] ?? true)
              <tr>
                <td>Kasir:</td>
                <td style="text-align: right;">{{ $transaction->user->name ?? '-' }}</td>
              </tr>
              @endif
            </table>

            <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>

            <table style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr>
                  <td><strong>Item</strong></td>
                  <td style="text-align: right;"><strong>Qty</strong></td>
                  <td style="text-align: right;"><strong>Total</strong></td>
                </tr>
              </thead>
              <tbody>
                @foreach($transaction->items as $item)
                <tr>
                  <td colspan="3">
                    <strong>{{ $item->product_name }}</strong><br>
                    <small>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</small>
                  </td>
                </tr>
                <tr>
                  <td></td>
                  <td style="text-align: right;">{{ $item->quantity }}</td>
                  <td style="text-align: right;">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>

            <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>

            <table style="width: 100%; border-collapse: collapse;">
              <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
              </tr>
              @if($transaction->discount_amount > 0)
              <tr>
                <td>Diskon:</td>
                <td style="text-align: right;">-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
              </tr>
              @endif
              <tr style="border-top: 1px solid #000;">
                <td><strong>TOTAL:</strong></td>
                <td style="text-align: right;"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
              </tr>
            </table>
          </div>

          <div style="text-align: center; border-top: 1px dashed #000; padding-top: 10px; margin-top: 10px; font-size: 10px;">
            <p style="margin: 5px 0;">{{ $template['footer'] ?? 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan' }}</p>
            <p style="margin: 5px 0;">Terima kasih atas kunjungan Anda!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
