@extends('admin.layouts.app')

@section('title', 'Kelola Item Koleksi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-collection me-2 text-primary"></i>Item Koleksi: {{ $parent->name }}
          </h4>
          <p class="text-muted mb-0">Kelola produk dalam koleksi "{{ $parent->name }}" ({{ $parent->key }})</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
          <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary btn-modern">
            <i class="bx bx-arrow-back me-1"></i>Kembali ke Koleksi
          </a>
          <a href="{{ route('admin.collection_items.create', $parent->id) }}" class="btn btn-primary btn-modern">
            <i class="bx bx-plus me-1"></i>Tambah Item
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card stat-card bg-primary text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Item</p>
              <h3 class="stat-value mb-0" id="totalItems">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-collection"></i>
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
              <p class="stat-label mb-2">Urutan Terendah</p>
              <h3 class="stat-value mb-0" id="lowestOrder" style="font-size: 1.5rem;">-</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-sort-asc"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card stat-card bg-warning text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Urutan Tertinggi</p>
              <h3 class="stat-value mb-0" id="highestOrder" style="font-size: 1.5rem;">-</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-sort-desc"></i>
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
              <p class="stat-label mb-2">Koleksi Key</p>
              <h3 class="stat-value mb-0" style="font-size: 1.5rem;">{{ $parent->key }}</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-key"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Filter -->
  <div class="card search-card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari produk berdasarkan nama...">
          </div>
        </div>
        <div class="col-md-4">
          <select id="orderFilter" class="form-select">
            <option value="">Semua Urutan</option>
            <option value="1-5">Urutan 1-5</option>
            <option value="6-10">Urutan 6-10</option>
            <option value="11+">Urutan 11+</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Collection Items Table -->
  <div class="card card-modern">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold">
        <i class="bx bx-list-ul me-2"></i>Daftar Item Koleksi
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover table-modern" id="items-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="50%">Nama Produk</th>
            <th width="15%">Urutan</th>
            <th width="15%">Dibuat</th>
            <th width="15%">Aksi</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Item</h6>
          <ul class="mb-3">
            <li>Ketik nama produk di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan urutan tampilan</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Item</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada item yang ingin diubah</li>
            <li>Ganti produk yang ingin ditampilkan</li>
            <li>Atur ulang urutan tampilan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Menambah Item Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Item</span></li>
            <li>Pilih produk dari daftar yang tersedia</li>
            <li>Atur urutan tampilan (angka)</li>
            <li>Urutan lebih kecil = tampil lebih dulu</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menambah</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Product Selection:</strong> Pilih produk dari katalog</li>
            <li><strong>Order Management:</strong> Atur urutan tampilan</li>
            <li><strong>Collection Display:</strong> Tampilkan di halaman beranda</li>
            <li><strong>Dynamic Content:</strong> Update otomatis di website</li>
            <li><strong>Easy Reorder:</strong> Ubah urutan kapan saja</li>
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
        var table = $('#items-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.collection_items.data", $parent->id) }}',
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
            order: [[2, 'asc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'product_name', name: 'product_name' },
                { data: 'sort_order', name: 'sort_order' },
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
                $('#items-table_filter').hide();
                
                // Update stats from initial data
                updateStats();
            }
        });

        // Custom search (using built-in DataTable search)
        $('#searchInput').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Order filter
        $('#orderFilter').on('change', function() {
            var filter = $(this).val();
            if (filter === '') {
                table.column(2).search('').draw();
            } else if (filter === '1-5') {
                table.column(2).search('^[1-5]$').draw();
            } else if (filter === '6-10') {
                table.column(2).search('^[6-9]$|10$').draw();
            } else if (filter === '11+') {
                table.column(2).search('^1[1-9]$|^[2-9][0-9]+$').draw();
            }
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.delete-collection-item', function(e) {
            e.preventDefault();
            var itemId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Item?',
                text: "Item ini akan dihapus dari koleksi secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.collection_items.destroy", ":id") }}'.replace(':id', itemId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Item berhasil dihapus dari koleksi.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus item.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Update statistics function
        function updateStats() {
            $.ajax({
                url: '{{ route("admin.collection_items.data", $parent->id) }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var lowest = null;
                    var highest = null;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(item) {
                            var order = parseInt(item.sort_order);
                            if (lowest === null || order < lowest) lowest = order;
                            if (highest === null || order > highest) highest = order;
                        });
                    }
                    
                    $('#totalItems').text(total);
                    $('#lowestOrder').text(lowest !== null ? lowest : '-');
                    $('#highestOrder').text(highest !== null ? highest : '-');
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
