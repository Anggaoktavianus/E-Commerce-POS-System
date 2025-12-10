@extends('admin.layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-package me-2"></i>Kelola Produk
          </h4>
          <p class="text-muted mb-0">Buat dan kelola produk katalog untuk ditampilkan di website</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Tambah Produk
        </a>
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
              <h6 class="card-title">Total Produk</h6>
              <h3 class="mb-0" id="totalProducts">0</h3>
            </div>
            <i class="bx bx-package bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Aktif</h6>
              <h3 class="mb-0" id="activeProducts">0</h3>
            </div>
            <i class="bx bx-show bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Unggulan</h6>
              <h3 class="mb-0" id="featuredProducts">0</h3>
            </div>
            <i class="bx bx-star bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Terlaris</h6>
              <h3 class="mb-0" id="bestsellerProducts">0</h3>
            </div>
            <i class="bx bx-trending-up bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Filter -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari produk berdasarkan nama atau slug...">
          </div>
        </div>
        <div class="col-md-2">
          <select id="filter-category" class="form-select">
            <option value="">— Semua Kategori —</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select id="filter-featured" class="form-select">
            <option value="">— Semua Status —</option>
            <option value="1">Unggulan</option>
            <option value="0">Non-Unggulan</option>
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
        <div class="col-md-2">
          <button id="btn-clear-filters" class="btn btn-outline-secondary w-100">
            <i class="bx bx-x me-1"></i>Reset
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Products Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Produk
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="products-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="20%">Nama</th>
            <th width="15%">Kategori</th>
            <th width="10%">Harga</th>
            <th width="10%">Unit</th>
            <th width="8%">Unggulan</th>
            <th width="8%">Terlaris</th>
            <th width="9%">Status</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Produk</h6>
          <ul class="mb-3">
            <li>Ketik nama produk di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan kategori produk</li>
            <li>Filter berdasarkan status unggulan</li>
            <li>Gunakan tombol Reset untuk menghapus filter</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Produk</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada produk yang ingin diubah</li>
            <li>Ubah nama, deskripsi, atau spesifikasi produk</li>
            <li>Atur harga dan unit produk</li>
            <li>Pilih kategori yang sesuai</li>
            <li>Atur status unggulan atau terlaris</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Produk Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Produk</span></li>
            <li>Isi nama produk yang deskriptif (wajib)</li>
            <li>Masukkan slug unik untuk URL</li>
            <li>Atur harga dan unit (pcs, kg, dll)</li>
            <li>Pilih kategori yang relevan</li>
            <li>Upload gambar produk berkualitas</li>
            <li>Atur status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Category Management:</strong> Multi-kategori per produk</li>
            <li><strong>Featured Products:</strong> Tandai produk unggulan</li>
            <li><strong>Bestseller:</strong> Tandai produk terlaris</li>
            <li><strong>Price Control:</strong> Atur harga dan unit</li>
            <li><strong>Image Gallery:</strong> Multiple gambar per produk</li>
            <li><strong>SEO Friendly:</strong> Slug dan meta tags</li>
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
        var table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.products.data") }}',
                data: function(d){
                    d.category_id = $('#filter-category').val();
                    d.featured = $('#filter-featured').val();
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
                { data: 'name', name: 'name' },
                { data: 'categories', name: 'categories', orderable: false, searchable: false },
                { data: 'price', name: 'price' },
                { data: 'unit', name: 'unit' },
                { data: 'is_featured', name: 'is_featured', orderable: false, searchable: false },
                { data: 'is_bestseller', name: 'is_bestseller', orderable: false, searchable: false },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
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
                $('#products-table_filter').hide();
                
                // Update stats from initial data
                updateStats();
            }
        });

        // Custom search (using built-in DataTable search)
        $('#searchInput').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Category and featured filters
        $('#filter-category, #filter-featured, #filter-store').on('change', function(){
            table.ajax.reload();
        });

        // Clear filters
        $('#btn-clear-filters').on('click', function(e){
            e.preventDefault();
            $('#searchInput').val('');
            $('#filter-category').val('');
            $('#filter-featured').val('');
            $('#filter-store').val('');
            table.search('').draw();
            table.ajax.reload();
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.delete-product', function(e) {
            e.preventDefault();
            var productId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Produk ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.products.destroy", ":id") }}'.replace(':id', productId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Produk berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus produk.',
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
                url: '{{ route("admin.products.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000, store_id: $('#filter-store').val() },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var featured = 0;
                    var bestseller = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(product) {
                            if (product.is_active == '1' || product.is_active == true) {
                                active++;
                            }
                            if (product.is_featured && product.is_featured !== '') {
                                featured++;
                            }
                            if (product.is_bestseller && product.is_bestseller !== '') {
                                bestseller++;
                            }
                        });
                    }
                    
                    $('#totalProducts').text(total);
                    $('#activeProducts').text(active);
                    $('#featuredProducts').text(featured);
                    $('#bestsellerProducts').text(bestseller);
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
