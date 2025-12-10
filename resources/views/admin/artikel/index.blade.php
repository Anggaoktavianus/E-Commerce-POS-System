@extends('admin.layouts.app')

@section('title', 'Kelola Artikel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-file me-2"></i>Kelola Artikel
          </h4>
          <p class="text-muted mb-0">Buat dan kelola artikel untuk website</p>
        </div>
        <a href="{{ route('admin.artikel.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Tambah Artikel
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
              <h6 class="card-title">Total Artikel</h6>
              <h3 class="mb-0" id="totalArtikel">0</h3>
            </div>
            <i class="bx bx-file bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Published</h6>
              <h3 class="mb-0" id="publishedArtikel">0</h3>
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
              <h6 class="card-title">Draft</h6>
              <h3 class="mb-0" id="draftArtikel">0</h3>
            </div>
            <i class="bx bx-edit bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Total Views</h6>
              <h3 class="mb-0" id="totalViews">0</h3>
            </div>
            <i class="bx bx-show-alt bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari artikel...">
          </div>
        </div>
        <div class="col-md-3">
          <select id="statusFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="archived">Archived</option>
          </select>
        </div>
        <div class="col-md-3">
          <select id="kategoriFilter" class="form-select">
            <option value="">Semua Kategori</option>
            @foreach($kategori ?? [] as $kat)
              <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
            <i class="bx bx-refresh me-1"></i> Reset
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Articles Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Artikel
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="artikel-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="15%">Gambar</th>
            <th width="25%">Judul</th>
            <th width="15%">Kategori</th>
            <th width="10%">Status</th>
            <th width="10%">Author</th>
            <th width="10%">Tanggal</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Artikel</h6>
          <ul class="mb-3">
            <li>Ketik judul atau konten artikel di kotak pencarian</li>
            <li>Filter berdasarkan status (Draft/Published/Archived)</li>
            <li>Filter berdasarkan kategori artikel</li>
            <li>Pencarian bersifat real-time</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Status Artikel</h6>
          <ul class="mb-3">
            <li><span class="badge bg-secondary">Draft</span> - Artikel masih dalam proses penulisan</li>
            <li><span class="badge bg-success">Published</span> - Artikel sudah tayang di website</li>
            <li><span class="badge bg-warning">Archived</span> - Artikel diarsipkan, tidak tayang</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Artikel Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Artikel</span></li>
            <li>Isi judul artikel yang menarik dan SEO-friendly</li>
            <li>Pilih kategori yang sesuai dengan konten</li>
            <li>Tulis konten artikel dengan format yang baik</li>
            <li>Upload gambar utama dan thumbnail (opsional)</li>
            <li>Set meta tags untuk SEO</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Rich Content:</strong> Support HTML dan media</li>
            <li><strong>SEO Optimized:</strong> Meta tags dan slug otomatis</li>
            <li><strong>Image Upload:</strong> Gambar utama dan thumbnail</li>
            <li><strong>View Counter:</strong> Track jumlah pembaca</li>
            <li><strong>Author Tracking:</strong> Info penulis artikel</li>
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
        var table = $('#artikel-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.artikel.data") }}',
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
                { data: 'gambar_utama', name: 'gambar_utama', orderable: false, searchable: false, className: 'text-center' },
                { data: 'judul', name: 'judul' },
                { data: 'kategori', name: 'kategori', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'user.name', name: 'user.name', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
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
                $('#artikel-table_filter').hide();
                
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
            table.column(4).search(status).draw();
        });

        // Kategori filter
        $('#kategoriFilter').on('change', function() {
            var kategori = $(this).val();
            if (kategori === '') {
                table.column(3).search('').draw();
            } else {
                table.column(3).search(kategori).draw();
            }
        });

        // Reset filters
        window.resetFilters = function() {
            $('#searchInput').val('');
            $('#statusFilter').val('');
            $('#kategoriFilter').val('');
            table.search('').columns().search('').draw();
        };

        // Handle delete with SweetAlert
        window.deleteItem = function(id) {
            Swal.fire({
                title: 'Hapus Artikel?',
                text: "Artikel ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.artikel.destroy", ":id") }}'.replace(':id', id),
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
                                'Terjadi kesalahan saat menghapus artikel.',
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
                url: '{{ route("admin.artikel.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var published = 0;
                    var draft = 0;
                    var totalViews = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(artikel) {
                            if (artikel.status === 'published') {
                                published++;
                            } else if (artikel.status === 'draft') {
                                draft++;
                            }
                            if (artikel.views) {
                                totalViews += artikel.views;
                            }
                        });
                    }
                    
                    $('#totalArtikel').text(total);
                    $('#publishedArtikel').text(published);
                    $('#draftArtikel').text(draft);
                    $('#totalViews').text(totalViews.toLocaleString());
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
