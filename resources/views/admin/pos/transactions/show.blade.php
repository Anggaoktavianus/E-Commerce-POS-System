@extends('admin.layouts.app')

@section('title', 'Detail Transaksi POS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-receipt me-2 text-primary"></i>Detail Transaksi
          </h4>
          <p class="text-muted mb-0">{{ $transaction->transaction_number }}</p>
        </div>
        <div>
          <a href="{{ route('admin.pos.transactions.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
          <a href="{{ route('admin.pos.receipts.preview', $transaction->id) }}" class="btn btn-info">
            <i class="bx bx-receipt me-1"></i>Preview Receipt
          </a>
          <a href="{{ route('admin.pos.receipts.print', $transaction->id) }}" target="_blank" class="btn btn-primary">
            <i class="bx bx-printer me-1"></i>Print Receipt
          </a>
          @if($transaction->canCancel())
            <button class="btn btn-warning" onclick="confirmCancelTransaction()">
              <i class="bx bx-x me-1"></i>Cancel
            </button>
          @endif
          @if($transaction->status === 'completed' && auth()->user()->canRefundTransaction())
            <button class="btn btn-danger" onclick="showRefundModal()">
              <i class="bx bx-undo me-1"></i>Refund
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Transaction Details -->
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Item Transaksi</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Qty</th>
                  <th>Harga</th>
                  <th>Diskon</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($transaction->items as $item)
                  <tr>
                    <td>
                      <strong>{{ $item->product_name }}</strong><br>
                      <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($item->total_amount, 0, ',', '.') }}</strong></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Summary -->
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Informasi Transaksi</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <p class="mb-1"><strong>Outlet:</strong></p>
            <p>{{ $transaction->outlet->name }}</p>
          </div>
          <div class="mb-3">
            <p class="mb-1"><strong>Kasir:</strong></p>
            <p>{{ $transaction->user->name }}</p>
          </div>
          <div class="mb-3">
            <p class="mb-1"><strong>Customer:</strong></p>
            <p>{{ $transaction->customer ? $transaction->customer->name : 'Walk-in' }}</p>
          </div>
          <div class="mb-3">
            <p class="mb-1"><strong>Waktu:</strong></p>
            <p>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
          </div>
          <div class="mb-3">
            <p class="mb-1"><strong>Status:</strong></p>
            <p>
              @if($transaction->status == 'completed')
                <span class="badge bg-success">Completed</span>
              @elseif($transaction->status == 'cancelled')
                <span class="badge bg-danger">Cancelled</span>
              @else
                <span class="badge bg-warning">{{ ucfirst($transaction->status) }}</span>
              @endif
            </p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Rincian Pembayaran</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span>Subtotal:</span>
            <strong>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</strong>
          </div>
          @if($transaction->discount_amount > 0)
            <div class="d-flex justify-content-between mb-2">
              <span>Diskon:</span>
              <strong class="text-danger">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</strong>
            </div>
          @endif
          @if($transaction->tax_amount > 0)
            <div class="d-flex justify-content-between mb-2">
              <span>Pajak:</span>
              <strong>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</strong>
            </div>
          @endif
          <hr>
          <div class="d-flex justify-content-between mb-2">
            <span><strong>Total:</strong></span>
            <strong class="text-primary fs-5">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
          </div>
          <div class="mt-3">
            <p class="mb-1"><strong>Payment Method:</strong></p>
            <span class="badge bg-{{ $transaction->payment_method == 'cash' ? 'success' : 'info' }}">
              {{ strtoupper($transaction->payment_method) }}
            </span>
          </div>
          @if($transaction->payment_method == 'cash' && $transaction->cash_received)
            <div class="mt-2">
              <p class="mb-1"><strong>Cash Received:</strong></p>
              <p>Rp {{ number_format($transaction->cash_received, 0, ',', '.') }}</p>
            </div>
            @if($transaction->change_amount)
              <div class="mt-2">
                <p class="mb-1"><strong>Change:</strong></p>
                <p class="text-success">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
              </div>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function confirmCancelTransaction() {
  Swal.fire({
    title: 'Batalkan Transaksi?',
    html: `
      <p>No. Transaksi: <strong>{{ $transaction->transaction_number }}</strong></p>
      <p>Total: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></p>
      <p>Alasan pembatalan:</p>
      <textarea id="cancelReason" class="form-control" rows="3" placeholder="Masukkan alasan pembatalan..." required></textarea>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Batal',
    preConfirm: () => {
      const reason = document.getElementById('cancelReason').value;
      if (!reason) {
        Swal.showValidationMessage('Alasan pembatalan harus diisi');
        return false;
      }
      return reason;
    }
  }).then((result) => {
    if (result.isConfirmed) {
      cancelTransaction(result.value);
    }
  });
}

function cancelTransaction(reason) {
  Swal.fire({
    title: 'Memproses...',
    text: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  const formData = new FormData();
  formData.append('reason', reason);

  fetch('{{ route("admin.pos.transactions.cancel", $transaction->id) }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Transaksi Dibatalkan',
        text: 'Transaksi berhasil dibatalkan',
        confirmButtonText: 'OK'
      }).then(() => {
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal Membatalkan',
        text: data.message || 'Terjadi kesalahan'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan saat membatalkan transaksi'
    });
  });
}

function showRefundModal() {
  Swal.fire({
    title: 'Refund Transaksi',
    html: `
      <p>No. Transaksi: <strong>{{ $transaction->transaction_number }}</strong></p>
      <p>Total Transaksi: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></p>
      <div class="mb-3">
        <label class="form-label">Jumlah Refund (kosongkan untuk full refund)</label>
        <input type="number" id="refundAmount" class="form-control" 
               step="0.01" min="0" max="{{ $transaction->total_amount }}" 
               placeholder="Kosongkan untuk full refund">
      </div>
      <div class="mb-3">
        <label class="form-label">Alasan Refund <span class="text-danger">*</span></label>
        <textarea id="refundReason" class="form-control" rows="3" 
                  placeholder="Masukkan alasan refund..." required></textarea>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Refund',
    cancelButtonText: 'Batal',
    preConfirm: () => {
      const reason = document.getElementById('refundReason').value;
      const amount = document.getElementById('refundAmount').value;
      
      if (!reason) {
        Swal.showValidationMessage('Alasan refund harus diisi');
        return false;
      }
      
      if (amount && (parseFloat(amount) <= 0 || parseFloat(amount) > {{ $transaction->total_amount }})) {
        Swal.showValidationMessage('Jumlah refund tidak valid');
        return false;
      }
      
      return {
        reason: reason,
        amount: amount ? parseFloat(amount) : null
      };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      processRefund(result.value.reason, result.value.amount);
    }
  });
}

function processRefund(reason, refundAmount) {
  Swal.fire({
    title: 'Memproses Refund...',
    text: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  const formData = new FormData();
  formData.append('reason', reason);
  if (refundAmount) {
    formData.append('refund_amount', refundAmount);
  }

  fetch('{{ route("admin.pos.transactions.refund", $transaction->id) }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Refund Berhasil',
        text: refundAmount ? `Refund sebesar Rp ${new Intl.NumberFormat('id-ID').format(refundAmount)} berhasil diproses` : 'Full refund berhasil diproses',
        confirmButtonText: 'OK'
      }).then(() => {
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal Refund',
        text: data.message || 'Terjadi kesalahan'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan saat memproses refund'
    });
  });
}
</script>
@endsection
