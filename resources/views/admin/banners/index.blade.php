@extends('admin.layouts.app')

@section('title', 'Kelola Banner')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-image me-2"></i>Kelola Banner
          </h4>
          <p class="text-muted mb-0">Buat dan kelola banner promosi untuk meningkatkan konversi</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Tambah Banner
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
              <h6 class="card-title">Total Banner</h6>
              <h3 class="mb-0" id="totalBanners">0</h3>
            </div>
            <i class="bx bx-image bx-lg"></i>
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
              <h3 class="mb-0" id="activeBanners">0</h3>
            </div>
            <i class="bx bx-show bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Non-Aktif</h6>
              <h3 class="mb-0" id="inactiveBanners">0</h3>
            </div>
            <i class="bx bx-hide bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Dengan Gambar</h6>
              <h3 class="mb-0" id="bannersWithImages">0</h3>
            </div>
            <i class="bx bx-photo-album bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari banner berdasarkan judul atau subtitle...">
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

  <!-- Banners Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Banner
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="banners-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="20%">Judul</th>
            <th width="25%">Subtitle</th>
            <th width="10%">Gambar</th>
            <th width="10%">Posisi</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Banner</h6>
          <ul class="mb-3">
            <li>Ketik judul banner di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Banner</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada banner yang ingin diubah</li>
            <li>Ubah judul, subtitle, atau teks tombol</li>
            <li>Upload gambar banner yang menarik</li>
            <li>Atur posisi dan urutan tampilan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Banner Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Banner</span></li>
            <li>Isi judul banner yang menarik (wajib)</li>
            <li>Masukkan subtitle atau deskripsi pendek</li>
            <li>Upload gambar banner dengan ukuran optimal</li>
            <li>Atur teks dan URL tombol</li>
            <li>Pilih posisi banner (Header, Footer, dll)</li>
            <li>Pilih status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Image Upload:</strong> Upload gambar banner berkualitas</li>
            <li><strong>Position Control:</strong> Atur posisi banner di website</li>
            <li><strong>Button Customization:</strong> Custom teks dan URL tombol</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif banner</li>
            <li><strong>Ordering:</strong> Atur urutan tampilan banner</li>
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
        var table = $('#banners-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.banners.data") }}',
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
                { data: 'subtitle', name: 'subtitle' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'position', name: 'position' },
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
                $('#banners-table_filter').hide();
                
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
                table.column(5).search('').draw();
            } else {
                table.column(5).search(status).draw();
            }
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.delete-banner', function(e) {
            e.preventDefault();
            var bannerId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Banner?',
                text: "Banner ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.banners.destroy", ":id") }}'.replace(':id', bannerId),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Banner berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus banner.',
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
                url: '{{ route("admin.banners.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var withImages = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(banner) {
                            if (banner.is_active == '1' || banner.is_active == true) {
                                active++;
                            }
                            if (banner.image_path && banner.image_path !== null && banner.image_path !== '') {
                                withImages++;
                            }
                        });
                    }
                    
                    $('#totalBanners').text(total);
                    $('#activeBanners').text(active);
                    $('#inactiveBanners').text(total - active);
                    $('#bannersWithImages').text(withImages);
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
