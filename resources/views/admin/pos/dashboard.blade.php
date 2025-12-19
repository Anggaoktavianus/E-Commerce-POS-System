@extends('admin.layouts.app')

@section('title', 'POS Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-store me-2 text-primary"></i>POS Dashboard
          </h4>
          <p class="text-muted mb-0">Point of Sales & Kasir</p>
        </div>
        <div>
          <select id="outletSelect" class="form-select" onchange="changeOutlet(this.value)">
            <option value="">Pilih Outlet</option>
            @foreach($outlets as $outlet)
              <option value="{{ $outlet->id }}" {{ $outletId == $outlet->id ? 'selected' : '' }}>
                {{ $outlet->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  @if(!$outletId)
    <div class="alert alert-info">
      <i class="bx bx-info-circle me-2"></i>Silakan pilih outlet terlebih dahulu
    </div>
  @else
    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
      <div class="col-6 col-md-3">
        <div class="card stat-card bg-primary text-white">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <p class="stat-label mb-2">Penjualan Hari Ini</p>
                <h3 class="stat-value mb-0">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
              </div>
              <div class="stat-icon">
                <i class="bx bx-dollar-circle"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card stat-card bg-success text-white">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <p class="stat-label mb-2">Transaksi</p>
                <h3 class="stat-value mb-0">{{ number_format($todayTransactions, 0, ',', '.') }}</h3>
              </div>
              <div class="stat-icon">
                <i class="bx bx-receipt"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card stat-card bg-info text-white">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <p class="stat-label mb-2">Kas Balance</p>
                <h3 class="stat-value mb-0">Rp {{ number_format($cashBalance, 0, ',', '.') }}</h3>
              </div>
              <div class="stat-icon">
                <i class="bx bx-money"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card stat-card {{ $currentShift ? 'bg-success' : 'bg-warning' }} text-white">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <p class="stat-label mb-2">Shift Status</p>
                <h3 class="stat-value mb-0">{{ $currentShift ? 'Terbuka' : 'Tertutup' }}</h3>
              </div>
              <div class="stat-icon">
                <i class="bx bx-{{ $currentShift ? 'check-circle' : 'x-circle' }}"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Current Shift Info -->
    @if($currentShift)
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bx bx-time me-2"></i>Shift Aktif</h5>
          <button class="btn btn-sm btn-danger" onclick="closeShift({{ $currentShift->id }})">
            <i class="bx bx-x me-1"></i>Tutup Shift
          </button>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <p class="mb-1"><strong>Kasir:</strong></p>
              <p>{{ $currentShift->user->name }}</p>
            </div>
            <div class="col-md-3">
              <p class="mb-1"><strong>Opening Balance:</strong></p>
              <p>Rp {{ number_format($currentShift->opening_balance, 0, ',', '.') }}</p>
            </div>
            <div class="col-md-3">
              <p class="mb-1"><strong>Total Sales:</strong></p>
              <p>Rp {{ number_format($currentShift->total_sales, 0, ',', '.') }}</p>
            </div>
            <div class="col-md-3">
              <p class="mb-1"><strong>Transaksi:</strong></p>
              <p>{{ $currentShift->total_transactions }}</p>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="alert alert-warning">
        <i class="bx bx-info-circle me-2"></i>Tidak ada shift yang terbuka. 
        <a href="{{ route('admin.pos.shifts.index', ['outlet_id' => $outletId]) }}" class="alert-link">Buka shift baru</a>
      </div>
    @endif

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="bx bx-plus-circle fs-1 text-primary mb-3"></i>
            <h5>Transaksi Baru</h5>
            <p class="text-muted">Buat transaksi penjualan baru</p>
            <a href="{{ route('admin.pos.transactions.create', ['outlet_id' => $outletId]) }}" class="btn btn-primary">
              <i class="bx bx-plus me-1"></i>Mulai Transaksi
            </a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="bx bx-list-ul fs-1 text-info mb-3"></i>
            <h5>Riwayat Transaksi</h5>
            <p class="text-muted">Lihat semua transaksi</p>
            <a href="{{ route('admin.pos.transactions.index', ['outlet_id' => $outletId]) }}" class="btn btn-info">
              <i class="bx bx-list-ul me-1"></i>Lihat Transaksi
            </a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="bx bx-time fs-1 text-success mb-3"></i>
            <h5>Kelola Shift</h5>
            <p class="text-muted">Buka atau tutup shift</p>
            <a href="{{ route('admin.pos.shifts.index', ['outlet_id' => $outletId]) }}" class="btn btn-success">
              <i class="bx bx-time me-1"></i>Kelola Shift
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>Transaksi Terakhir</h5>
      </div>
      <div class="card-body">
        @if($recentTransactions->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>No. Transaksi</th>
                  <th>Kasir</th>
                  <th>Customer</th>
                  <th>Total</th>
                  <th>Payment</th>
                  <th>Waktu</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentTransactions as $transaction)
                  <tr>
                    <td><strong>{{ $transaction->transaction_number }}</strong></td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ $transaction->customer ? $transaction->customer->name : 'Walk-in' }}</td>
                    <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                    <td>
                      <span class="badge bg-{{ $transaction->payment_method == 'cash' ? 'success' : 'info' }}">
                        {{ strtoupper($transaction->payment_method) }}
                      </span>
                    </td>
                    <td>{{ $transaction->created_at->format('H:i') }}</td>
                    <td>
                      <a href="{{ route('admin.pos.transactions.show', $transaction->id) }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-show"></i>
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted text-center py-4">Belum ada transaksi hari ini</p>
        @endif
      </div>
    </div>
  @endif
</div>

<script>
function changeOutlet(outletId) {
  if (outletId) {
    window.location.href = '{{ route("admin.pos.dashboard") }}?outlet_id=' + outletId;
  }
}

function closeShift(shiftId) {
  if (confirm('Apakah Anda yakin ingin menutup shift ini?')) {
    // Redirect to close shift page
    window.location.href = '{{ route("admin.pos.shifts.index") }}?outlet_id={{ $outletId }}&close=' + shiftId;
  }
}
</script>
@endsection
