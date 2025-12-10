@extends('admin.layouts.app')

@section('title', 'Kelola Link Menu')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-link me-2"></i>Link Menu: {{ $parent->name }}
          </h4>
          <p class="text-muted mb-0">Kelola link untuk menu "{{ $parent->name }}" ({{ $parent->location }})</p>
        </div>
        <div>
          <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Menu
          </a>
          <a href="{{ route('admin.links.create', $parent->id) }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Link
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
              <h6 class="card-title">Total Link</h6>
              <h3 class="mb-0" id="totalLinks">0</h3>
            </div>
            <i class="bx bx-link bx-lg"></i>
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
              <h3 class="mb-0" id="activeLinks">0</h3>
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
              <h3 class="mb-0" id="inactiveLinks">0</h3>
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
              <h6 class="card-title">Lokasi Menu</h6>
              <h3 class="mb-0">{{ $parent->location }}</h3>
            </div>
            <i class="bx bx-location-pin bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari link berdasarkan label atau URL...">
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

  <!-- Links Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Link Menu
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="links-table">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th width="8%"><i class="fas fa-arrows-alt"></i> Urutan</th>
            <th width="20%">Label</th>
            <th width="25%">URL</th>
            <th width="10%">Parent</th>
            <th width="9%">Status</th>
            <th width="8%">Aksi</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Link</h6>
          <ul class="mb-3">
            <li>Ketik label atau URL link di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan status (Aktif/Non-Aktif)</li>
            <li>Hasil akan muncul secara otomatis</li>
          </ul>
          
          <h6><i class="fas fa-arrows-alt text-success me-2"></i>Cara Mengatur Urutan (Drag & Drop)</h6>
          <ul class="mb-3">
            <li>Klik dan tahan ikon <i class="fas fa-arrows-alt"></i> pada kolom urutan</li>
            <li>Seret (drag) link ke posisi yang diinginkan</li>
            <li>Lepaskan (drop) untuk mengatur ulang urutan</li>
            <li>Urutan akan tersimpan secara otomatis</li>
            <li>Halaman akan refresh untuk menampilkan urutan baru</li>
          </ul>
          
          <h6><i class="bx bx-edit text-warning me-2"></i>Cara Mengedit Link</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada link yang ingin diubah</li>
            <li>Ubah label, URL, atau target link</li>
            <li>Atur ulang parent atau urutan tampilan</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Menambah Link Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Link</span></li>
            <li>Isi label link yang akan ditampilkan</li>
            <li>Masukkan URL atau pilih route yang tersedia</li>
            <li>Pilih target (_self, _blank, dll)</li>
            <li>Atur parent link untuk submenu</li>
            <li>Tentukan urutan tampilan</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>URL Management:</strong> External dan internal links</li>
            <li><strong>Route Support:</strong> Link ke route Laravel</li>
            <li><strong>Target Control:</strong> _self, _blank, dll</li>
            <li><strong>Parent System:</strong> Submenu support</li>
            <li><strong>Drag & Drop:</strong> Atur urutan dengan menyeret link</li>
            <li><strong>Order Management:</strong> Atur urutan tampilan otomatis</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.drag-handle {
    cursor: move !important;
    color: #6c757d;
    font-size: 14px;
}

.drag-handle:hover {
    color: #0d6efd !important;
}

.sortable-ghost {
    opacity: 0.4;
    background: #f8f9fa;
}

.sortable-drag {
    opacity: 0.9;
}

/* Smaller drag icon */
.drag-handle .fa-arrows-alt {
    font-size: 12px !important;
}

/* Smaller order numbers */
.drag-handle span {
    font-size: 13px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#links-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.links.data", $parent->id) }}',
                error: function(xhr, error, code) {
                    console.error('DataTable error:', xhr, error, code);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memuat data. Silakan refresh halaman.',
                        confirmButtonText: 'Refresh',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            },
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'drag_order', name: 'drag_order',  orderable: false, searchable: false },
                { data: 'label', name: 'label' },
                { data: 'url', name: 'url' },
                { data: 'parent_name', name: 'parent_name', className: 'text-center', orderable: false, searchable: false },
                { data: 'is_active', name: 'is_active', className: 'text-center', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', className: 'text-center', orderable: false, searchable: false }
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
                // Initialize drag and drop after table is loaded
                initializeSortable();
                // Update statistics on initial load
                updateStats();
            }
        });

        function initializeSortable() {
            var tbody = $('#links-table tbody')[0];
            new Sortable(tbody, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function(evt) {
                    var orders = [];
                    var parentOrder = 0;
                    
                    $('#links-table tbody tr').each(function(index) {
                        var row = table.row($(this)).data();
                        if (row) {
                            if (!row.parent_id) {
                                // Parent item - increment parent order as string
                                parentOrder++;
                                orders.push({
                                    id: row.id,
                                    sort_order: parentOrder.toString()
                                });
                            } else {
                                // Child item - get parent's order and add child index as string
                                var childIndex = $(this).prevAll('tr').filter(function() {
                                    return table.row($(this)).data().parent_id == row.parent_id;
                                }).length + 1;
                                
                                orders.push({
                                    id: row.id,
                                    sort_order: childIndex.toString()
                                });
                            }
                        }
                    });

                    // Update orders via AJAX
                    $.ajax({
                        url: '{{ route("admin.links.order") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            orders: orders
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                // Reload table to show updated order
                                table.ajax.reload();
                                // Update statistics after reordering
                                updateStats();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal memperbarui urutan.',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        }

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
        $(document).on('click', '.delete-link', function(e) {
            e.preventDefault();
            var linkId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Link?',
                text: "Link ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.links.destroy", ":id") }}'.replace(':id', linkId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Link berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus link.',
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
                url: '{{ route("admin.links.data", $parent->id) }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000 },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var inactive = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(link) {
                            // Check if the is_active column contains "Aktif" (HTML badge)
                            if (link.is_active && link.is_active.includes('Aktif')) {
                                active++;
                            } else {
                                inactive++;
                            }
                        });
                    }
                    
                    $('#totalLinks').text(total);
                    $('#activeLinks').text(active);
                    $('#inactiveLinks').text(inactive);
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
