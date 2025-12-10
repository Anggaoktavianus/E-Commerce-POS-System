@extends('admin.layouts.app')

@section('title', 'Kelola Menu')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-menu me-2"></i>Kelola Menu Navigasi
          </h4>
          <p class="text-muted mb-0">Kelola menu navigasi website dan konfigurasi link</p>
        </div>
        <div>
          <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Menu
          </a>
        </div>
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
              <h6 class="card-title">Total Menu</h6>
              <h3 class="mb-0" id="totalMenus">0</h3>
            </div>
            <i class="bx bx-menu bx-lg"></i>
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
              <h3 class="mb-0" id="activeMenus">0</h3>
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
              <h3 class="mb-0" id="inactiveMenus">0</h3>
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
              <h6 class="card-title">Total Links</h6>
              <h3 class="mb-0" id="totalLinks">0</h3>
            </div>
            <i class="bx bx-link bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari menu berdasarkan nama atau lokasi...">
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

  <!-- Menus Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Menu Navigasi
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="menus-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="30%">Nama Menu</th>
            <th width="20%">Lokasi</th>
            <th width="10%">Status</th>
            <th width="15%">Links</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Menu</h6>
          <ul class="mb-3">
            <li>Ketik nama atau lokasi menu di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Menu</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada menu yang ingin diubah</li>
            <li>Ubah nama menu atau lokasi tampilan</li>
            <li>Aktifkan/non-aktifkan menu</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Menambah Menu Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Menu</span></li>
            <li>Isi nama menu yang akan ditampilkan</li>
            <li>Pilih lokasi menu (header, footer, dll)</li>
            <li>Atur status menu (aktif/non-aktif)</li>
            <li>Setelah dibuat, tambahkan link menu</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Menu Management:</strong> Kelola menu navigasi</li>
            <li><strong>Location Control:</strong> Header, footer, sidebar</li>
            <li><strong>Link Management:</strong> Submenu dan link items</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif menu</li>
            <li><strong>Dynamic Display:</strong> Update otomatis di website</li>
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
        var table = $('#menus-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.menus.data") }}',
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
                { data: 'location', name: 'location' },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
                { data: 'links', name: 'links', orderable: false, searchable: false },
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
                $('#menus-table_filter').hide();
                
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
        $(document).on('click', '.delete-menu', function(e) {
            e.preventDefault();
            var menuId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Menu?',
                text: "Menu ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.menus.destroy", ":id") }}'.replace(':id', menuId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Menu berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus menu.',
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
                url: '{{ route("admin.menus.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(menu) {
                            if (menu.is_active == '1' || menu.is_active == true) {
                                active++;
                            }
                        });
                    }
                    
                    $('#totalMenus').text(total);
                    $('#activeMenus').text(active);
                    $('#inactiveMenus').text(total - active);
                    
                    // Count total links (would need separate API call for accurate count)
                    $('#totalLinks').text('0');
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
