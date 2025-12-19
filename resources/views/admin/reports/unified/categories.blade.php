@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan Kategori Terpadu')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-category me-2 text-primary"></i>Laporan Penjualan Kategori Terpadu
          </h4>
          <p class="text-muted mb-0">Gabungan penjualan kategori dari online dan POS</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.reports.unified.index', ['outlet_id' => $outletId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.reports.unified.categories') }}" class="row g-3">
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

  <!-- Categories Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Detail Penjualan Kategori</h5>
    </div>
    <div class="card-body">
      @if($categorySales->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>Kategori</th>
              <th class="text-end">Online Qty</th>
              <th class="text-end">Online Sales</th>
              <th class="text-end">POS Qty</th>
              <th class="text-end">POS Sales</th>
              <th class="text-end">Total Qty</th>
              <th class="text-end">Total Sales</th>
            </tr>
          </thead>
          <tbody>
            @foreach($categorySales as $index => $item)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td><strong>{{ $item['category_name'] }}</strong></td>
              <td class="text-end">
                {{ number_format($item['online_quantity'], 0, ',', '.') }}
                <small class="text-muted d-block">({{ $item['online_count'] }} order)</small>
              </td>
              <td class="text-end">
                <span class="text-success">Rp {{ number_format($item['online_total'], 0, ',', '.') }}</span>
              </td>
              <td class="text-end">
                {{ number_format($item['pos_quantity'], 0, ',', '.') }}
                <small class="text-muted d-block">({{ $item['pos_count'] }} transaksi)</small>
              </td>
              <td class="text-end">
                <span class="text-info">Rp {{ number_format($item['pos_total'], 0, ',', '.') }}</span>
              </td>
              <td class="text-end">
                <strong>{{ number_format($item['total_quantity'], 0, ',', '.') }}</strong>
              </td>
              <td class="text-end">
                <strong class="text-primary">Rp {{ number_format($item['total_sales'], 0, ',', '.') }}</strong>
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="table-primary">
              <th colspan="2">TOTAL</th>
              <th class="text-end">{{ number_format($categorySales->sum('online_quantity'), 0, ',', '.') }}</th>
              <th class="text-end">Rp {{ number_format($categorySales->sum('online_total'), 0, ',', '.') }}</th>
              <th class="text-end">{{ number_format($categorySales->sum('pos_quantity'), 0, ',', '.') }}</th>
              <th class="text-end">Rp {{ number_format($categorySales->sum('pos_total'), 0, ',', '.') }}</th>
              <th class="text-end">{{ number_format($categorySales->sum('total_quantity'), 0, ',', '.') }}</th>
              <th class="text-end">Rp {{ number_format($categorySales->sum('total_sales'), 0, ',', '.') }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
      @else
      <div class="alert alert-info">
        <i class="bx bx-info-circle me-2"></i>Tidak ada data penjualan kategori pada periode yang dipilih
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
