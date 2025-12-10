@extends('admin.layouts.app')

@section('title', 'Kelola Halaman')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-file me-2"></i>Kelola Halaman
          </h4>
          <p class="text-muted mb-0">Buat dan kelola halaman statis website Anda dengan mudah</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Tambah Halaman
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
              <h6 class="card-title">Total Halaman</h6>
              <h3 class="mb-0" id="totalPages">0</h3>
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
              <h6 class="card-title">Diterbitkan</h6>
              <h3 class="mb-0" id="publishedPages">0</h3>
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
              <h3 class="mb-0" id="draftPages">0</h3>
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
              <h6 class="card-title">Dengan Gambar</h6>
              <h3 class="mb-0" id="pagesWithImages">0</h3>
            </div>
            <i class="bx bx-image bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari halaman berdasarkan judul atau konten...">
          </div>
        </div>
        <div class="col-md-4">
          <select id="statusFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="published">Diterbitkan</option>
            <option value="draft">Draft</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Pages Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Halaman
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="pages-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="25%">Judul Halaman</th>
            <th width="20%">Slug</th>
            <th width="15%">Status</th>
            <th width="15%">Dibuat</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Halaman</h6>
          <ul class="mb-3">
            <li>Ketik judul halaman di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Published/Draft)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Halaman</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada halaman yang ingin diubah</li>
            <li>Ubah judul, konten, atau pengaturan lainnya</li>
            <li>Gunakan editor rich text untuk format konten</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Halaman Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Halaman</span></li>
            <li>Isi judul halaman (wajib)</li>
            <li>Tulis konten menggunakan editor rich text</li>
            <li>Upload gambar unggulan (opsional)</li>
            <li>Atur SEO meta tags (opsional)</li>
            <li>Pilih status publikasi</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Rich Text Editor:</strong> Format teks, gambar, link, tabel</li>
            <li><strong>Gambar Unggulan:</strong> Upload gambar untuk halaman</li>
            <li><strong>Lampiran:</strong> Upload file PDF, DOC, dll</li>
            <li><strong>SEO Settings:</strong> Meta title dan description</li>
            <li><strong>Video Embed:</strong> Embed video YouTube/Vimeo</li>
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
        var table = $('#pages-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.pages.data") }}',
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
                { data: 'slug', name: 'slug', orderable: false },
                { data: 'status', name: 'status', orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
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
                $('#pages-table_filter').hide();
            }
        });

        // Custom search (using built-in DataTable search)
        $('#searchInput').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.delete-page', function(e) {
            e.preventDefault();
            var pageId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Halaman?',
                text: "Halaman ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.pages.destroy", ":id") }}'.replace(':id', pageId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Halaman berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus halaman.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

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
