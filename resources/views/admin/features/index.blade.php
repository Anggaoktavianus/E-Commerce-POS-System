@extends('admin.layouts.app')

@section('title', 'Kelola Fitur')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-star me-2 text-primary"></i>Kelola Fitur
          </h4>
          <p class="text-muted mb-0">Buat dan kelola fitur unggulan website Anda dengan mudah</p>
        </div>
        <a href="{{ route('admin.features.create') }}" class="btn btn-primary btn-modern mt-2 mt-md-0">
          <i class="bx bx-plus me-1"></i>Tambah Fitur
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
              <p class="stat-label mb-2">Total Fitur</p>
              <h3 class="stat-value mb-0" id="totalFeatures">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-star"></i>
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
              <h3 class="stat-value mb-0" id="activeFeatures">0</h3>
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
              <h3 class="stat-value mb-0" id="inactiveFeatures">0</h3>
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
              <p class="stat-label mb-2">Dengan Gambar</p>
              <h3 class="stat-value mb-0" id="featuresWithImages">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-image"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari fitur berdasarkan judul atau deskripsi...">
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

  <!-- Features Table -->
  <div class="card card-modern">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold">
        <i class="bx bx-list-ul me-2"></i>Daftar Fitur
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover table-modern" id="features-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="25%">Judul Fitur</th>
            <th width="30%">Deskripsi</th>
            <th width="10%">Icon</th>
            <th width="10%">Status</th>
            <th width="10%">Urutan</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Fitur</h6>
          <ul class="mb-3">
            <li>Ketik judul fitur di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Fitur</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada fitur yang ingin diubah</li>
            <li>Ubah judul, deskripsi, atau pengaturan lainnya</li>
            <li>Pilih icon yang sesuai</li>
            <li>Upload gambar jika diperlukan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Fitur Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Fitur</span></li>
            <li>Isi judul fitur (wajib)</li>
            <li>Tulis deskripsi singkat tentang fitur</li>
            <li>Pilih icon yang mewakili fitur</li>
            <li>Upload gambar pendukung (opsional)</li>
            <li>Atur urutan tampilan</li>
            <li>Pilih status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Icon Selection:</strong> Pilih dari berbagai icon</li>
            <li><strong>Image Upload:</strong> Upload gambar untuk fitur</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif fitur</li>
            <li><strong>Ordering:</strong> Atur urutan tampilan fitur</li>
            <li><strong>Rich Description:</strong> Deskripsi detail fitur</li>
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
        var table = $('#features-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.features.data") }}',
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
                { data: 'title', name: 'title' },
                { data: 'description', name: 'description', orderable: false },
                { data: 'icon_class', name: 'icon_class', orderable: false, searchable: false, render: function(data) { return data ? `<i class="${data}"></i>` : ''; } },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
                { data: 'sort_order', name: 'sort_order' },
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
                $('#features-table_filter').hide();
                
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
        $(document).on('click', '.delete-feature', function(e) {
            e.preventDefault();
            var featureId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Fitur?',
                text: "Fitur ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.features.destroy", ":id") }}'.replace(':id', featureId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Fitur berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus fitur.',
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
                url: '{{ route("admin.features.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var withImages = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(feature) {
                            if (feature.is_active == '1' || feature.is_active == true) {
                                active++;
                            }
                            if (feature.image && feature.image !== null && feature.image !== '') {
                                withImages++;
                            }
                        });
                    }
                    
                    $('#totalFeatures').text(total);
                    $('#activeFeatures').text(active);
                    $('#inactiveFeatures').text(total - active);
                    $('#featuresWithImages').text(withImages);
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
