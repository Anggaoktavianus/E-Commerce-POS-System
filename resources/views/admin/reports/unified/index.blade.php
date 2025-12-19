@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan Terpadu (Online + POS)')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-bar-chart-alt-2 me-2 text-primary"></i>Laporan Penjualan Terpadu
          </h4>
          <p class="text-muted mb-0">Gabungan penjualan online dan offline (POS)</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.reports.unified.index') }}" class="row g-3">
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
          <label class="form-label">Dari Tanggal</label>
          <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Sampai Tanggal</label>
          <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-search me-1"></i>Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <p class="mb-1">Total Penjualan</p>
          <h3>Rp {{ number_format($totalSales['total'], 0, ',', '.') }}</h3>
          <small class="d-block mt-2">{{ number_format($totalSales['total_count'], 0, ',', '.') }} transaksi</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <p class="mb-1">Penjualan Online</p>
          <h3>Rp {{ number_format($totalSales['online']['total'], 0, ',', '.') }}</h3>
          <small class="d-block mt-2">
            {{ number_format($totalSales['online']['count'], 0, ',', '.') }} order
            ({{ number_format($totalSales['online']['percentage'], 2) }}%)
          </small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <p class="mb-1">Penjualan POS</p>
          <h3>Rp {{ number_format($totalSales['pos']['total'], 0, ',', '.') }}</h3>
          <small class="d-block mt-2">
            {{ number_format($totalSales['pos']['count'], 0, ',', '.') }} transaksi
            ({{ number_format($totalSales['pos']['percentage'], 2) }}%)
          </small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <p class="mb-1">Outlet</p>
          <h3>{{ $selectedOutlet ? $selectedOutlet->name : 'Semua' }}</h3>
          <small class="d-block mt-2">
            {{ $dateFrom }} s/d {{ $dateTo }}
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Comparison Chart -->
  @if(isset($comparison['daily']) && count($comparison['daily']) > 0)
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Perbandingan Harian (Online vs POS)</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th class="text-end">Online</th>
              <th class="text-end">POS</th>
              <th class="text-end">Total</th>
              <th class="text-center">Persentase</th>
            </tr>
          </thead>
          <tbody>
            @foreach($comparison['daily'] as $day)
            <tr>
              <td><strong>{{ $day['date_formatted'] }}</strong></td>
              <td class="text-end">
                Rp {{ number_format($day['online'], 0, ',', '.') }}
                <small class="text-muted d-block">({{ $day['online_count'] }} order)</small>
              </td>
              <td class="text-end">
                Rp {{ number_format($day['pos'], 0, ',', '.') }}
                <small class="text-muted d-block">({{ $day['pos_count'] }} transaksi)</small>
              </td>
              <td class="text-end">
                <strong>Rp {{ number_format($day['total'], 0, ',', '.') }}</strong>
                <small class="text-muted d-block">({{ $day['total_count'] }} total)</small>
              </td>
              <td class="text-center">
                @if($day['total'] > 0)
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ ($day['online'] / $day['total']) * 100 }}%">
                      {{ number_format(($day['online'] / $day['total']) * 100, 1) }}%
                    </div>
                    <div class="progress-bar bg-info" role="progressbar" 
                         style="width: {{ ($day['pos'] / $day['total']) * 100 }}%">
                      {{ number_format(($day['pos'] / $day['total']) * 100, 1) }}%
                    </div>
                  </div>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  <!-- Quick Links -->
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body text-center">
          <i class="bx bx-package text-primary" style="font-size: 3rem;"></i>
          <h5 class="mt-3">Laporan Produk</h5>
          <p class="text-muted">Lihat penjualan produk dari online dan POS</p>
          <a href="{{ route('admin.reports.unified.products', ['outlet_id' => $outletId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-primary">
            <i class="bx bx-right-arrow-alt me-1"></i>Lihat Laporan Produk
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body text-center">
          <i class="bx bx-category text-info" style="font-size: 3rem;"></i>
          <h5 class="mt-3">Laporan Kategori</h5>
          <p class="text-muted">Lihat penjualan kategori dari online dan POS</p>
          <a href="{{ route('admin.reports.unified.categories', ['outlet_id' => $outletId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-info">
            <i class="bx bx-right-arrow-alt me-1"></i>Lihat Laporan Kategori
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
