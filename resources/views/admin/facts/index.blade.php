@extends('admin.layouts.app')

@section('title', 'Kelola Statistik & Fakta')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-bar-chart-alt me-2"></i>Kelola Statistik & Fakta
          </h4>
          <p class="text-muted mb-0">Buat dan kelola statistik perusahaan untuk menampilkan pencapaian</p>
        </div>
        <a href="{{ route('admin.facts.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Tambah Statistik
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
              <h6 class="card-title">Total Statistik</h6>
              <h3 class="mb-0" id="totalFacts">0</h3>
            </div>
            <i class="bx bx-bar-chart-alt bx-lg"></i>
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
              <h3 class="mb-0" id="activeFacts">0</h3>
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
              <h3 class="mb-0" id="inactiveFacts">0</h3>
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
              <h6 class="card-title">Dengan Icon</h6>
              <h3 class="mb-0" id="factsWithIcons">0</h3>
            </div>
            <i class="bx bx-star bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari statistik berdasarkan label atau nilai...">
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

  <!-- Facts Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Statistik & Fakta
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="facts-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="30%">Label</th>
            <th width="20%">Nilai</th>
            <th width="15%">Icon</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Statistik</h6>
          <ul class="mb-3">
            <li>Ketik label statistik di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Statistik</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada statistik yang ingin diubah</li>
            <li>Ubah label, nilai, atau icon</li>
            <li>Pilih icon yang sesuai dari library</li>
            <li>Atur urutan tampilan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Statistik Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Statistik</span></li>
            <li>Isi label statistik (wajib)</li>
            <li>Masukkan nilai/angka statistik</li>
            <li>Pilih icon yang mewakili statistik</li>
            <li>Atur urutan tampilan</li>
            <li>Pilih status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Icon Selection:</strong> Pilih dari berbagai icon</li>
            <li><strong>Value Display:</strong> Format angka atau teks</li>
            <li><strong>Status Control:</strong> Aktif/non-aktif statistik</li>
            <li><strong>Ordering:</strong> Atur urutan tampilan</li>
            <li><strong>Rich Display:</strong> Statistik menarik dengan icon</li>
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
        var table = $('#facts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.facts.data") }}',
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
                { data: 'label', name: 'label' },
                { data: 'value', name: 'value' },
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
                $('#facts-table_filter').hide();
                
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
        $(document).on('click', '.delete-fact', function(e) {
            e.preventDefault();
            var factId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Statistik?',
                text: "Statistik ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.facts.destroy", ":id") }}'.replace(':id', factId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Statistik berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus statistik.',
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
                url: '{{ route("admin.facts.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var withIcons = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(fact) {
                            if (fact.is_active == '1' || fact.is_active == true) {
                                active++;
                            }
                            if (fact.icon_class && fact.icon_class !== null && fact.icon_class !== '') {
                                withIcons++;
                            }
                        });
                    }
                    
                    $('#totalFacts').text(total);
                    $('#activeFacts').text(active);
                    $('#inactiveFacts').text(total - active);
                    $('#factsWithIcons').text(withIcons);
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
