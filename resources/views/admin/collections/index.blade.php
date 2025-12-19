@extends('admin.layouts.app')

@section('title', 'Kelola Koleksi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-collection me-2 text-primary"></i>Kelola Koleksi
          </h4>
          <p class="text-muted mb-0">Buat dan kelola koleksi untuk menampilkan produk terpilih di halaman beranda</p>
        </div>
        <a href="{{ route('admin.collections.create') }}" class="btn btn-primary btn-modern mt-2 mt-md-0">
          <i class="bx bx-plus me-1"></i>Tambah Koleksi
        </a>
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
              <p class="stat-label mb-2">Total Koleksi</p>
              <h3 class="stat-value mb-0" id="totalCollections">0</h3>
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
              <p class="stat-label mb-2">Aktif</p>
              <h3 class="stat-value mb-0" id="activeCollections">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-show"></i>
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
              <p class="stat-label mb-2">Non-Aktif</p>
              <h3 class="stat-value mb-0" id="inactiveCollections">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-hide"></i>
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
              <p class="stat-label mb-2">Total Items</p>
              <h3 class="stat-value mb-0" id="totalItems">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-list-ul"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari koleksi berdasarkan nama atau key...">
          </div>
        </div>
        <div class="col-md-4">
          <select id="statusFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Non-Aktif</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Collections Table -->
  <div class="card card-modern">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold">
        <i class="bx bx-list-ul me-2"></i>Daftar Koleksi
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover table-modern" id="collections-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="25%">Nama</th>
            <th width="20%">Key</th>
            <th width="15%">Status</th>
            <th width="15%">Items</th>
            <th width="20%">Aksi</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Koleksi</h6>
          <ul class="mb-3">
            <li>Ketik nama koleksi di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Koleksi</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada koleksi yang ingin diubah</li>
            <li>Ubah nama atau key koleksi</li>
            <li>Key digunakan untuk identifikasi di template</li>
            <li>Atur status aktif/non-aktif</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Koleksi Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Koleksi</span></li>
            <li>Isi nama koleksi yang deskriptif</li>
            <li>Masukkan key unik (contoh: featured_products, new_arrivals)</li>
            <li>Key akan digunakan di template Blade</li>
            <li>Pilih status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Item Management:</strong> Kelola produk per koleksi</li>
            <li><strong>Key System:</strong> Identifikasi unik untuk template</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif koleksi</li>
            <li><strong>Home Display:</strong> Tampilkan di halaman beranda</li>
            <li><strong>Product Grouping:</strong> Kelompokkan produk tematik</li>
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
        var table = $('#collections-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.collections.data") }}',
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
                { data: 'name', name: 'name' },
                { data: 'key', name: 'key' },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
                { data: 'items', name: 'items', orderable: false, searchable: false },
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
                $('#collections-table_filter').hide();
                
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
                table.column(3).search('').draw();
            } else {
                table.column(3).search(status).draw();
            }
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.delete-collection', function(e) {
            e.preventDefault();
            var collectionId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Koleksi?',
                text: "Koleksi ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.collections.destroy", ":id") }}'.replace(':id', collectionId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Koleksi berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus koleksi.',
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
                url: '{{ route("admin.collections.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(collection) {
                            if (collection.is_active == '1' || collection.is_active == true) {
                                active++;
                            }
                        });
                    }
                    
                    $('#totalCollections').text(total);
                    $('#activeCollections').text(active);
                    $('#inactiveCollections').text(total - active);
                    $('#totalItems').text('0'); // Will be updated later with actual item count
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
