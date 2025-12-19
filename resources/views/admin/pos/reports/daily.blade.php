@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan Harian - POS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-bar-chart me-2 text-primary"></i>Laporan Penjualan Harian
          </h4>
          <p class="text-muted mb-0">Detail penjualan per hari</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.pos.dashboard') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
          <a href="{{ route('admin.pos.reports.export', ['type' => 'daily', 'outlet_id' => $outletId, 'date' => $date]) }}" class="btn btn-success">
            <i class="bx bx-download me-1"></i>Export CSV
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.pos.reports.daily') }}" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Outlet</label>
          <select name="outlet_id" class="form-select">
            <option value="">Semua Outlet</option>
            @foreach($outlets as $outlet)
              <option value="{{ $outlet->id }}" {{ $outletId == $outlet->id ? 'selected' : '' }}>
                {{ $outlet->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal</label>
          <input type="date" name="date" class="form-control" value="{{ $date }}" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bx bx-search me-1"></i>Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <p class="mb-1">Total Penjualan</p>
          <h3>Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-success text-white">
        <div class="card-body">
          <p class="mb-1">Jumlah Transaksi</p>
          <h3>{{ number_format($totalTransactions, 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-info text-white">
        <div class="card-body">
          <p class="mb-1">Total Item Terjual</p>
          <h3>{{ number_format($totalItems, 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Sales by Payment Method -->
  @if($salesByPayment->count() > 0)
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Penjualan per Metode Pembayaran</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Metode Pembayaran</th>
              <th class="text-end">Jumlah Transaksi</th>
              <th class="text-end">Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($salesByPayment as $method => $data)
            <tr>
              <td><strong>{{ ucfirst($method) }}</strong></td>
              <td class="text-end">{{ number_format($data['count'], 0, ',', '.') }}</td>
              <td class="text-end">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  <!-- Transactions Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Detail Transaksi</h5>
    </div>
    <div class="card-body">
      @if($transactions->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal & Waktu</th>
              <th>No. Transaksi</th>
              <th>Outlet</th>
              <th>Kasir</th>
              <th>Customer</th>
              <th>Metode</th>
              <th class="text-end">Total</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transactions as $index => $transaction)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>
                <div>{{ $transaction->created_at->format('d/m/Y') }}</div>
                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
              </td>
              <td><code>{{ $transaction->transaction_number }}</code></td>
              <td>{{ $transaction->outlet->name ?? '-' }}</td>
              <td>{{ $transaction->user->name ?? '-' }}</td>
              <td>{{ $transaction->customer->name ?? 'Walk-in' }}</td>
              <td><span class="badge bg-info">{{ ucfirst($transaction->payment_method) }}</span></td>
              <td class="text-end"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
              <td>
                <a href="{{ route('admin.pos.transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="bx bx-show"></i>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="alert alert-info">
        <i class="bx bx-info-circle me-2"></i>Tidak ada transaksi pada tanggal yang dipilih
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
