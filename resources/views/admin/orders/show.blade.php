@extends('admin.layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-shopping-bag me-2 text-primary"></i>Detail Pesanan #{{ $order->order_number }}
          </h4>
          <p class="text-muted mb-0">
            <span class="badge badge-modern bg-{{ $order->status_color }}">{{ $order->formatted_status }}</span>
            <span class="ms-2"><i class="bx bx-calendar me-1"></i>{{ $order->created_at->format('d M Y H:i') }}</span>
          </p>
        </div>
        <div class="mt-2 mt-md-0">
          <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-modern">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Order Details -->
    <div class="col-md-8">
      <!-- Customer Information -->
      <div class="card card-modern mb-4">
        <div class="card-header">
          <h5 class="mb-0 fw-bold">
            <i class="bx bx-user me-2"></i>Informasi Pelanggan
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h6>Nama Lengkap</h6>
              <p class="text-muted">{{ $order->shipping_address['first_name'] }} </p>
              
              <h6>Email</h6>
              <p class="text-muted">{{ $order->user?->email ?? 'Guest Customer' }}</p>
              
              <h6>Telepon</h6>
              <p class="text-muted">{{ $order->shipping_address['phone'] ?? '-' }}</p>
            </div>
            <div class="col-md-6">
              <h6>Alamat Pengiriman</h6>
              <p class="text-muted">
                {{ $order->shipping_address['address'] }}<br>
                {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['postal_code'] }}<br>
                {{ $order->shipping_address['country'] }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="card card-modern mb-4">
        <div class="card-header">
          <h5 class="mb-0 fw-bold">
            <i class="bx bx-package me-2"></i>Produk yang Dipesan
          </h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-modern">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Harga</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($order->items as $item)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="{{ $item->product_details['image'] ?? asset('fruitables/img/vegetable-item-3.png') }}" 
                           class="img-fluid me-3 rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="">
                      <div>
                        <h6 class="mb-0">{{ $item->product_name }}</h6>
                        <small class="text-muted">ID: {{ $item->product_id }}</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-center">{{ $item->quantity }}</td>
                  <td class="text-end">{{ $item->formatted_price }}</td>
                  <td class="text-end">{{ $item->formatted_total }}</td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3">Subtotal</th>
                  <th class="text-end">{{ $order->formatted_total }}</th>
                </tr>
                <tr>
                  <td colspan="3">Biaya Pengiriman</td>
                  <td class="text-end">IDR {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                  <td colspan="3">Diskon</td>
                  <td class="text-end text-danger">-IDR {{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="table-primary">
                  <th colspan="3">Total Pembayaran</th>
                  <th class="text-end">{{ $order->formatted_total }}</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Information -->
    <div class="col-md-4">
      <!-- Payment Status -->
      <div class="card card-modern mb-4">
        <div class="card-header">
          <h5 class="mb-0 fw-bold">
            <i class="bx bx-credit-card me-2"></i>Status Pembayaran
          </h5>
        </div>
        <div class="card-body">
          <div class="text-center mb-3">
            <span class="badge bg-{{ $order->status_color }} fs-6 p-2">{{ $order->formatted_status }}</span>
          </div>
          
          @if($order->payment_method)
          <div class="mb-3">
            <h6>Metode Pembayaran</h6>
            <p class="text-muted">{{ $order->payment_method }}</p>
          </div>
          @endif
          
          @if($order->paid_at)
          <div class="mb-3">
            <h6>Tanggal Pembayaran</h6>
            <p class="text-muted">{{ $order->paid_at->format('d M Y H:i') }}</p>
          </div>
          @endif
          
          @if($order->midtrans_transaction_id)
          <div class="mb-3">
            <h6>ID Transaksi Midtrans</h6>
            <p class="text-muted font-monospace">{{ $order->midtrans_transaction_id }}</p>
          </div>
          @endif
        </div>
      </div>

      <!-- Payment Transactions -->
      @if($order->paymentTransactions->count() > 0)
      <div class="card card-modern mb-4">
        <div class="card-header">
          <h5 class="mb-0 fw-bold">
            <i class="bx bx-history me-2"></i>Riwayat Transaksi
          </h5>
        </div>
        <div class="card-body">
          @foreach($order->paymentTransactions as $transaction)
          <div class="border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <span class="badge bg-{{ $transaction->status_color }}">{{ $transaction->formatted_status }}</span>
              <small class="text-muted">{{ $transaction->created_at->format('d M H:i') }}</small>
            </div>
            <p class="mb-1 mt-2"><strong>{{ $transaction->payment_type }}</strong></p>
            @if($transaction->payment_method)
            <p class="mb-0 text-muted">{{ $transaction->payment_method }}</p>
            @endif
            @if($transaction->va_numbers)
            <p class="mb-0 text-muted">
              VA: {{ $transaction->va_numbers[0]['bank'] }} - {{ $transaction->va_numbers[0]['va_number'] }}
            </p>
            @endif
          </div>
          @endforeach
        </div>
      </div>
      @endif

      <!-- Actions -->
      <div class="card card-modern">
        <div class="card-header">
          <h5 class="mb-0 fw-bold">
            <i class="bx bx-cog me-2"></i>Aksi
          </h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button class="btn btn-outline-primary" onclick="refreshOrderStatus({{ $order->id }})">
              <i class="bx bx-refresh me-2"></i>Refresh Status
            </button>
            <button class="btn btn-outline-success">
              <i class="bx bx-printer me-2"></i>Cetak Invoice
            </button>
            <button class="btn btn-outline-info">
              <i class="bx bx-envelope me-2"></i>Kirim Email
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function refreshOrderStatus(orderId) {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Refreshing...';
    
    fetch(`{{ route('admin.orders.refresh-status', ':order') }}`.replace(':order', orderId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reload page to show updated status
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat refresh status'
        });
    })
    .finally(() => {
        // Restore button state
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
@endpush
