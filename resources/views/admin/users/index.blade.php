@extends('admin.layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-user me-2"></i>Kelola Pengguna
          </h4>
          <p class="text-muted mb-0">Buat dan kelola pengguna sistem dengan berbagai peran akses</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Tambah Pengguna
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
              <h6 class="card-title">Total Pengguna</h6>
              <h3 class="mb-0" id="totalUsers">0</h3>
            </div>
            <i class="bx bx-user bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Terverifikasi</h6>
              <h3 class="mb-0" id="verifiedUsers">0</h3>
            </div>
            <i class="bx bx-user-check bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Menunggu</h6>
              <h3 class="mb-0" id="pendingUsers">0</h3>
            </div>
            <i class="bx bx-user-x bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Admin</h6>
              <h3 class="mb-0" id="adminUsers">0</h3>
            </div>
            <i class="bx bx-shield bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Filter -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari pengguna berdasarkan nama atau email...">
          </div>
        </div>
        <div class="col-md-3">
          <select id="filter-role" class="form-select">
            <option value="">Semua Peran</option>
            <option value="admin">Admin</option>
            <option value="mitra">Mitra</option>
            <option value="customer">Customer</option>
          </select>
        </div>
        <div class="col-md-3">
          <select id="statusFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="1">Terverifikasi</option>
            <option value="0">Menunggu</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Pengguna
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="users-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="25%">Nama</th>
            <th width="25%">Email</th>
            <th width="15%">Peran</th>
            <th width="15%">Status</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Pengguna</h6>
          <ul class="mb-3">
            <li>Ketik nama atau email pengguna di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan peran (Admin/Mitra/Customer)</li>
            <li>Filter berdasarkan status verifikasi</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Pengguna</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada pengguna yang ingin diubah</li>
            <li>Ubah nama, email, atau peran pengguna</li>
            <li>Atur status verifikasi untuk mitra</li>
            <li>Perubahan akan langsung tersimpan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Pengguna Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Pengguna</span></li>
            <li>Isi nama lengkap pengguna (wajib)</li>
            <li>Masukkan email yang valid dan unik</li>
            <li>Pilih peran pengguna (Admin/Mitra/Customer)</li>
            <li>Atur password dan konfirmasi</li>
            <li>Pilih status verifikasi jika diperlukan</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Role Management:</strong> Admin, Mitra, Customer</li>
            <li><strong>Verification System:</strong> Verifikasi mitra</li>
            <li><strong>Access Control:</strong> Hak akses berbeda per peran</li>
            <li><strong>User Management:</strong> Kelola semua pengguna</li>
            <li><strong>Security:</strong> Password dan verifikasi</li>
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
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.users.data") }}',
                data: function(d){
                    d.role = $('#filter-role').val();
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
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { data: 'is_verified', name: 'is_verified', orderable: false, searchable: false },
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
                $('#users-table_filter').hide();
                
                // Update stats from initial data
                updateStats();
            }
        });

        // Custom search (using built-in DataTable search)
        $('#searchInput').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Role filter
        $('#filter-role').on('change', function(){
            table.ajax.reload();
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
        $(document).on('click', '.delete-user', function(e) {
            e.preventDefault();
            var userId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: "Pengguna ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.users.destroy", ":id") }}'.replace(':id', userId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Pengguna berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus pengguna.',
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
                url: '{{ route("admin.users.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var verified = 0;
                    var admin = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(user) {
                            if (user.is_verified == '1' || user.is_verified == true) {
                                verified++;
                            }
                            if (user.role === 'admin') {
                                admin++;
                            }
                        });
                    }
                    
                    $('#totalUsers').text(total);
                    $('#verifiedUsers').text(verified);
                    $('#pendingUsers').text(total - verified);
                    $('#adminUsers').text(admin);
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
