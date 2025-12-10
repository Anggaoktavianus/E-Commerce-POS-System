@extends('admin.layouts.app')

@section('title','Pengaturan Website')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-cog me-2"></i>Pengaturan Website
          </h4>
          <p class="text-muted mb-0">Kelola semua tampilan dan teks website Anda dari satu tempat</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.settings.logo') }}" class="btn btn-info">
            <i class="bx bx-image me-1"></i> Kelola Logo
          </a>
          <a href="{{ route('admin.settings.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Pengaturan
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
              <h6 class="card-title">Total Pengaturan</h6>
              <h3 class="mb-0">95+</h3>
            </div>
            <i class="bx bx-cog bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Homepage</h6>
              <h3 class="mb-0">25+</h3>
            </div>
            <i class="bx bx-home bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Produk</h6>
              <h3 class="mb-0">20+</h3>
            </div>
            <i class="bx bx-package bx-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title">Kontak</h6>
              <h3 class="mb-0">15+</h3>
            </div>
            <i class="bx bx-phone bx-lg"></i>
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
            <input type="text" id="searchInput" class="form-control" placeholder="Cari pengaturan (contoh: logo, homepage, produk, kontak...)">
          </div>
        </div>
        <div class="col-md-4">
          <select id="categoryFilter" class="form-select">
            <option value="">Semua Kategori</option>
            <option value="branding">Branding & Logo</option>
            <option value="homepage">Homepage</option>
            <option value="product">Produk & E-commerce</option>
            <option value="contact">Kontak & Form</option>
            <option value="navigation">Navigasi & Menu</option>
            <option value="footer">Footer</option>
            <option value="mitra">Mitra Dashboard</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Settings Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Pengaturan
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table id="settings-table" class="table table-striped table-hover w-100">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="20%">Kategori</th>
            <th width="25%">Pengaturan</th>
            <th width="30%">Nilai Saat Ini</th>
            <th width="15%">Status</th>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Pengaturan</h6>
          <ul class="mb-3">
            <li>Ketik kata kunci di kotak pencarian (contoh: "logo", "harga", "email")</li>
            <li>Gunakan filter kategori untuk mempersempit hasil</li>
            <li>Pencarian bersifat real-time</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengubah Nilai</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada pengaturan yang ingin diubah</li>
            <li>Isi nilai baru sesuai kebutuhan</li>
            <li>Klik tombol <span class="badge bg-success">Save</span> untuk menyimpan</li>
            <li>Perubahan akan langsung terlihat di website</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-category text-info me-2"></i>Kategori Pengaturan</h6>
          <ul class="mb-3">
            <li><strong>Branding & Logo:</strong> Nama perusahaan, logo, tagline</li>
            <li><strong>Homepage:</strong> Judul section, subtitle, tombol</li>
            <li><strong>Produk:</strong> Label harga, tombol cart, deskripsi</li>
            <li><strong>Kontak:</strong> Form kontak, alamat, telepon</li>
            <li><strong>Navigasi:</strong> Menu, tombol login/register</li>
            <li><strong>Footer:</strong> Teks footer, hak cipta, link</li>
            <li><strong>Mitra:</strong> Dashboard mitra, status pesanan</li>
          </ul>
          
          <h6><i class="bx bx-error text-warning me-2"></i>Tips Penting</h6>
          <ul class="mb-3">
            <li>Perubahan langsung terlihat di website</li>
            <li>Hati-hati saat mengubah pengaturan penting</li>
            <li>Backup data sebelum perubahan besar</li>
            <li>Gunakan preview untuk melihat perubahan</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(function(){
    // Initialize DataTable
    const table = $('#settings-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '{{ route('admin.settings.data') }}',
        data: function(d) {
          // Add category filter to request
          d.category_filter = $('#categoryFilter').val();
          d.search_filter = $('#searchInput').val();
        }
      },
      order: [[0,'desc']],
      columns: [
        {data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false},
        {data:'category', name:'category', orderable:false, searchable:false},
        {data:'key_display', name:'key'},
        {data:'value_display', name:'value'},
        {data:'status', name:'status', orderable:false, searchable:false},
        {data:'actions', name:'actions', orderable:false, searchable:false},
      ],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        paginate: {
          first: "Pertama",
          last: "Terakhir", 
          next: "Selanjutnya",
          previous: "Sebelumnya"
        },
        emptyTable: "Tidak ada data pengaturan",
        zeroRecords: "Tidak ada pengaturan yang cocok ditemukan"
      },
      initComplete: function() {
        // Hide default search input since we have custom search
        $('#settings-table_filter').hide();
      }
    });

    // Custom search with delay
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
      clearTimeout(searchTimeout);
      const searchValue = $(this).val();
      
      searchTimeout = setTimeout(function() {
        table.search(searchValue).draw();
      }, 300); // 300ms delay
    });

    // Category filter
    $('#categoryFilter').on('change', function() {
      table.draw(); // This will trigger ajax with new category filter
    });

    // Handle delete with SweetAlert
    $(document).on('click', '.delete-setting', function(e) {
      e.preventDefault();
      var deleteUrl = $(this).data('id');
      
      Swal.fire({
        title: 'Hapus Pengaturan?',
        text: "Pengaturan yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: deleteUrl,
            type: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              Swal.fire(
                'Terhapus!',
                'Pengaturan berhasil dihapus.',
                'success'
              );
              table.ajax.reload();
            },
            error: function(xhr) {
              Swal.fire(
                'Error!',
                'Terjadi kesalahan saat menghapus pengaturan.',
                'error'
              );
            }
          });
        }
      });
    });
  });
</script>
@endpush
