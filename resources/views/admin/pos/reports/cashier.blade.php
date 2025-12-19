@extends('admin.layouts.app')

@section('title', 'Laporan Kinerja Kasir - POS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-user me-2 text-primary"></i>Laporan Kinerja Kasir
          </h4>
          <p class="text-muted mb-0">Performance per kasir</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.pos.dashboard') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
          <a href="{{ route('admin.pos.reports.export', ['type' => 'cashier', 'outlet_id' => $outletId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-success">
            <i class="bx bx-download me-1"></i>Export CSV
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.pos.reports.cashier') }}" class="row g-3">
        <div class="col-md-3">
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
        <div class="col-md-3">
          <label class="form-label">Dari Tanggal</label>
          <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Sampai Tanggal</label>
          <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bx bx-search me-1"></i>Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Summary -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <p class="mb-1">Total Kasir</p>
          <h3>{{ number_format($cashierStats->count(), 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-success text-white">
        <div class="card-body">
          <p class="mb-1">Total Penjualan</p>
          <h3>Rp {{ number_format($cashierStats->sum('total_sales'), 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-info text-white">
        <div class="card-body">
          <p class="mb-1">Total Transaksi</p>
          <h3>{{ number_format($cashierStats->sum('transaction_count'), 0, ',', '.') }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Cashier Performance Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Detail Kinerja Kasir</h5>
    </div>
    <div class="card-body">
      @if($cashierStats->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>Kasir</th>
              <th class="text-end">Jumlah Transaksi</th>
              <th class="text-end">Total Penjualan</th>
              <th class="text-end">Rata-rata</th>
              <th class="text-end">Transaksi Terkecil</th>
              <th class="text-end">Transaksi Terbesar</th>
            </tr>
          </thead>
          <tbody>
            @foreach($cashierStats as $index => $stat)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td><strong>{{ $stat->user->name ?? '-' }}</strong></td>
              <td class="text-end">{{ number_format($stat->transaction_count, 0, ',', '.') }}</td>
              <td class="text-end"><strong>Rp {{ number_format($stat->total_sales, 0, ',', '.') }}</strong></td>
              <td class="text-end">Rp {{ number_format($stat->avg_transaction, 0, ',', '.') }}</td>
              <td class="text-end">Rp {{ number_format($stat->min_transaction, 0, ',', '.') }}</td>
              <td class="text-end">Rp {{ number_format($stat->max_transaction, 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="alert alert-info">
        <i class="bx bx-info-circle me-2"></i>Tidak ada data pada periode yang dipilih
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
