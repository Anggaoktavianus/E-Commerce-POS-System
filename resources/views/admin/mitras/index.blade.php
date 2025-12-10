@extends('admin.layouts.app')

@section('title', 'Kelola Mitra')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-user me-2"></i>Kelola Mitra
          </h4>
          <p class="text-muted mb-0">Kelola mitra dan verifikasi status kerjasama</p>
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
              <h6 class="card-title">Total Mitra</h6>
              <h3 class="mb-0" id="totalMitra">0</h3>
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
              <h3 class="mb-0" id="verifiedMitra">0</h3>
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
              <h3 class="mb-0" id="pendingMitra">0</h3>
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
              <h6 class="card-title">Bulan Ini</h6>
              <h3 class="mb-0" id="monthlyMitra">0</h3>
            </div>
            <i class="bx bx-calendar bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari mitra berdasarkan nama atau email...">
          </div>
        </div>
        <div class="col-md-4">
          <select id="statusFilter" class="form-select">
            <option value="">Semua Status</option>
            <option value="1">Terverifikasi</option>
            <option value="0">Menunggu</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Mitras Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Mitra
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="mitras-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="20%">Nama</th>
            <th width="20%">Email</th>
            <th width="15%">Telepon</th>
            <th width="20%">Alamat</th>
            <th width="10%">Status</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Mitra</h6>
          <ul class="mb-3">
            <li>Ketik nama atau email mitra di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status verifikasi</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-user-check text-success me-2"></i>Cara Memverifikasi Mitra</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-success">Verifikasi</span> pada mitra yang ingin diverifikasi</li>
            <li>Periksa data mitra sebelum verifikasi</li>
            <li>Status akan berubah menjadi "Terverifikasi"</li>
            <li>Mitra dapat membatalkan verifikasi kapan saja</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-info-circle text-info me-2"></i>Informasi Mitra</h6>
          <ul class="mb-3">
            <li><strong>Data Pribadi:</strong> Nama, email, telepon, alamat</li>
            <li><strong>Status Verifikasi:</strong> Terverifikasi/Menunggu</li>
            <li><strong>Tanggal Bergabung:</strong> Waktu pendaftaran</li>
            <li><strong>Akses Sistem:</strong> Khusus untuk mitra terverifikasi</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Verification System:</strong> Verifikasi mitra</li>
            <li><strong>Partner Management:</strong> Kelola mitra</li>
            <li><strong>Status Tracking:</strong> Pantau status verifikasi</li>
            <li><strong>Contact Info:</strong> Informasi kontak lengkap</li>
            <li><strong>Search Filter:</strong> Pencarian dan filter</li>
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
        var table = $('#mitras-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.mitras.data") }}',
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
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
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
                $('#mitras-table_filter').hide();
                
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

        // Update statistics function
        function updateStats() {
            $.ajax({
                url: '{{ route("admin.mitras.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var verified = 0;
                    var monthly = 0;
                    var currentMonth = new Date().getMonth();
                    var currentYear = new Date().getFullYear();
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(mitra) {
                            if (mitra.is_verified == '1' || mitra.is_verified == true) {
                                verified++;
                            }
                            
                            // Check if created this month
                            var createdDate = new Date(mitra.created_at);
                            if (createdDate.getMonth() === currentMonth && createdDate.getFullYear() === currentYear) {
                                monthly++;
                            }
                        });
                    }
                    
                    $('#totalMitra').text(total);
                    $('#verifiedMitra').text(verified);
                    $('#pendingMitra').text(total - verified);
                    $('#monthlyMitra').text(monthly);
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
