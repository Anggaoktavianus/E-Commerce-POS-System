@extends('admin.layouts.app')

@section('title', 'Laporan Shift')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-file me-2 text-primary"></i>Laporan Shift
          </h4>
          <p class="text-muted mb-0">Detail laporan shift #{{ $shift->id }}</p>
        </div>
        <div>
          <a href="{{ route('admin.pos.shifts.index', ['outlet_id' => $shift->outlet_id]) }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Shift Summary -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <p class="text-muted mb-1">Outlet</p>
          <h5>{{ $shift->outlet->name }}</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <p class="text-muted mb-1">Kasir</p>
          <h5>{{ $shift->user->name }}</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <p class="text-muted mb-1">Tanggal</p>
          <h5>{{ $shift->shift_date->format('d/m/Y') }}</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <p class="text-muted mb-1">Status</p>
          <h5>
            @if($shift->status == 'open')
              <span class="badge bg-success">Terbuka</span>
            @elseif($shift->status == 'closed')
              <span class="badge bg-secondary">Tertutup</span>
            @else
              <span class="badge bg-warning">Pending</span>
            @endif
          </h5>
        </div>
      </div>
    </div>
  </div>

  <!-- Financial Summary -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <p class="mb-1">Opening Balance</p>
          <h3>Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <p class="mb-1">Total Sales</p>
          <h3>Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <p class="mb-1">Expected Cash</p>
          <h3>Rp {{ number_format($shift->expected_cash ?? $shift->calculateExpectedCash(), 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-{{ $shift->variance && abs($shift->variance) > 10000 ? 'danger' : 'success' }} text-white">
        <div class="card-body">
          <p class="mb-1">Variance</p>
          <h3>Rp {{ number_format($shift->variance ?? 0, 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Transactions -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>Transaksi ({{ $shift->total_transactions }})</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No. Transaksi</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Waktu</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($shift->transactions as $transaction)
              <tr>
                <td><strong>{{ $transaction->transaction_number }}</strong></td>
                <td>{{ $transaction->customer ? $transaction->customer->name : 'Walk-in' }}</td>
                <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                <td>
                  <span class="badge bg-{{ $transaction->payment_method == 'cash' ? 'success' : 'info' }}">
                    {{ strtoupper($transaction->payment_method) }}
                  </span>
                </td>
                <td>{{ $transaction->created_at->format('H:i:s') }}</td>
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
    </div>
  </div>
</div>
@endsection
