@extends('admin.layouts.app')

@section('title', 'Kelola Testimoni')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-comment-dots me-2 text-primary"></i>Kelola Testimoni
          </h4>
          <p class="text-muted mb-0">Buat dan kelola testimoni pelanggan untuk meningkatkan kepercayaan</p>
        </div>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-modern mt-2 mt-md-0">
          <i class="bx bx-plus me-1"></i>Tambah Testimoni
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
              <p class="stat-label mb-2">Total Testimoni</p>
              <h3 class="stat-value mb-0" id="totalTestimonials">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-comment-dots"></i>
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
              <h3 class="stat-value mb-0" id="activeTestimonials">0</h3>
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
              <h3 class="stat-value mb-0" id="inactiveTestimonials">0</h3>
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
              <p class="stat-label mb-2">Rating 5 Bintang</p>
              <h3 class="stat-value mb-0" id="fiveStarTestimonials">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-star"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari testimoni berdasarkan nama atau konten...">
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

  <!-- Testimonials Table -->
  <div class="card card-modern">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold">
        <i class="bx bx-list-ul me-2"></i>Daftar Testimoni
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover table-modern" id="testimonials-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="25%">Nama</th>
            <th width="20%">Jabatan</th>
            <th width="15%">Rating</th>
            <th width="10%">Status</th>
            <th width="10%">Urutan</th>
            <th width="15%">Aksi</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

  <!-- Help Guide -->
  <div class="card card-modern mt-4">
    <div class="card-header">
      <h5 class="card-title mb-0 fw-bold">
        <i class="bx bx-help-circle me-2"></i>Panduan Penggunaan
      </h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Testimoni</h6>
          <ul class="mb-3">
            <li>Ketik nama pelanggan di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Testimoni</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada testimoni yang ingin diubah</li>
            <li>Ubah nama, jabatan, atau konten testimoni</li>
            <li>Atur rating bintang (1-5)</li>
            <li>Upload foto pelanggan jika diperlukan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Testimoni Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Testimoni</span></li>
            <li>Isi nama pelanggan (wajib)</li>
            <li>Masukkan jabatan atau perusahaan</li>
            <li>Tulis konten testimoni yang menarik</li>
            <li>Pilih rating bintang (1-5)</li>
            <li>Upload foto pelanggan (opsional)</li>
            <li>Pilih status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Rating System:</strong> Bintang 1-5 untuk kualitas</li>
            <li><strong>Photo Upload:</strong> Upload foto pelanggan</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif testimoni</li>
            <li><strong>Ordering:</strong> Atur urutan tampilan</li>
            <li><strong>Rich Content:</strong> Format teks untuk testimoni</li>
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
        var table = $('#testimonials-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.testimonials.data") }}',
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
                { data: 'author_name', name: 'author_name' },
                { data: 'author_title', name: 'author_title' },
                { data: 'rating', name: 'rating', orderable: false, searchable: false },
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
                $('#testimonials-table_filter').hide();
                
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
        $(document).on('click', '.delete-testimonial', function(e) {
            e.preventDefault();
            var testimonialId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Testimoni?',
                text: "Testimoni ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.testimonials.destroy", ":id") }}'.replace(':id', testimonialId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Testimoni berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus testimoni.',
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
                url: '{{ route("admin.testimonials.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var fiveStar = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(testimonial) {
                            if (testimonial.is_active == '1' || testimonial.is_active == true) {
                                active++;
                            }
                            if (testimonial.rating == '5') {
                                fiveStar++;
                            }
                        });
                    }
                    
                    $('#totalTestimonials').text(total);
                    $('#activeTestimonials').text(active);
                    $('#inactiveTestimonials').text(total - active);
                    $('#fiveStarTestimonials').text(fiveStar);
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
