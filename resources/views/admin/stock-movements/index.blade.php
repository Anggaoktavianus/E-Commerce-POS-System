@extends('admin.layouts.app')

@section('title', 'Riwayat Perubahan Stok')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-history me-2 text-primary"></i>Riwayat Perubahan Stok
          </h4>
          <p class="text-muted mb-0">Pantau semua perubahan stok produk secara detail</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
          <a href="{{ route('admin.stock_movements.export_summary') }}" class="btn btn-success btn-modern" target="_blank">
            <i class="bx bx-download me-1"></i>Export Ringkasan
          </a>
          <button type="button" class="btn btn-primary btn-modern" id="btn-export-history">
            <i class="bx bx-download me-1"></i>Export Riwayat
          </button>
          <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-modern">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card search-card mb-4">
    <div class="card-body">
      <form id="filter-form" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Filter Produk</label>
          <select name="product_id" id="filter-product" class="form-select">
            <option value="">Semua Produk</option>
            @foreach($products as $p)
              <option value="{{ $p->id }}" {{ ($productId ?? '') == $p->id ? 'selected' : '' }}>
                {{ $p->name }} (Stok: {{ $p->stock_qty ?? 0 }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tipe Perubahan</label>
          <select name="type" id="filter-type" class="form-select">
            <option value="">Semua Tipe</option>
            <option value="in">Stock Masuk</option>
            <option value="out">Stock Keluar</option>
            <option value="adjustment">Penyesuaian Manual</option>
            <option value="restore">Restore Stock</option>
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button type="button" id="btn-clear-filters" class="btn btn-outline-secondary w-100">
            <i class="bx bx-x me-2"></i>Reset Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Stock Movements Table -->
  <div class="card card-modern">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold">
        <i class="bx bx-list-ul me-2"></i>Daftar Perubahan Stok
      </h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="stock-movements-table" class="table table-striped table-hover table-modern">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Produk</th>
              <th>Tipe</th>
              <th>Jumlah</th>
              <th>Stok Lama</th>
              <th>Stok Baru</th>
              <th>Referensi</th>
              <th>User</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#stock-movements-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.stock_movements.data") }}',
            data: function(d) {
                d.product_id = $('#filter-product').val();
                d.type = $('#filter-type').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'product.name', name: 'product.name' },
            { data: 'type', name: 'type' },
            { data: 'quantity', name: 'quantity' },
            { data: 'old_stock', name: 'old_stock' },
            { data: 'new_stock', name: 'new_stock' },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'user.name', name: 'user.name' },
            { data: 'notes_display', name: 'notes', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        language: {
            processing: "Memproses...",
            lengthMenu: "Tampilkan _MENU_ entri",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            infoFiltered: "(difilter dari _MAX_ total entri)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Filter change
    $('#filter-product, #filter-type').on('change', function() {
        table.ajax.reload();
    });

    // Clear filters
    $('#btn-clear-filters').on('click', function() {
        $('#filter-product').val('');
        $('#filter-type').val('');
        table.ajax.reload();
    });

    // Export history
    $('#btn-export-history').on('click', function() {
        var productId = $('#filter-product').val();
        var type = $('#filter-type').val();
        var url = '{{ route("admin.stock_movements.export") }}';
        var params = new URLSearchParams();
        
        if (productId) params.append('product_id', productId);
        if (type) params.append('type', type);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        window.location.href = url;
    });
});
</script>
@endpush
