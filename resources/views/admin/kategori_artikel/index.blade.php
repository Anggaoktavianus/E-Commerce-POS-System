@extends('admin.layouts.app')

@section('title', 'Kelola Kategori Artikel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-category me-2 text-primary"></i>Kelola Kategori Artikel
          </h4>
          <p class="text-muted mb-0">Buat dan kelola kategori artikel untuk organisasi konten yang lebih baik</p>
        </div>
        <a href="{{ route('admin.kategori_artikel.create') }}" class="btn btn-primary btn-modern mt-2 mt-md-0">
          <i class="bx bx-plus me-1"></i>Tambah Kategori
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
              <p class="stat-label mb-2">Total Kategori</p>
              <h3 class="stat-value mb-0 text-white" id="totalCategories">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-category"></i>
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
              <h3 class="stat-value mb-0 text-white" id="activeCategories">0</h3>
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
              <h3 class="stat-value mb-0 text-white" id="inactiveCategories">0</h3>
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
              <p class="stat-label mb-2">Total Artikel</p>
              <h3 class="stat-value mb-0 text-white" id="totalArtikel">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-file"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Filter -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari kategori berdasarkan nama atau deskripsi...">
          </div>
        </div>
        <div class="col-md-4">
          <select id="statusFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Non-Aktif</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Kategori Artikel Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Kategori Artikel
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="kategori-artikel-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="15%">Gambar</th>
            <th width="25%">Nama</th>
            <th width="20%">Slug</th>
            <th width="10%">Status</th>
            <th width="10%">Artikel</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Kategori</h6>
          <ul class="mb-3">
            <li>Ketik nama kategori di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>

          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Kategori</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-warning">Edit</span> pada kategori yang ingin diubah</li>
            <li>Ubah nama, deskripsi, atau gambar kategori</li>
            <li>Atur status aktif/non-aktif</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Kategori Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Kategori</span></li>
            <li>Isi nama kategori yang deskriptif (wajib)</li>
            <li>Masukkan deskripsi kategori</li>
            <li>Upload gambar kategori (opsional)</li>
            <li>Pilih status aktif/non-aktif</li>
          </ul>

          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>SEO Friendly:</strong> Slug untuk URL yang bersih</li>
            <li><strong>Image Support:</strong> Upload gambar kategori</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif kategori</li>
            <li><strong>Article Count:</strong> Hitung jumlah artikel per kategori</li>
            <li><strong>Easy Navigation:</strong> Struktur kategori yang jelas</li>
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
        var table = $('#kategori-artikel-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.kategori_artikel.data") }}',
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
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'gambar', name: 'gambar', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama', name: 'nama' },
                { data: 'slug', name: 'slug' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'artikel_count', name: 'artikel_count', orderable: false, searchable: false, className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
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
                $('#kategori-artikel-table_filter').hide();

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

        // Handle delete with SweetAlert
        window.deleteItem = function(id) {
            Swal.fire({
                title: 'Hapus Kategori Artikel?',
                text: "Kategori ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.kategori_artikel.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                );
                                table.ajax.reload();
                                updateStats();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus kategori.',
                                'error'
                            );
                        }
                    });
                }
            });
        };

        // Update statistics function
        function updateStats() {
            $.ajax({
                url: '{{ route("admin.kategori_artikel.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var totalArtikel = 0;

                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(kategori) {
                            if (kategori.status === 'aktif') {
                                active++;
                            }
                            if (kategori.artikels_count) {
                                totalArtikel += kategori.artikels_count;
                            }
                        });
                    }

                    $('#totalCategories').text(total);
                    $('#activeCategories').text(active);
                    $('#inactiveCategories').text(total - active);
                    $('#totalArtikel').text(totalArtikel);
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
