@extends('admin.layouts.app')

@section('title', 'Kelola Pesanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-shopping-bag me-2"></i>Kelola Pesanan
          </h4>
          <p class="text-muted mb-0">Pantau dan kelola semua pesanan pelanggan</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Total Pesanan</h6>
              <h3 class="mb-0" id="totalOrders">0</h3>
            </div>
            <i class="bx bx-shopping-bag bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Dibayar</h6>
              <h3 class="mb-0" id="paidOrders">0</h3>
            </div>
            <i class="bx bx-check-circle bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Menunggu</h6>
              <h3 class="mb-0" id="pendingOrders">0</h3>
            </div>
            <i class="bx bx-time bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Total Revenue</h6>
              <h3 class="mb-0" id="totalRevenue">0</h3>
            </div>
            <i class="bx bx-dollar-circle bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Filter -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari pesanan berdasarkan nomor, nama, atau email...">
          </div>
        </div>
        <div class="col-md-3">
          <select id="statusFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="pending">Menunggu</option>
            <option value="paid">Dibayar</option>
            <option value="failed">Gagal</option>
            <option value="cancelled">Dibatalkan</option>
            <option value="expired">Kadaluarsa</option>
          </select>
        </div>
        <div class="col-md-3">
          <select id="filter-store" class="form-select">
            <option value="">— Store Saat Ini —</option>
            @foreach(($stores ?? []) as $s)
              <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Orders Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Pesanan
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="orders-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="15%">Nomor Pesanan</th>
            <th width="20%">Pelanggan</th>
            <th width="15%">Total</th>
            <th width="10%">Status</th>
            <th width="15%">Metode Pembayaran</th>
            <th width="15%">Tanggal</th>
            <th width="10%">Aksi</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

  <!-- Help Guide -->
  <div class="card mt-4">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-help-circle me-2"></i>Panduan Penggunaan
      </h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Pesanan</h6>
          <ul class="mb-3">
            <li>Ketik nomor pesanan, nama pelanggan, atau email di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status pembayaran</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-eye text-success me-2"></i>Cara Melihat Detail Pesanan</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Lihat</span> pada pesanan yang ingin diperiksa</li>
            <li>Lihat detail produk, alamat pengiriman, dan status pembayaran</li>
            <li>Periksa riwayat transaksi Midtrans</li>
            <li>Unduh invoice jika diperlukan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-credit-card text-info me-2"></i>Status Pembayaran</h6>
          <ul class="mb-3">
            <li><strong>Menunggu:</strong> Pesanan baru, menunggu pembayaran</li>
            <li><strong>Dibayar:</strong> Pembayaran berhasil, siap diproses</li>
            <li><strong>Gagal:</strong> Pembayaran ditolak atau gagal</li>
            <li><strong>Dibatalkan:</strong> Pesanan dibatalkan pelanggan</li>
            <li><strong>Kadaluarsa:</strong> Pembayaran kadaluarsa</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Order Management:</strong> Kelola semua pesanan</li>
            <li><strong>Payment Tracking:</strong> Monitor status pembayaran</li>
            <li><strong>Customer Data:</strong> Informasi pelanggan lengkap</li>
            <li><strong>Revenue Analytics:</strong> Laporan penjualan</li>
            <li><strong>Midtrans Integration:</strong> Sistem pembayaran terpercaya</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.orders.data") }}',
                data: function(d){
                    d.store_id = $('#filter-store').val();
                },
                error: function(xhr, error, code) {
                    console.error('DataTable error:', xhr, error, code);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memuat data. Silakan refresh halaman.',
                        confirmButtonText: 'Refresh',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'order_number', name: 'order_number' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'payment_method', name: 'payment_method', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: "Sedang memproses...",
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data yang tersedia",
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
            },
            initComplete: function() {
                // Hide default search input since we have custom search
                $('#orders-table_filter').hide();
                
                // Update stats from initial data
                updateStats();
            }
        });

        // Custom search (using built-in DataTable search)
        $('#searchInput').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Status filter
        $('#statusFilter').on('change', function() {
            var status = $(this).val();
            if (status === '') {
                table.column(4).search('').draw();
            } else {
                table.column(4).search(status).draw();
            }
        });

        // Store filter
        $('#filter-store').on('change', function() {
            table.ajax.reload();
            updateStats();
        });

        // Update statistics function
        function updateStats() {
            $.ajax({
                url: '{{ route("admin.orders.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000, store_id: $('#filter-store').val() },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var paid = 0;
                    var pending = 0;
                    var revenue = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(order) {
                            if (order.status === 'paid') {
                                paid++;
                                // Extract numeric value from formatted amount
                                var amount = order.total_amount.replace(/[^0-9]/g, '');
                                revenue += parseFloat(amount) || 0;
                            } else if (order.status === 'pending') {
                                pending++;
                            }
                        });
                    }
                    
                    $('#totalOrders').text(total);
                    $('#paidOrders').text(paid);
                    $('#pendingOrders').text(pending);
                    $('#totalRevenue').text('IDR ' + revenue.toLocaleString('id-ID'));
                }
            });
        }

        // Success message from session
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
        @endif
    });
</script>
@endpush
