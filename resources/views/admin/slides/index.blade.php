@extends('admin.layouts.app')

@section('title', 'Kelola Slide')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-image me-2 text-primary"></i>Slide Carousel: {{ $parent->name }}
          </h4>
          <p class="text-muted mb-0">Kelola slide untuk carousel "{{ $parent->name }}"</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
          <a href="{{ route('admin.carousels.index') }}" class="btn btn-secondary btn-modern">
            <i class="bx bx-arrow-back me-1"></i>Kembali ke Carousel
          </a>
          <a href="{{ route('admin.slides.create', $parent->id) }}" class="btn btn-primary btn-modern">
            <i class="bx bx-plus me-1"></i>Tambah Slide
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
              <p class="stat-label mb-2">Total Slide</p>
              <h3 class="stat-value mb-0" id="totalSlides">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-image"></i>
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
              <h3 class="stat-value mb-0" id="activeSlides">0</h3>
            </div>
            <i class="bx bx-show bx-lg"></i>
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
              <h3 class="stat-value mb-0" id="inactiveSlides">0</h3>
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
              <p class="stat-label mb-2">Carousel Key</p>
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
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari slide berdasarkan judul atau subtitle...">
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

  <!-- Slides Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Slide
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="slides-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="20%">Judul</th>
            <th width="20%">Subtitle</th>
            <th width="10%">Tombol</th>
            <th width="10%">Gambar</th>
            <th width="8%">Urutan</th>
            <th width="9%">Status</th>
            <th width="18%">Aksi</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Slide</h6>
          <ul class="mb-3">
            <li>Ketik judul atau subtitle slide di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Slide</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada slide yang ingin diubah</li>
            <li>Ubah judul, subtitle, atau teks tombol</li>
            <li>Ganti gambar slide jika perlu</li>
            <li>Atur ulang urutan tampilan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Menambah Slide Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Slide</span></li>
            <li>Isi judul slide yang menarik (wajib)</li>
            <li>Masukkan subtitle atau deskripsi</li>
            <li>Upload gambar slide berkualitas tinggi</li>
            <li>Atur teks dan URL tombol</li>
            <li>Tentukan urutan tampilan</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Image Upload:</strong> Gambar slide berkualitas</li>
            <li><strong>Text Overlay:</strong> Judul dan subtitle</li>
            <li><strong>Call to Action:</strong> Tombol dengan URL</li>
            <li><strong>Order Management:</strong> Atur urutan tampilan</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif slide</li>
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
        var table = $('#slides-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.slides.data", $parent->id) }}',
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
            order: [[5, 'asc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'subtitle', name: 'subtitle' },
                { data: 'button_text', name: 'button_text' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'sort_order', name: 'sort_order' },
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
                $('#slides-table_filter').hide();
                
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
                table.column(6).search('').draw();
            } else {
                table.column(6).search(status).draw();
            }
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.delete-slide', function(e) {
            e.preventDefault();
            var slideId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Slide?',
                text: "Slide ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.slides.destroy", ":id") }}'.replace(':id', slideId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Slide berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus slide.',
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
                url: '{{ route("admin.slides.data", $parent->id) }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(slide) {
                            if (slide.is_active == '1' || slide.is_active == true) {
                                active++;
                            }
                        });
                    }
                    
                    $('#totalSlides').text(total);
                    $('#activeSlides').text(active);
                    $('#inactiveSlides').text(total - active);
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
