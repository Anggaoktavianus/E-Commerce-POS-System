@extends('admin.layouts.app')

@section('title', 'Kelola Produk')

@section('content')
@php
  $currentStoreId = app()->has('current_store') ? (int)app('current_store')->id : 1;
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-package me-2 text-primary"></i>Kelola Produk
          </h4>
          <p class="text-muted mb-0">Buat dan kelola produk katalog untuk ditampilkan di website</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
          <button type="button" id="btn-transfer-all" class="btn btn-danger btn-modern">
            <i class="bx bx-transfer-alt me-1"></i> Transfer Semua Produk
          </button>
          <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-modern">
            <i class="bx bx-plus me-1"></i> Tambah Produk
          </a>
        </div>
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
              <p class="stat-label mb-2">Total Produk</p>
              <h3 class="stat-value mb-0 text-white" id="totalProducts">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-package"></i>
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
              <h3 class="stat-value mb-0 text-white" id="activeProducts">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-show"></i>
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
              <p class="stat-label mb-2">Unggulan</p>
              <h3 class="stat-value mb-0 text-white" id="featuredProducts">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-star"></i>
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
              <p class="stat-label mb-2">Terlaris</p>
              <h3 class="stat-value mb-0 text-white" id="bestsellerProducts">0</h3>
            </div>
            <div class="stat-icon">
              <i class="bx bx-trending-up"></i>
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
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari produk berdasarkan nama atau slug...">
          </div>
        </div>
        <div class="col-md-2">
          <select id="filter-category" class="form-select">
            <option value="">— Semua Kategori —</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select id="filter-featured" class="form-select">
            <option value="">— Semua Status —</option>
            <option value="1">Unggulan</option>
            <option value="0">Non-Unggulan</option>
          </select>
        </div>
        <div class="col-md-3">
          <select id="filter-store" class="form-select">
            <option value="">— Store Saat Ini —</option>
            @foreach(($stores ?? []) as $s)
              <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button id="btn-clear-filters" class="btn btn-outline-secondary w-100">
            <i class="bx bx-x me-1"></i>Reset
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Products Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="bx bx-list-ul me-2"></i>Daftar Produk
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="products-table">
        <thead>
          <tr>
            <th width="4%">No</th>
            <th width="16%">Nama</th>
            <th width="12%">Kategori</th>
            <th width="8%">Harga</th>
            <th width="6%">Unit</th>
            <th width="8%">Stok</th>
            <th width="12%">Toko/Outlet</th>
            <th width="6%">Unggulan</th>
            <th width="6%">Terlaris</th>
            <th width="7%">Status</th>
            <th width="13%">Aksi</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

  <!-- Stock Adjustment Modal -->
  <div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adjustStockModalLabel">
            <i class="bx bx-adjust me-2"></i>Sesuaikan Stok
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="adjustStockForm">
          <div class="modal-body">
            <input type="hidden" id="adjust-product-id" name="product_id">
            <div class="mb-3">
              <label class="form-label">Produk</label>
              <input type="text" id="adjust-product-name" class="form-control" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Stok Saat Ini</label>
              <input type="text" id="adjust-current-stock" class="form-control" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Tipe Penyesuaian <span class="text-danger">*</span></label>
              <select name="adjustment_type" id="adjustment-type" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="set">Set Stok (Tentukan Jumlah)</option>
                <option value="increase">Tambah Stok</option>
                <option value="decrease">Kurangi Stok</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Jumlah <span class="text-danger">*</span></label>
              <input type="number" name="quantity" id="adjust-quantity" class="form-control" min="1" required>
              <small class="text-muted" id="adjust-hint"></small>
            </div>
            <div class="mb-3">
              <label class="form-label">Stok Baru (Preview)</label>
              <input type="text" id="adjust-new-stock" class="form-control bg-light" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea name="notes" id="adjust-notes" class="form-control" rows="3" placeholder="Contoh: Stok masuk dari supplier, Koreksi stok, dll"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-check me-2"></i>Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Transfer Product Modal -->
  <div class="modal fade" id="transferProductModal" tabindex="-1" aria-labelledby="transferProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="transferProductModalLabel">
            <i class="bx bx-transfer me-2"></i>Pindah/Salin Produk ke Toko Lain
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="transferProductForm">
          <div class="modal-body">
            <input type="hidden" id="transfer-product-id" name="product_id">
            <div class="mb-3">
              <label class="form-label">Produk</label>
              <input type="text" id="transfer-product-name" class="form-control" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Toko Asal</label>
              <input type="text" id="transfer-source-store" class="form-control" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Toko Tujuan <span class="text-danger">*</span></label>
              <select name="target_store_id" id="transfer-target-store" class="form-select" required>
                <option value="">-- Pilih Toko Tujuan --</option>
                @foreach($stores as $store)
                  <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->code ?? 'N/A' }})</option>
                @endforeach
              </select>
              <small class="text-muted">Pilih toko tujuan untuk memindahkan atau menyalin produk</small>
            </div>
            <div class="mb-3">
              <label class="form-label">Tipe Transfer <span class="text-danger">*</span></label>
              <select name="transfer_type" id="transfer-type" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="move">Pindah (Produk akan dipindahkan ke toko baru)</option>
                <option value="copy">Salin (Produk akan disalin, produk asli tetap di toko ini)</option>
              </select>
              <small class="text-muted">
                <strong>Pindah:</strong> Produk akan dipindahkan ke toko tujuan, tidak lagi ada di toko asal.<br>
                <strong>Salin:</strong> Produk akan disalin ke toko tujuan, produk asli tetap ada di toko asal.
              </small>
            </div>
            <div class="mb-3">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea name="notes" id="transfer-notes" class="form-control" rows="3" placeholder="Contoh: Transfer untuk ekspansi, Salin untuk cabang baru, dll"></textarea>
            </div>
            <div class="alert alert-warning mb-0">
              <i class="bx bx-info-circle me-2"></i>
              <strong>Perhatian:</strong> Pastikan SKU produk unik di toko tujuan. Jika SKU sudah digunakan, transfer akan gagal.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning">
              <i class="bx bx-transfer me-2"></i>Proses Transfer
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Batch Transfer Modal -->
  <div class="modal fade" id="batchTransferModal" tabindex="-1" aria-labelledby="batchTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="batchTransferModalLabel">
            <i class="bx bx-transfer me-2"></i>Transfer Batch Produk
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="batchTransferForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Jumlah Produk Terpilih</label>
              <input type="text" id="batch-selected-count" class="form-control" readonly>
              <input type="hidden" id="batch-transfer-all" name="transfer_all" value="0">
              <div id="batch-product-ids-container"></div>
            </div>
            <div class="mb-3">
              <label class="form-label">Toko Tujuan <span class="text-danger">*</span></label>
              <select name="target_store_id" id="batch-target-store" class="form-select" required>
                <option value="">-- Pilih Toko Tujuan --</option>
                @foreach($stores as $store)
                  <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->code ?? 'N/A' }})</option>
                @endforeach
              </select>
              <small class="text-muted">Pilih toko tujuan untuk memindahkan atau menyalin produk</small>
            </div>
            <div class="mb-3">
              <label class="form-label">Tipe Transfer <span class="text-danger">*</span></label>
              <select name="transfer_type" id="batch-transfer-type" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="move">Pindah (Produk akan dipindahkan ke toko baru)</option>
                <option value="copy">Salin (Produk akan disalin, produk asli tetap di toko ini)</option>
              </select>
              <small class="text-muted">
                <strong>Pindah:</strong> Produk akan dipindahkan ke toko tujuan, tidak lagi ada di toko asal.<br>
                <strong>Salin:</strong> Produk akan disalin ke toko tujuan, produk asli tetap ada di toko asal.
              </small>
            </div>
            <div class="mb-3">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea name="notes" id="batch-transfer-notes" class="form-control" rows="3" placeholder="Contoh: Transfer batch untuk ekspansi, Salin produk untuk cabang baru, dll"></textarea>
            </div>
            <div class="alert alert-warning mb-0">
              <i class="bx bx-info-circle me-2"></i>
              <strong>Perhatian:</strong> 
              <ul class="mb-0 mt-2">
                <li>Pastikan SKU produk unik di toko tujuan</li>
                <li>Produk dengan SKU duplikat akan dilewati</li>
                <li>Hasil transfer akan ditampilkan setelah proses selesai</li>
              </ul>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning">
              <i class="bx bx-transfer me-2"></i>Proses Transfer Batch
            </button>
          </div>
        </form>
      </div>
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
          <h6><i class="bx bx-search text-primary me-2"></i>Cara Mencari Produk</h6>
          <ul class="mb-3">
            <li>Ketik nama produk di kotak pencarian</li>
            <li>Pencarian bersifat real-time</li>
            <li>Filter berdasarkan kategori produk</li>
            <li>Filter berdasarkan status unggulan</li>
            <li>Gunakan tombol Reset untuk menghapus filter</li>
          </ul>
          
          <h6><i class="bx bx-edit text-success me-2"></i>Cara Mengedit Produk</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Edit</span> pada produk yang ingin diubah</li>
            <li>Ubah nama, deskripsi, atau spesifikasi produk</li>
            <li>Atur harga dan unit produk</li>
            <li>Pilih kategori yang sesuai</li>
            <li>Atur status unggulan atau terlaris</li>
            <li>Klik <span class="badge bg-success">Simpan</span> untuk menyimpan perubahan</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6><i class="bx bx-plus text-info me-2"></i>Cara Membuat Produk Baru</h6>
          <ul class="mb-3">
            <li>Klik tombol <span class="badge bg-primary">Tambah Produk</span></li>
            <li>Isi nama produk yang deskriptif (wajib)</li>
            <li>Masukkan slug unik untuk URL</li>
            <li>Atur harga dan unit (pcs, kg, dll)</li>
            <li>Pilih kategori yang relevan</li>
            <li>Upload gambar produk berkualitas</li>
            <li>Atur status aktif/non-aktif</li>
          </ul>
          
          <h6><i class="bx bx-cog text-warning me-2"></i>Fitur Tersedia</h6>
          <ul class="mb-3">
            <li><strong>Category Management:</strong> Multi-kategori per produk</li>
            <li><strong>Featured Products:</strong> Tandai produk unggulan</li>
            <li><strong>Bestseller:</strong> Tandai produk terlaris</li>
            <li><strong>Price Control:</strong> Atur harga dan unit</li>
            <li><strong>Image Gallery:</strong> Multiple gambar per produk</li>
            <li><strong>SEO Friendly:</strong> Slug dan meta tags</li>
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
        // Check if jQuery and required libraries are loaded
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded!');
            alert('jQuery tidak dimuat. Silakan refresh halaman.');
            return;
        }
        
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables is not loaded!');
            alert('DataTables tidak dimuat. Silakan refresh halaman.');
            return;
        }
        
        console.log('JavaScript initialized successfully');
        
        // Handle transfer all button click
        $('#btn-transfer-all').on('click', function() {
            Swal.fire({
                title: 'Transfer Semua Produk?',
                text: 'Apakah Anda yakin ingin transfer SEMUA produk di toko ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Transfer Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    openBatchTransferModal();
                }
            });
        });

        // Function to open batch transfer modal
        function openBatchTransferModal() {
            // Get total products count from current store
            $.ajax({
                url: '{{ route("admin.products.data") }}',
                type: 'GET',
                data: { 
                    draw: 1, 
                    start: 0, 
                    length: 1,
                    store_id: $('#filter-store').val() || {!! json_encode($currentStoreId ?? 1) !!}
                },
                success: function(data) {
                    var totalProducts = data.recordsTotal || 0;
                    
                    $('#batch-transfer-all').val('1');
                    $('#batch-product-ids-container').empty();
                    $('#batch-selected-count').val('Semua produk di toko ini (' + totalProducts + ' produk)');
                    
                    setupBatchTransferModal();
                }
            });
        }

        // Function to setup batch transfer modal
        function setupBatchTransferModal() {
            $('#batch-target-store').val('');
            $('#batch-transfer-type').val('');
            $('#batch-transfer-notes').val('');

            // Get current store ID
            var currentStoreId = $('#filter-store').val() || {!! json_encode($currentStoreId ?? 1) !!};
            
            // Exclude current store from target store options
            $('#batch-target-store option').each(function() {
                if ($(this).val() == currentStoreId) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });

            $('#batchTransferModal').modal('show');
        }

        // Handle batch transfer form submit
        $('#batchTransferForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var transferAll = $('#batch-transfer-all').val() === '1';
            
            var transferType = $('#batch-transfer-type').val();
            var targetStoreName = $('#batch-target-store option:selected').text();
            
            // Confirmation message
            var targetStoreNameEscaped = targetStoreName.replace(/'/g, "\\'").replace(/"/g, '\\"');
            var confirmMessage = transferType === 'move' 
                ? 'Apakah Anda yakin ingin MEMINDAHKAN SEMUA produk ke toko "' + targetStoreNameEscaped + '"?\n\nProduk akan dipindahkan dan tidak lagi tersedia di toko asal.'
                : 'Apakah Anda yakin ingin MENYALIN SEMUA produk ke toko "' + targetStoreNameEscaped + '"?\n\nProduk akan disalin dan tetap tersedia di toko asal.';
            
            confirmMessage += '\\n\\n⚠️ PERINGATAN: Ini akan memproses SEMUA produk di toko ini!';
            
            // Close Bootstrap modal first
            $('#batchTransferModal').modal('hide');
            
            // Wait for modal to fully close before showing SweetAlert
            $('#batchTransferModal').one('hidden.bs.modal', function() {
                Swal.fire({
                    title: transferType === 'move' ? 'Pindah Batch Produk?' : 'Salin Batch Produk?',
                    text: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, ' + (transferType === 'move' ? 'Pindahkan' : 'Salin') + '!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu, sedang memproses SEMUA produk di toko ini...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit form normally - product_ids[] will be sent as array (or transfer_all=1)
                        var formData = form.serialize();
                        
                        $.ajax({
                            url: '{{ route("admin.products.batch_transfer") }}',
                            type: 'POST',
                            data: formData + '&_token=' + encodeURIComponent('{{ csrf_token() }}'),
                            success: function(response) {
                                var details = response.details || {};
                                var detailMessage = response.message;
                                
                                if (details.skipped_products && details.skipped_products.length > 0) {
                                    detailMessage += '\n\nProduk yang dilewati:\n';
                                    details.skipped_products.forEach(function(p) {
                                        detailMessage += '- ' + p.name + ': ' + p.reason + '\n';
                                    });
                                }
                                
                                if (details.failed_products && details.failed_products.length > 0) {
                                    detailMessage += '\n\nProduk yang gagal:\n';
                                    details.failed_products.forEach(function(p) {
                                        detailMessage += '- ' + p.name + ': ' + p.error + '\n';
                                    });
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    html: '<div style="text-align: left; white-space: pre-line;">' + detailMessage + '</div>',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6',
                                    width: '600px'
                                });
                                
                                // Reload table
                                table.ajax.reload();
                                updateStats();
                            },
                            error: function(xhr) {
                                var message = 'Terjadi kesalahan saat transfer batch.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: message,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });
        });

        // Initialize DataTable
        try {
            var table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.products.data") }}',
                data: function(d){
                    d.category_id = $('#filter-category').val();
                    d.featured = $('#filter-featured').val();
                    d.store_id = $('#filter-store').val();
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
                { data: 'categories', name: 'categories', orderable: false, searchable: false },
                { data: 'price', name: 'price' },
                { data: 'unit', name: 'unit' },
                { data: 'stock_qty', name: 'stock_qty', orderable: true, searchable: false },
                { data: 'store_name', name: 'store_name', orderable: false, searchable: false },
                { data: 'is_featured', name: 'is_featured', orderable: false, searchable: false },
                { data: 'is_bestseller', name: 'is_bestseller', orderable: false, searchable: false },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
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
                $('#products-table_filter').hide();
                
                // Update stats from initial data
                updateStats();
            }
        });
        console.log('DataTable initialized successfully');
        } catch (error) {
            console.error('Error initializing DataTable:', error);
            console.error('Error stack:', error.stack);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memuat tabel: ' + error.message,
                    confirmButtonText: 'Refresh',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                alert('Error: ' + error.message + '\nSilakan refresh halaman.');
            }
        }

        // Custom search (using built-in DataTable search)
        $('#searchInput').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Category and featured filters
        $('#filter-category, #filter-featured, #filter-store').on('change', function(){
            table.ajax.reload();
        });

        // Clear filters
        $('#btn-clear-filters').on('click', function(e){
            e.preventDefault();
            $('#searchInput').val('');
            $('#filter-category').val('');
            $('#filter-featured').val('');
            $('#filter-store').val('');
            table.search('').draw();
            table.ajax.reload();
        });

        // Handle stock adjustment
        $(document).on('click', '.adjust-stock-btn', function(e) {
            e.preventDefault();
            var productId = $(this).data('id');
            var productName = $(this).data('name');
            var $btn = $(this);
            
            // Get current stock from data attribute (may be outdated, but we'll fetch latest)
            var currentStock = parseInt($btn.data('current-stock')) || 0;
            
            // Set initial values
            $('#adjust-product-id').val(productId);
            $('#adjust-product-name').val(productName);
            $('#adjust-current-stock').val(currentStock); // Set initial value
            $('#adjustment-type').val('');
            $('#adjust-quantity').val('');
            $('#adjust-new-stock').val('');
            $('#adjust-notes').val('');
            $('#adjust-hint').text('');
            
            // Fetch latest stock from database to ensure accuracy
            // This ensures we always get the most up-to-date stock value
            $.ajax({
                url: '{{ route("admin.products.stock_info", ":id") }}'.replace(':id', productId),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response && response.stock_qty !== undefined) {
                        var latestStock = parseInt(response.stock_qty) || 0;
                        $('#adjust-current-stock').val(latestStock);
                        // Update data attribute for future use
                        $btn.data('current-stock', latestStock);
                    }
                },
                error: function() {
                    // If fetch fails, use the data attribute value (fallback)
                    console.warn('Failed to fetch latest stock, using cached value: ' + currentStock);
                }
            });
            
            $('#adjustStockModal').modal('show');
        });

        // Calculate new stock preview
        $('#adjustment-type, #adjust-quantity').on('change keyup', function() {
            var type = $('#adjustment-type').val();
            var quantity = parseInt($('#adjust-quantity').val()) || 0;
            var currentStock = parseInt($('#adjust-current-stock').val()) || 0;
            var newStock = currentStock;
            var hint = '';

            if (type && quantity > 0) {
                switch(type) {
                    case 'set':
                        newStock = quantity;
                        hint = 'Stok akan diatur menjadi ' + quantity;
                        break;
                    case 'increase':
                        newStock = currentStock + quantity;
                        hint = 'Stok akan ditambah ' + quantity + ' menjadi ' + newStock;
                        break;
                    case 'decrease':
                        newStock = Math.max(0, currentStock - quantity);
                        hint = 'Stok akan dikurangi ' + quantity + ' menjadi ' + newStock;
                        if (newStock === 0 && currentStock - quantity < 0) {
                            hint += ' (akan menjadi 0, tidak bisa negatif)';
                        }
                        break;
                }
                $('#adjust-new-stock').val(newStock);
                $('#adjust-hint').text(hint).removeClass('text-danger text-success').addClass('text-info');
            } else {
                $('#adjust-new-stock').val('');
                $('#adjust-hint').text('');
            }
        });

        // Handle stock adjustment form submit
        $('#adjustStockForm').on('submit', function(e) {
            e.preventDefault();
            var productId = $('#adjust-product-id').val();
            var formData = $(this).serialize();
            
            $.ajax({
                url: '{{ route("admin.products.adjust_stock", ":id") }}'.replace(':id', productId),
                type: 'POST',
                data: formData + '&_token=' + encodeURIComponent('{{ csrf_token() }}'),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message + ' (Stok: ' + response.old_stock + ' → ' + response.new_stock + ')',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    $('#adjustStockModal').modal('hide');
                    
                    // Update data attribute with new stock value immediately
                    var productId = $('#adjust-product-id').val();
                    $('.adjust-stock-btn[data-id="' + productId + '"]').data('current-stock', response.new_stock);
                    
                    // Reload table to refresh all data
                    table.ajax.reload(null, false); // false = don't reset paging
                    updateStats();
                },
                error: function(xhr) {
                    var message = 'Terjadi kesalahan saat menyesuaikan stok.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });

        // Handle transfer product button click
        $(document).on('click', '.transfer-product-btn', function(e) {
            e.preventDefault();
            var productId = $(this).data('id');
            var productName = $(this).data('name');
            var storeId = $(this).data('store-id');
            
            // Get store name
            var storeName = '';
            try {
                var stores = {!! json_encode($stores ?? []) !!};
                if (stores && Array.isArray(stores)) {
                    stores.forEach(function(store) {
                        if (store && store.id == storeId) {
                            storeName = store.name || '';
                        }
                    });
                }
            } catch(e) {
                console.error('Error getting store name:', e);
            }
            
            $('#transfer-product-id').val(productId);
            $('#transfer-product-name').val(productName);
            $('#transfer-source-store').val(storeName || 'Toko ID: ' + storeId);
            $('#transfer-target-store').val('');
            $('#transfer-type').val('');
            $('#transfer-notes').val('');
            
            // Exclude current store from target store options
            $('#transfer-target-store option').each(function() {
                if ($(this).val() == storeId) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
            
            $('#transferProductModal').modal('show');
        });

        // Handle transfer product form submit
        $('#transferProductForm').on('submit', function(e) {
            e.preventDefault();
            var productId = $('#transfer-product-id').val();
            var formData = $(this).serialize();
            var transferType = $('#transfer-type').val();
            var targetStoreName = $('#transfer-target-store option:selected').text();
            
            // Confirmation message
            var targetStoreNameEscaped = targetStoreName.replace(/'/g, "\\'").replace(/"/g, '\\"');
            var confirmMessage = transferType === 'move'
                ? 'Apakah Anda yakin ingin MEMINDAHKAN produk ini ke toko "' + targetStoreNameEscaped + '"?\n\nProduk akan dipindahkan dan tidak lagi tersedia di toko asal.'
                : 'Apakah Anda yakin ingin MENYALIN produk ini ke toko "' + targetStoreNameEscaped + '"?\n\nProduk akan disalin dan tetap tersedia di toko asal.';
            
            Swal.fire({
                title: transferType === 'move' ? 'Pindah Produk?' : 'Salin Produk?',
                text: confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, ' + (transferType === 'move' ? 'Pindahkan' : 'Salin') + '!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    $.ajax({
                        url: '{{ route("admin.products.transfer", ":id") }}'.replace(':id', productId),
                        type: 'POST',
                        data: formData + '&_token=' + encodeURIComponent('{{ csrf_token() }}'),
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                            $('#transferProductModal').modal('hide');
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            var message = 'Terjadi kesalahan saat memindahkan produk.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: message,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.delete-product', function(e) {
            e.preventDefault();
            var productId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Produk ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.products.destroy", ":id") }}'.replace(':id', productId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Produk berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                            updateStats();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus produk.',
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
                url: '{{ route("admin.products.data") }}',
                type: 'GET',
                data: { draw: 1, start: 0, length: 1000, store_id: $('#filter-store').val() },
                success: function(data) {
                    var total = data.recordsTotal || 0;
                    var active = 0;
                    var featured = 0;
                    var bestseller = 0;
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(function(product) {
                            if (product.is_active == '1' || product.is_active == true) {
                                active++;
                            }
                            if (product.is_featured && product.is_featured !== '') {
                                featured++;
                            }
                            if (product.is_bestseller && product.is_bestseller !== '') {
                                bestseller++;
                            }
                        });
                    }
                    
                    $('#totalProducts').text(total);
                    $('#activeProducts').text(active);
                    $('#featuredProducts').text(featured);
                    $('#bestsellerProducts').text(bestseller);
                }
            });
        }

        // Success message from session
        @if(session('success'))
        try {
            var successMessage = {!! json_encode(session('success'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) !!};
            if (successMessage && typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: String(successMessage),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            }
        } catch(e) {
            console.error('Error showing success message:', e);
        }
        @endif

        @if(session('error'))
        try {
            var errorMessage = {!! json_encode(session('error'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) !!};
            if (errorMessage && typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: String(errorMessage),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            }
        } catch(e) {
            console.error('Error showing error message:', e);
        }
        @endif
    });
</script>
@endpush
