@extends('admin.layouts.app')

@section('title', 'Kelola Media Sosial')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bxl-linkedin me-2"></i>Kelola Media Sosial
          </h4>
          <p class="text-muted mb-0">Buat dan kelola link media sosial untuk koneksi dengan pelanggan</p>
        </div>
        <a href="{{ route('admin.social_links.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Tambah Media Sosial
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
              <h6 class="card-title">Total Media Sosial</h6>
              <h3 class="mb-0" id="totalSocialLinks">0</h3>
            </div>
            <i class="bx bxl-linkedin bx-lg"></i>
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
              <h3 class="mb-0" id="activeSocialLinks">0</h3>
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
              <h3 class="mb-0" id="inactiveSocialLinks">0</h3>
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
              <h6 class="card-title">Platform Types</h6>
              <h3 class="mb-0" id="platformTypes">0</h3>
            </div>
            <i class="bx bx-category bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari media sosial berdasarkan platform atau URL...">
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

  <!-- Social Links Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Media Sosial
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="social-links-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="25%">Platform</th>
            <th width="15%">Icon</th>
            <th width="30%">URL</th>
            <th width="10%">Status</th>
            <th width="10%">Urutan</th>
            <th width="5%">Aksi</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Media Sosial</h6>
          <ul class="mb-3">
            <li>Ketik nama platform di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Media Sosial</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada media sosial yang ingin diubah</li>
            <li>Ubah platform, URL, atau icon</li>
            <li>Pilih icon yang sesuai untuk platform</li>
            <li>Atur urutan tampilan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Media Sosial Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Media Sosial</span></li>
            <li>Pilih platform (Facebook, Instagram, Twitter, dll)</li>
            <li>Masukkan URL lengkap media sosial</li>
            <li>Pilih icon yang mewakili platform</li>
            <li>Atur urutan tampilan</li>
            <li>Pilih status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Platform Support:</strong> Facebook, Instagram, Twitter, LinkedIn, dll</li>
            <li><strong>Icon Selection:</strong> Icon khusus untuk setiap platform</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif media sosial</li>
            <li><strong>Ordering:</strong> Atur urutan tampilan</li>
            <li><strong>URL Validation:</strong> Validasi format URL</li>
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
        var table = $('#social-links-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.social_links.data") }}',
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
                { data: 'platform', name: 'platform' },
                { data: 'icon_class', name: 'icon_class', orderable: false, searchable: false, render: function(data) { return data ? `<i class="${data}"></i>` : ''; } },
                { data: 'url', name: 'url' },
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
                $('#social-links-table_filter').hide();
                
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
        $(document).on('click', '.delete-social-link', function(e) {
            e.preventDefault();
            var socialLinkId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Media Sosial?',
                text: "Media sosial ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.social_links.destroy", ":id") }}'.replace(':id', socialLinkId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Media sosial berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus media sosial.',
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
                url: '{{ route("admin.social_links.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var platforms = new Set();
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(socialLink) {
                            if (socialLink.is_active == '1' || socialLink.is_active == true) {
                                active++;
                            }
                            if (socialLink.platform) {
                                platforms.add(socialLink.platform);
                            }
                        });
                    }
                    
                    $('#totalSocialLinks').text(total);
                    $('#activeSocialLinks').text(active);
                    $('#inactiveSocialLinks').text(total - active);
                    $('#platformTypes').text(platforms.size);
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
